<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PuntoEncuentro extends Model
{
    protected $table = 'puntos_encuentro';

    protected $fillable = [
        'nombre',
        'direccion',
        'descripcion',
        'activo',
        'orden',
        'horario_semanal',
    ];

    protected $casts = [
        'activo'         => 'boolean',
        'horario_semanal'=> 'array',
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'punto_encuentro_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HORARIO (helpers)
    |--------------------------------------------------------------------------
    */

    private static function normTime($v): ?string
    {
        if ($v === null) return null;
        $s = trim((string)$v);
        if ($s === '') return null;

        // Acepta "9:30" o "09:30"
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $s, $m)) {
            $hh = (int)$m[1];
            $mm = (int)$m[2];
            if ($hh < 0 || $hh > 23 || $mm < 0 || $mm > 59) return null;
            return str_pad((string)$hh, 2, '0', STR_PAD_LEFT) . ':' . $m[2];
        }

        // Acepta "09:30:00"
        if (preg_match('/^(\d{2}):(\d{2}):\d{2}$/', $s, $m)) {
            $hh = (int)$m[1];
            $mm = (int)$m[2];
            if ($hh < 0 || $hh > 23 || $mm < 0 || $mm > 59) return null;
            return $m[1] . ':' . $m[2];
        }

        return null;
    }

    /**
     * Devuelve rangos normalizados y ordenados para un día.
     * Formato: [['from'=>'09:30','to'=>'13:30'], ...]
     */
    public function getRangosDia(string $dayKey): array
    {
        $horario = $this->horario_semanal ?? [];
        if (!is_array($horario)) return [];

        $rangos = $horario[$dayKey] ?? [];
        if (!is_array($rangos)) return [];

        $out = [];
        foreach ($rangos as $r) {
            if (!is_array($r)) continue;

            $from = self::normTime($r['from'] ?? null);
            $to   = self::normTime($r['to'] ?? null);

            if (!$from || !$to) continue;
            if ($from >= $to) continue;

            $out[] = ['from' => $from, 'to' => $to];
        }

        usort($out, fn($a,$b) => strcmp($a['from'], $b['from']));
        return $out;
    }

    public function tieneHorario(): bool
    {
        $horario = $this->horario_semanal ?? [];
        if (!is_array($horario) || empty($horario)) return false;

        foreach (['mon','tue','wed','thu','fri','sat','sun'] as $d) {
            if (count($this->getRangosDia($d)) > 0) return true;
        }
        return false;
    }

    /**
     * Verifica si el punto está disponible en una fecha/hora específica.
     * - múltiples rangos por día
     * - regla: [from, to) (incluye inicio, excluye término)
     */
    public function estaDisponibleEn(Carbon $fechaHora): bool
    {
        if (!$this->activo) return false;
        if (!$this->tieneHorario()) return false;

        $map = [
            1 => 'mon',
            2 => 'tue',
            3 => 'wed',
            4 => 'thu',
            5 => 'fri',
            6 => 'sat',
            7 => 'sun',
        ];

        $dayKey = $map[$fechaHora->dayOfWeekIso] ?? null;
        if (!$dayKey) return false;

        $rangos = $this->getRangosDia($dayKey);
        if (!$rangos) return false;

        $hora = $fechaHora->format('H:i');

        foreach ($rangos as $r) {
            if ($hora >= $r['from'] && $hora < $r['to']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Próxima hora válida dentro del mismo día según horario.
     * - Si ya está dentro, devuelve la misma fecha/hora.
     * - Si está en "almuerzo"/entre rangos, salta al inicio del siguiente rango.
     * - Si está fuera (antes de todo o después de todo), devuelve el inicio del primer rango.
     * Retorna null si el día no tiene rangos.
     */
    public function proximaHoraValidaEnMismoDia(Carbon $fechaHora): ?Carbon
    {
        $map = [
            1 => 'mon',
            2 => 'tue',
            3 => 'wed',
            4 => 'thu',
            5 => 'fri',
            6 => 'sat',
            7 => 'sun',
        ];

        $dayKey = $map[$fechaHora->dayOfWeekIso] ?? null;
        if (!$dayKey) return null;

        $rangos = $this->getRangosDia($dayKey);
        if (!$rangos) return null;

        $hora = $fechaHora->format('H:i');

        // ya está dentro
        foreach ($rangos as $r) {
            if ($hora >= $r['from'] && $hora < $r['to']) {
                return $fechaHora->copy();
            }
        }

        // buscar el siguiente rango
        foreach ($rangos as $r) {
            if ($hora < $r['from']) {
                return $fechaHora->copy()->setTimeFromTimeString($r['from']);
            }
        }

        // si está después de todo, usar primer rango
        return $fechaHora->copy()->setTimeFromTimeString($rangos[0]['from']);
    }

    /**
     * Devuelve el horario formateado para mostrar en UI.
     * Ej:
     * Lun 09:30-13:30, 14:30-20:30 | Mar 15:00-19:00
     */
    public function getHorarioTextoAttribute(): string
    {
        $labels = [
            'mon' => 'Lun',
            'tue' => 'Mar',
            'wed' => 'Mié',
            'thu' => 'Jue',
            'fri' => 'Vie',
            'sat' => 'Sáb',
            'sun' => 'Dom',
        ];

        $dayOrder = ['mon','tue','wed','thu','fri','sat','sun'];
        $parts = [];

        foreach ($dayOrder as $day) {
            $rangos = $this->getRangosDia($day);
            if (!$rangos) continue;

            $rangeParts = array_map(fn($r) => $r['from'] . '-' . $r['to'], $rangos);
            $parts[] = ($labels[$day] ?? strtoupper($day)) . ' ' . implode(', ', $rangeParts);
        }

        return $parts ? implode(' | ', $parts) : 'Sin horario definido';
    }
}