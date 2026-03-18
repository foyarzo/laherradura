<?php

namespace App\Exports;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class PedidosDetalleSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithTitle
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function buildQuery()
    {
        $estadoMap = [
            'pendiente' => ['pendiente_aprobacion', 'pendiente'],
            'aceptado'  => ['aceptado', 'aprobado', 'confirmado'],
            'cancelado' => ['cancelado', 'rechazado', 'anulado'],
        ];

        $query = Pedido::query()->with('user');

        if ($this->request->filled('desde')) {
            $query->whereDate('created_at', '>=', $this->request->desde);
        }

        if ($this->request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $this->request->hasta);
        }

        if ($this->request->filled('estado')) {
            $key = $this->request->estado;
            $estados = $estadoMap[$key] ?? [$key];
            $query->whereIn('estado', $estados);
        }

        return $query;
    }

    public function collection()
    {
        $estadoLabels = [
            'pendiente_aprobacion' => 'Pendiente aprobación',
            'pendiente'            => 'Pendiente',
            'aceptado'             => 'Aceptado',
            'aprobado'             => 'Aprobado',
            'confirmado'           => 'Confirmado',
            'cancelado'            => 'Cancelado',
            'rechazado'            => 'Rechazado',
            'anulado'              => 'Anulado',
        ];

        return $this->buildQuery()
            ->latest()
            ->get()
            ->map(function ($p) use ($estadoLabels) {
                $estadoRaw = strtolower((string) ($p->estado ?? ''));
                $estado = $estadoLabels[$estadoRaw] ?? ucfirst(str_replace('_', ' ', $estadoRaw));

                return [
                    'ID' => $p->id,
                    'Cliente' => $p->cliente_nombre ?? ($p->user->name ?? '—'),
                    'Estado' => $estado,
                    'Total' => (int) ($p->total ?? 0),
                    'Fecha' => optional($p->created_at)->format('d-m-Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Cliente',
            'Estado',
            'Total',
            'Fecha',
        ];
    }

    public function title(): string
    {
        return 'Detalle';
    }
}