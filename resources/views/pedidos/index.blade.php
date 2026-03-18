@extends('layouts.app')

@section('content')

@php
    $badgeClass = function($estado) {
        return match($estado) {
            'aprobado' => 'bg-green-100 text-green-800 border-green-200',
            'rechazado' => 'bg-red-100 text-red-800 border-red-200',
            default => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        };
    };
@endphp

<div class="space-y-6 sm:space-y-8">

    {{-- Header responsive (2 filas en mobile) --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-semibold tracking-tight text-[#123617] dark:text-[#EAF3EA]">
                Pedidos
            </h1>
            <p class="text-sm text-[#40624a] dark:text-[#9ec79f] mt-2">
                Revisa el estado de tus pedidos y la coordinación.
            </p>
        </div>

        <a href="{{ route('tienda.home') }}"
           class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                  border border-[#DDEEDD] dark:border-[#16351F]
                  text-[#123617] dark:text-[#EAF3EA]
                  hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40">
            Ir a la tienda
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-2xl px-5 py-3 border border-green-200 bg-green-50 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl px-5 py-3 border border-red-200 bg-red-50 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    {{-- Desktop table + Mobile cards --}}
    <div class="rounded-2xl overflow-hidden
                bg-white/90 dark:bg-[#0B1A10]/80
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.45)]">

        {{-- MOBILE: Cards --}}
        <div class="block sm:hidden p-3 space-y-3">
            @forelse($pedidos as $pedido)
                <div class="rounded-xl border border-[#DDEEDD] dark:border-[#16351F]
                            bg-white/80 dark:bg-[#07120A]/60
                            p-4 space-y-3">

                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                                Pedido #{{ $pedido->id }}
                            </div>
                            <div class="text-xs text-[#40624a] dark:text-[#9ec79f] mt-1">
                                {{ optional($pedido->created_at)->format('d-m-Y H:i') }}
                            </div>
                        </div>

                        <span class="shrink-0 inline-flex items-center px-3 py-1 rounded-full border text-[10px] font-bold {{ $badgeClass($pedido->estado) }}">
                            {{ strtoupper(str_replace('_',' ', $pedido->estado)) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <div class="text-[11px] uppercase tracking-wide text-[#123617]/60 dark:text-[#EAF3EA]/60">
                                Cliente
                            </div>
                            <div class="text-[#123617] dark:text-[#EAF3EA] font-medium break-words">
                                {{ $pedido->user->name ?? '—' }}
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="text-[11px] uppercase tracking-wide text-[#123617]/60 dark:text-[#EAF3EA]/60">
                                Total
                            </div>
                            <div class="text-[#123617] dark:text-[#EAF3EA] font-semibold">
                                $ {{ number_format((int)$pedido->total, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('pedidos.show', $pedido) }}"
                       class="w-full inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs font-semibold
                              border border-[#DDEEDD] dark:border-[#16351F]
                              text-[#123617] dark:text-[#EAF3EA]
                              hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40">
                        Ver
                    </a>
                </div>
            @empty
                <div class="px-4 py-10 text-center text-sm text-[#40624a] dark:text-[#9ec79f]">
                    No hay pedidos registrados.
                </div>
            @endforelse
        </div>

        {{-- DESKTOP/TABLET: Tabla --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm min-w-[760px]">
                <thead class="bg-[#EAF6E7] dark:bg-[#0A2012]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[#123617]/70 dark:text-[#EAF3EA]/70">
                        <th class="px-4 md:px-6 py-3">ID</th>
                        <th class="px-4 md:px-6 py-3">Cliente</th>
                        <th class="px-4 md:px-6 py-3">Total</th>
                        <th class="px-4 md:px-6 py-3">Estado</th>
                        <th class="px-4 md:px-6 py-3">Fecha</th>
                        <th class="px-4 md:px-6 py-3 text-right">Acción</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#DDEEDD] dark:divide-[#16351F]">
                    @forelse($pedidos as $pedido)
                        <tr class="hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40 transition-colors">
                            <td class="px-4 md:px-6 py-4 font-semibold text-[#123617] dark:text-[#EAF3EA]">
                                #{{ $pedido->id }}
                            </td>

                            <td class="px-4 md:px-6 py-4">
                                {{ $pedido->user->name ?? '—' }}
                            </td>

                            <td class="px-4 md:px-6 py-4 font-semibold">
                                $ {{ number_format((int)$pedido->total, 0, ',', '.') }}
                            </td>

                            <td class="px-4 md:px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full border text-xs font-bold {{ $badgeClass($pedido->estado) }}">
                                    {{ strtoupper(str_replace('_',' ', $pedido->estado)) }}
                                </span>
                            </td>

                            <td class="px-4 md:px-6 py-4 text-[#40624a] dark:text-[#9ec79f]">
                                {{ optional($pedido->created_at)->format('d-m-Y H:i') }}
                            </td>

                            <td class="px-4 md:px-6 py-4 text-right">
                                <a href="{{ route('pedidos.show', $pedido) }}"
                                   class="inline-flex items-center rounded-xl px-4 py-2 text-xs font-semibold
                                          border border-[#DDEEDD] dark:border-[#16351F]
                                          text-[#123617] dark:text-[#EAF3EA]
                                          hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-[#40624a] dark:text-[#9ec79f]">
                                No hay pedidos registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($pedidos, 'links'))
            <div class="p-4 border-t border-[#DDEEDD] dark:border-[#16351F]">
                {{ $pedidos->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
