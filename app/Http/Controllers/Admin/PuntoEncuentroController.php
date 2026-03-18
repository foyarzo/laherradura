<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PuntoEncuentro;

class PuntoEncuentroController extends Controller
{
    public function index()
    {
        $puntos = PuntoEncuentro::query()
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get();

        return view('admin.puntos.home', compact('puntos'));
    }

    /**
     * Normaliza el horario semanal recibido desde el formulario.
     *
     * Espera:
     * horario[thu][from], horario[thu][to]
     * horario[thu][from2], horario[thu][to2] (opcional, ej: almuerzo)
     *
     * Devuelve:
     * [
     *   'thu' => [
     *      ['from'=>'09:30','to'=>'13:30'],
     *      ['from'=>'14:30','to'=>'20:30'],
     *   ],
     *   ...
     * ]
     */
    private function normalizeHorarioSemanal(array $input): array
    {
        $allowedDays = ['mon','tue','wed','thu','fri','sat','sun'];
        $out = [];

        foreach ($allowedDays as $day) {
            if (!isset($input[$day]) || !is_array($input[$day])) continue;

            $ranges = [];

            // Rango 1
            $from1 = isset($input[$day]['from']) ? trim((string)$input[$day]['from']) : '';
            $to1   = isset($input[$day]['to'])   ? trim((string)$input[$day]['to'])   : '';
            if ($from1 !== '' && $to1 !== '' && $from1 < $to1) {
                $ranges[] = ['from' => $from1, 'to' => $to1];
            }

            // Rango 2 (opcional) para almuerzo
            $from2 = isset($input[$day]['from2']) ? trim((string)$input[$day]['from2']) : '';
            $to2   = isset($input[$day]['to2'])   ? trim((string)$input[$day]['to2'])   : '';
            if ($from2 !== '' && $to2 !== '' && $from2 < $to2) {
                $ranges[] = ['from' => $from2, 'to' => $to2];
            }

            // Si no hay rangos válidos, no guardamos el día
            if (!$ranges) continue;

            // Ordenar por hora inicio
            usort($ranges, fn ($a, $b) => strcmp($a['from'], $b['from']));

            // Validación anti-solape: rango[i].to <= rango[i+1].from
            $valid = true;
            for ($i = 0; $i < count($ranges) - 1; $i++) {
                if ($ranges[$i]['to'] > $ranges[$i + 1]['from']) {
                    $valid = false;
                    break;
                }
            }
            if (!$valid) continue;

            $out[$day] = $ranges;
        }

        return $out;
    }

    private function hasAnyHorario(array $horarioSemanal): bool
    {
        foreach ($horarioSemanal as $ranges) {
            if (is_array($ranges) && count($ranges) > 0) return true;
        }
        return false;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'direccion'   => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'orden'       => ['nullable', 'integer', 'min:0', 'max:9999'],
            'activo'      => ['nullable', 'boolean'],

            'horario'            => ['required', 'array'],
            'horario.*'          => ['array'],
            'horario.*.from'     => ['nullable', 'date_format:H:i'],
            'horario.*.to'       => ['nullable', 'date_format:H:i'],
            'horario.*.from2'    => ['nullable', 'date_format:H:i'],
            'horario.*.to2'      => ['nullable', 'date_format:H:i'],
        ]);

        $horarioSemanal = $this->normalizeHorarioSemanal($data['horario'] ?? []);

        if (!$this->hasAnyHorario($horarioSemanal)) {
            return back()
                ->withErrors(['horario' => 'Debes definir al menos 1 día con horario válido. Si usas 2 rangos, no pueden solaparse.'])
                ->withInput();
        }

        PuntoEncuentro::create([
            'nombre'          => $data['nombre'],
            'direccion'       => $data['direccion'] ?? null,
            'descripcion'     => $data['descripcion'] ?? null,
            'orden'           => (int)($data['orden'] ?? 0),
            'activo'          => (bool)($data['activo'] ?? true),
            'horario_semanal' => $horarioSemanal,
        ]);

        return back()->with('success', 'Punto de encuentro creado.');
    }

    public function update(Request $request, PuntoEncuentro $punto)
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'direccion'   => ['nullable', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'orden'       => ['nullable', 'integer', 'min:0', 'max:9999'],
            'activo'      => ['nullable', 'boolean'],

            'horario'            => ['required', 'array'],
            'horario.*'          => ['array'],
            'horario.*.from'     => ['nullable', 'date_format:H:i'],
            'horario.*.to'       => ['nullable', 'date_format:H:i'],
            'horario.*.from2'    => ['nullable', 'date_format:H:i'],
            'horario.*.to2'      => ['nullable', 'date_format:H:i'],
        ]);

        $horarioSemanal = $this->normalizeHorarioSemanal($data['horario'] ?? []);

        if (!$this->hasAnyHorario($horarioSemanal)) {
            return back()
                ->withErrors(['horario' => 'Debes definir al menos 1 día con horario válido. Si usas 2 rangos, no pueden solaparse.'])
                ->withInput();
        }

        $punto->update([
            'nombre'          => $data['nombre'],
            'direccion'       => $data['direccion'] ?? null,
            'descripcion'     => $data['descripcion'] ?? null,
            'orden'           => (int)($data['orden'] ?? 0),
            'activo'          => (bool)($data['activo'] ?? false),
            'horario_semanal' => $horarioSemanal,
        ]);

        return back()->with('success', 'Punto de encuentro actualizado.');
    }

    public function toggle(PuntoEncuentro $punto)
    {
        $punto->update(['activo' => !$punto->activo]);
        return back()->with('success', 'Estado actualizado.');
    }

    public function destroy(PuntoEncuentro $punto)
    {
        $punto->delete();
        return back()->with('success', 'Punto eliminado.');
    }
}