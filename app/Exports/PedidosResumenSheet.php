<?php

namespace App\Exports;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class PedidosResumenSheet implements FromArray, ShouldAutoSize, WithTitle, WithCharts
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function estadoMap(): array
    {
        return [
            'pendiente' => ['pendiente_aprobacion', 'pendiente'],
            'aceptado'  => ['aceptado', 'aprobado', 'confirmado'],
            'cancelado' => ['cancelado', 'rechazado', 'anulado'],
        ];
    }

    protected function estadoLabels(): array
    {
        return [
            'pendiente_aprobacion' => 'Pendiente aprobación',
            'pendiente'            => 'Pendiente',
            'aceptado'             => 'Aceptado',
            'aprobado'             => 'Aprobado',
            'confirmado'           => 'Confirmado',
            'cancelado'            => 'Cancelado',
            'rechazado'            => 'Rechazado',
            'anulado'              => 'Anulado',
        ];
    }

    protected function buildQuery()
    {
        $query = Pedido::query()->with('user');

        if ($this->request->filled('desde')) {
            $query->whereDate('created_at', '>=', $this->request->desde);
        }

        if ($this->request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $this->request->hasta);
        }

        if ($this->request->filled('estado')) {
            $key = $this->request->estado;
            $estados = $this->estadoMap()[$key] ?? [$key];
            $query->whereIn('estado', $estados);
        }

        return $query;
    }

    protected function resolveFechaDesde(): string
    {
        if ($this->request->filled('desde')) {
            return Carbon::parse($this->request->desde)->format('d-m-Y');
        }

        $primerPedido = Pedido::query()->orderBy('created_at', 'asc')->first();

        return $primerPedido?->created_at
            ? Carbon::parse($primerPedido->created_at)->format('d-m-Y')
            : Carbon::now()->format('d-m-Y');
    }

    protected function resolveFechaHasta(): string
    {
        if ($this->request->filled('hasta')) {
            return Carbon::parse($this->request->hasta)->format('d-m-Y');
        }

        return Carbon::now()->format('d-m-Y');
    }

    protected function getPedidos()
    {
        return (clone $this->buildQuery())
            ->latest()
            ->get();
    }

    protected function getResumenData(): array
    {
        $estadoMap = $this->estadoMap();
        $base = $this->buildQuery();

        $totalPedidos = (clone $base)->count();
        $pendientes   = (clone $base)->whereIn('estado', $estadoMap['pendiente'])->count();
        $aceptados    = (clone $base)->whereIn('estado', $estadoMap['aceptado'])->count();
        $cancelados   = (clone $base)->whereIn('estado', $estadoMap['cancelado'])->count();
        $montoTotal   = (clone $base)->whereIn('estado', $estadoMap['aceptado'])->sum('total');
        $ticketProm   = $aceptados > 0 ? (int) round($montoTotal / $aceptados) : 0;

        return compact(
            'totalPedidos',
            'pendientes',
            'aceptados',
            'cancelados',
            'montoTotal',
            'ticketProm'
        );
    }

    public function array(): array
    {
        $estadoLabels = $this->estadoLabels();
        $pedidos = $this->getPedidos();
        $resumen = $this->getResumenData();

        $rows = [
            ['LA HERRADURA - REPORTE DE PEDIDOS'],
            [''],
            ['Fecha de generación', Carbon::now()->format('d-m-Y H:i')],
            ['Desde', $this->resolveFechaDesde()],
            ['Hasta', $this->resolveFechaHasta()],
            ['Estado', $this->request->input('estado') ?: 'Todos'],
            [''],
            ['DETALLE DE PEDIDOS'],
            ['ID', 'Cliente', 'Estado', 'Total', 'Fecha', 'Hora'],
        ];

        foreach ($pedidos as $p) {
            $cliente = $p->cliente_nombre
                ?? ($p->user->name ?? null)
                ?? ($p->nombre_cliente ?? null)
                ?? '—';

            $estadoRaw = strtolower((string)($p->estado ?? $p->status ?? 'pendiente_aprobacion'));
            $estado = $estadoLabels[$estadoRaw] ?? ucfirst(str_replace('_', ' ', $estadoRaw));

            $total = (int)($p->total ?? $p->monto_total ?? $p->TOTAL ?? 0);
            $fechaRaw = $p->created_at ?? $p->fecha ?? $p->FEC_CREACION ?? null;

            $fecha = $fechaRaw ? Carbon::parse($fechaRaw)->format('d-m-Y') : '—';
            $hora  = $fechaRaw ? Carbon::parse($fechaRaw)->format('H:i') : '—';

            $rows[] = [
                $p->id ?? $p->PED_ID ?? '—',
                $cliente,
                $estado,
                $total,
                $fecha,
                $hora,
            ];
        }

        $rows[] = [''];
        $rows[] = ['RESUMEN'];
        $rows[] = ['Indicador', 'Valor'];
        $rows[] = ['Total pedidos', $resumen['totalPedidos']];
        $rows[] = ['Pendientes', $resumen['pendientes']];
        $rows[] = ['Aceptados', $resumen['aceptados']];
        $rows[] = ['Cancelados', $resumen['cancelados']];
        $rows[] = ['Total vendido', $resumen['montoTotal']];
        $rows[] = ['Ticket promedio', $resumen['ticketProm']];

        $rows[] = [''];
        $rows[] = ['DATOS GRÁFICO'];
        $rows[] = ['Estado', 'Cantidad'];
        $rows[] = ['Pendientes', $resumen['pendientes']];
        $rows[] = ['Aceptados', $resumen['aceptados']];
        $rows[] = ['Cancelados', $resumen['cancelados']];

        return $rows;
    }

    public function charts(): array
    {
        $pedidos = $this->getPedidos();
        $detalleStart = 9;
        $detalleEnd = $detalleStart + max($pedidos->count(), 1);

        $resumenHeaderRow = $detalleEnd + 2;
        $chartHeaderRow = $resumenHeaderRow + 8;
        $chartDataStart = $chartHeaderRow + 2;
        $chartDataEnd = $chartDataStart + 2;

        $label = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                "'Resumen'!\$B\$" . $chartHeaderRow,
                null,
                1
            )
        ];

        $categories = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_STRING,
                "'Resumen'!\$A\$" . $chartDataStart . ":\$A\$" . $chartDataEnd,
                null,
                3
            )
        ];

        $values = [
            new DataSeriesValues(
                DataSeriesValues::DATASERIES_TYPE_NUMBER,
                "'Resumen'!\$B\$" . $chartDataStart . ":\$B\$" . $chartDataEnd,
                null,
                3
            )
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($values) - 1),
            $label,
            $categories,
            $values
        );

        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);

        $chart = new Chart(
            'pedidos_por_estado',
            new Title('Pedidos por estado'),
            $legend,
            $plotArea
        );

        $chart->setTopLeftPosition('J3');
        $chart->setBottomRightPosition('T27');

        return [$chart];
    }

    public function title(): string
    {
        return 'Resumen';
    }
}