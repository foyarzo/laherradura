@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Carbon;

    $filters = $filters ?? [
        'desde'  => request('desde'),
        'hasta'  => request('hasta'),
        'estado' => request('estado'),
    ];

    $kpis = $kpis ?? [
        'total' => 0,
        'pendientes' => 0,
        'aceptados' => 0,
        'cancelados' => 0,
        'monto_total' => 0,
        'ticket_promedio' => 0,
    ];

    $rows = $rows ?? collect();
    $ventasPorDia = collect($ventasPorDia ?? []);
    $pedidosPorEstado = collect($pedidosPorEstado ?? []);
    $topProductos = collect($topProductos ?? []);

    $isPaginator = is_object($rows) && method_exists($rows, 'total');
    $totalRows   = $isPaginator ? (int) $rows->total() : (is_countable($rows) ? count($rows) : 0);
    $shownRows   = $isPaginator ? (int) $rows->count() : $totalRows;
    $page        = $isPaginator ? (int) $rows->currentPage() : 1;
    $lastPage    = $isPaginator ? (int) $rows->lastPage() : 1;

    $estadoSel = (string) ($filters['estado'] ?? '');

    $estadoLabels = $estadoLabels ?? [
        'pendiente_aprobacion' => 'Pendiente aprobación',
        'pendiente'            => 'Pendiente',
        'aceptado'             => 'Aceptado',
        'aprobado'             => 'Aprobado',
        'confirmado'           => 'Confirmado',
        'cancelado'            => 'Cancelado',
        'rechazado'            => 'Rechazado',
        'anulado'              => 'Anulado',
    ];

    $estadoFilterLabel = [
        ''          => 'Todos',
        'pendiente' => 'Pendiente',
        'aceptado'  => 'Aceptado',
        'cancelado' => 'Cancelado',
    ];

    $hasAnyFilter = !empty($filters['desde']) || !empty($filters['hasta']) || !empty($filters['estado']);

    $ventasLabels = $ventasPorDia->pluck('fecha')->map(function ($f) {
        try {
            return Carbon::parse($f)->format('d-m');
        } catch (\Throwable $e) {
            return $f;
        }
    })->values();

    $ventasSeries = $ventasPorDia->pluck('total')->map(fn ($v) => (int) $v)->values();

    $estadoChartLabels = $pedidosPorEstado->map(function ($row) {
        return $row['label'] ?? $row->label ?? $row['estado'] ?? $row->estado ?? 'Estado';
    })->values();

    $estadoChartSeries = $pedidosPorEstado->map(function ($row) {
        return (int) ($row['total'] ?? $row->total ?? 0);
    })->values();
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-[#123617] dark:text-[#92b95d]">Dashboard financiero de pedidos</h1>
            <p class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Balance general de ventas, estados y rendimiento comercial.
            </p>

            <div class="mt-2 text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Mostrando <span class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $shownRows }}</span>
                de <span class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $totalRows }}</span>
                @if($isPaginator)
                    <span class="mx-2">·</span>
                    Página <span class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $page }}</span> / {{ $lastPage }}
                @endif

                @if($hasAnyFilter)
                    <span class="mx-2">·</span>
                    <span class="inline-flex items-center gap-2 flex-wrap">
                        <span>Filtros:</span>
                        @if(!empty($filters['desde']))
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#123617]/10 text-[#123617] dark:bg-white/10 dark:text-[#EAF3EA]">
                                desde {{ $filters['desde'] }}
                            </span>
                        @endif
                        @if(!empty($filters['hasta']))
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#123617]/10 text-[#123617] dark:bg-white/10 dark:text-[#EAF3EA]">
                                hasta {{ $filters['hasta'] }}
                            </span>
                        @endif
                        @if(!empty($filters['estado']))
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-[#123617]/10 text-[#123617] dark:bg-white/10 dark:text-[#EAF3EA]">
                                estado: {{ $estadoFilterLabel[$filters['estado']] ?? $filters['estado'] }}
                            </span>
                        @endif
                    </span>
                @endif
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <form method="GET" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <input type="date" name="desde" value="{{ $filters['desde'] ?? '' }}"
                       class="w-full sm:w-auto rounded-xl px-3 py-2 text-sm
                              border border-[#DDEEDD] dark:border-[#16351F]
                              bg-white/90 dark:bg-[#07120A]
                              text-[#123617] dark:text-[#EAF3EA]">

                <input type="date" name="hasta" value="{{ $filters['hasta'] ?? '' }}"
                       class="w-full sm:w-auto rounded-xl px-3 py-2 text-sm
                              border border-[#DDEEDD] dark:border-[#16351F]
                              bg-white/90 dark:bg-[#07120A]
                              text-[#123617] dark:text-[#EAF3EA]">

                <select name="estado"
                        class="w-full sm:w-auto rounded-xl px-3 py-2 text-sm
                               border border-[#DDEEDD] dark:border-[#16351F]
                               bg-white/90 dark:bg-[#07120A]
                               text-[#123617] dark:text-[#EAF3EA]">
                    <option value="" {{ $estadoSel === '' ? 'selected' : '' }}>Todos</option>
                    <option value="pendiente" {{ $estadoSel === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="aceptado" {{ $estadoSel === 'aceptado' ? 'selected' : '' }}>Aceptado</option>
                    <option value="cancelado" {{ $estadoSel === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>

                <button type="submit"
                        class="rounded-xl px-4 py-2 text-sm font-semibold
                               bg-[#123617] text-white hover:opacity-90 transition">
                    Filtrar
                </button>

                <a href="{{ route('pedidos.dashboard') }}"
                   class="rounded-xl px-4 py-2 text-sm font-semibold text-center
                          border border-[#DDEEDD] dark:border-[#16351F]
                          text-[#123617] dark:text-[#EAF3EA]
                          hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40 transition">
                    Limpiar
                </a>
            </form>

            <a href="{{ route('pedidos.export', request()->query()) }}"
               class="rounded-xl px-4 py-2 text-sm font-semibold text-center
                      bg-green-600 text-white hover:opacity-90 transition">
                Descargar Excel
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-3">
        @php
            $cards = [
                ['label' => 'Total pedidos', 'val' => (int) ($kpis['total'] ?? 0)],
                ['label' => 'Pendientes', 'val' => (int) ($kpis['pendientes'] ?? 0)],
                ['label' => 'Aceptados', 'val' => (int) ($kpis['aceptados'] ?? 0)],
                ['label' => 'Cancelados', 'val' => (int) ($kpis['cancelados'] ?? 0)],
                ['label' => 'Total vendido', 'val' => '$ ' . number_format((int) ($kpis['monto_total'] ?? 0), 0, ',', '.')],
                ['label' => 'Ticket prom.', 'val' => '$ ' . number_format((int) ($kpis['ticket_promedio'] ?? 0), 0, ',', '.')],
            ];
        @endphp

        @foreach($cards as $c)
            <div class="rounded-2xl p-4
                        bg-white/90 dark:bg-[#0B1A10]/80
                        border border-[#DDEEDD] dark:border-[#16351F]
                        shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.35)]">
                <div class="text-xs font-semibold tracking-wide uppercase text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                    {{ $c['label'] }}
                </div>
                <div class="mt-2 text-2xl font-bold text-[#123617] dark:text-[#EAF3EA]">
                    {{ $c['val'] }}
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 rounded-2xl p-5
                    bg-white/90 dark:bg-[#0B1A10]/80
                    border border-[#DDEEDD] dark:border-[#16351F]
                    shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.35)]">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <div class="text-base font-semibold text-[#123617] dark:text-[#EAF3EA]">Ventas por día</div>
                    <div class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Montos vendidos según fechas filtradas</div>
                </div>
            </div>
            <div class="h-[320px]">
                <canvas id="ventasChart"></canvas>
            </div>
        </div>

        <div class="rounded-2xl p-5
                    bg-white/90 dark:bg-[#0B1A10]/80
                    border border-[#DDEEDD] dark:border-[#16351F]
                    shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.35)]">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <div class="text-base font-semibold text-[#123617] dark:text-[#EAF3EA]">Pedidos por estado</div>
                    <div class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Distribución del periodo seleccionado</div>
                </div>
            </div>
            <div class="h-[320px]">
                <canvas id="estadoChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl overflow-hidden
                    bg-white/90 dark:bg-[#0B1A10]/80
                    border border-[#DDEEDD] dark:border-[#16351F]
                    shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.45)]">
            <div class="px-5 py-4 flex items-center justify-between">
                <div>
                    <div class="text-base font-semibold text-[#123617] dark:text-[#EAF3EA]">Pedidos</div>
                    <div class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Listado financiero según filtros</div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[860px] w-full text-sm">
                    <thead class="bg-[#EAF6E7] dark:bg-[#0A2012]">
                        <tr class="text-left text-xs uppercase tracking-wide text-[#123617]/70 dark:text-[#EAF3EA]/70">
                            <th class="px-5 py-3">#</th>
                            <th class="px-5 py-3">Cliente</th>
                            <th class="px-5 py-3">Estado</th>
                            <th class="px-5 py-3">Total</th>
                            <th class="px-5 py-3">Fecha</th>
                            <th class="px-5 py-3">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#DDEEDD] dark:divide-[#16351F]">
                        @forelse($rows as $p)
                            @php
                                $id = $p->id ?? $p->PED_ID ?? null;

                                $cliente = $p->cliente_nombre
                                    ?? ($p->user->name ?? null)
                                    ?? ($p->nombre_cliente ?? null)
                                    ?? '—';

                                $estadoRaw = strtolower((string) ($p->estado ?? $p->status ?? 'pendiente_aprobacion'));

                                $estadoLabel = $estadoLabels[$estadoRaw]
                                    ?? ucfirst(str_replace('_', ' ', $estadoRaw));

                                $total = (int) ($p->total ?? $p->monto_total ?? $p->TOTAL ?? 0);

                                $fechaRaw = $p->created_at ?? $p->fecha ?? $p->FEC_CREACION ?? null;
                                $fecha = $fechaRaw ? Carbon::parse($fechaRaw)->format('d-m-Y H:i') : '—';

                                $badge = match (true) {
                                    in_array($estadoRaw, ['aceptado', 'aprobado', 'confirmado']) => 'bg-green-100 text-green-800 border-green-200',
                                    in_array($estadoRaw, ['cancelado', 'rechazado', 'anulado']) => 'bg-red-100 text-red-800 border-red-200',
                                    default => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                };
                            @endphp

                            <tr class="hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40 transition-colors">
                                <td class="px-5 py-4 font-semibold text-[#123617] dark:text-[#EAF3EA]">
                                    {{ $id ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-[#123617] dark:text-[#EAF3EA]">
                                    {{ $cliente }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold border {{ $badge }}">
                                        {{ $estadoLabel }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-semibold text-[#123617] dark:text-[#EAF3EA] whitespace-nowrap">
                                    $ {{ number_format($total, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-[#3b6a33]/80 dark:text-[#EAF3EA]/70 whitespace-nowrap">
                                    {{ $fecha }}
                                </td>
                                <td class="px-5 py-4">
                                    @if($id)
                                        <a href="{{ route('pedidos.show', $id) }}"
                                           class="inline-flex items-center rounded-xl px-3 py-2 text-xs font-semibold
                                                  bg-[#123617] text-white hover:opacity-90 transition">
                                            Ver
                                        </a>
                                    @else
                                        <span class="text-xs text-[#3b6a33]/60 dark:text-[#EAF3EA]/50">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                    Sin pedidos para los filtros actuales.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($isPaginator)
                <div class="px-5 py-4">
                    {{ method_exists($rows, 'withQueryString') ? $rows->withQueryString()->links() : $rows->links() }}
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl p-5
                        bg-white/90 dark:bg-[#0B1A10]/80
                        border border-[#DDEEDD] dark:border-[#16351F]
                        shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.35)]">
                <div class="text-base font-semibold text-[#123617] dark:text-[#EAF3EA]">Top productos</div>
                <div class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Por cantidad según filtros</div>

                <div class="mt-4 space-y-3">
                    @forelse($topProductos as $tp)
                        @php
                            $name = $tp->name ?? $tp->producto ?? 'Producto';
                            $qty = (int) ($tp->qty ?? $tp->cantidad ?? 0);
                        @endphp
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA] truncate">{{ $name }}</div>
                                <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Vendidos: {{ $qty }}</div>
                            </div>
                            <div class="text-sm font-bold text-[#123617] dark:text-[#EAF3EA]">{{ $qty }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                            Aún no hay datos de productos para los filtros seleccionados.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl p-5
                        bg-white/90 dark:bg-[#0B1A10]/80
                        border border-[#DDEEDD] dark:border-[#16351F]
                        shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.35)]">
                <div class="text-base font-semibold text-[#123617] dark:text-[#EAF3EA]">Resumen rápido</div>
                <div class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Lectura del periodo actual</div>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-[#3b6a33]/80 dark:text-[#EAF3EA]/70">Pedidos analizados</span>
                        <span class="text-sm font-bold text-[#123617] dark:text-[#EAF3EA]">{{ (int) ($kpis['total'] ?? 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-[#3b6a33]/80 dark:text-[#EAF3EA]/70">Ventas aceptadas</span>
                        <span class="text-sm font-bold text-[#123617] dark:text-[#EAF3EA]">$ {{ number_format((int) ($kpis['monto_total'] ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-[#3b6a33]/80 dark:text-[#EAF3EA]/70">Ticket promedio</span>
                        <span class="text-sm font-bold text-[#123617] dark:text-[#EAF3EA]">$ {{ number_format((int) ($kpis['ticket_promedio'] ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-[#3b6a33]/80 dark:text-[#EAF3EA]/70">Pedidos aceptados</span>
                        <span class="text-sm font-bold text-[#123617] dark:text-[#EAF3EA]">{{ (int) ($kpis['aceptados'] ?? 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-[#3b6a33]/80 dark:text-[#EAF3EA]/70">Pedidos cancelados</span>
                        <span class="text-sm font-bold text-[#123617] dark:text-[#EAF3EA]">{{ (int) ($kpis['cancelados'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const ventasLabels = @json($ventasLabels);
    const ventasSeries = @json($ventasSeries);
    const estadoLabels = @json($estadoChartLabels);
    const estadoSeries = @json($estadoChartSeries);

    const ventasCtx = document.getElementById('ventasChart');
    const estadoCtx = document.getElementById('estadoChart');

    if (ventasCtx) {
        new Chart(ventasCtx, {
            type: 'line',
            data: {
                labels: ventasLabels,
                datasets: [{
                    label: 'Ventas',
                    data: ventasSeries,
                    tension: 0.35,
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = Number(context.raw || 0);
                                return 'Ventas: $ ' + value.toLocaleString('es-CL');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$ ' + Number(value).toLocaleString('es-CL');
                            }
                        }
                    }
                }
            }
        });
    }

    if (estadoCtx) {
        new Chart(estadoCtx, {
            type: 'doughnut',
            data: {
                labels: estadoLabels,
                datasets: [{
                    data: estadoSeries,
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = Number(context.raw || 0);
                                return label + ': ' + value.toLocaleString('es-CL');
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection