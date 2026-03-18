@extends('layouts.app')

@section('content')
@php
    $all = $pedidos ?? collect();

    $countPend = $all->whereIn('estado', ['pendiente','pendiente_aprobacion'])->count();
    $countApr  = $all->where('estado', 'aprobado')->count();
    $countRech = $all->where('estado', 'rechazado')->count();

    $puntosMap = collect($puntosEncuentro ?? [])
        ->keyBy('id')
        ->map(function ($p) {
            $label = (string)($p->nombre ?? '');
            if (!empty($p->direccion)) $label .= ' — ' . $p->direccion;
            return trim($label);
        });

    $labelPunto = function ($pedido) use ($puntosMap) {
        $id = (int)($pedido->punto_encuentro_id ?? 0);
        if ($id && $puntosMap->has($id)) return $puntosMap->get($id);
        return (string)($pedido->punto_encuentro ?? '—');
    };
@endphp

<div class="space-y-8">

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#123617] dark:text-[#92b95d]">
                Administración de Pedidos
            </h1>
            <p class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Aprobar/rechazar pedidos y revisar detalle.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="rounded-2xl px-4 py-3
                        bg-white/90 dark:bg-[#0B1A10]/80
                        border border-[#DDEEDD] dark:border-[#16351F]
                        shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03)]">
                <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Pendientes</div>
                <div class="mt-1 inline-flex items-center justify-center min-w-8 h-8 px-3 rounded-full bg-red-600 text-white text-sm font-bold">
                    {{ $countPend }}
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl
                bg-white/90 dark:bg-[#0B1A10]/80
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-sm">

        <div class="p-4 lg:p-5 space-y-5">

            <div class="flex flex-wrap gap-2">

                @php
                    $tabBase = "tabBtn flex items-center justify-center gap-2
                                rounded-full px-4 py-2 text-sm font-semibold
                                border border-[#DDEEDD] dark:border-[#16351F]
                                bg-white/80 dark:bg-[#07120A]/55
                                text-[#123617] dark:text-[#EAF3EA]
                                hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/75
                                transition focus:outline-none focus:ring-2 focus:ring-[#92b95d]/35";
                    $chipBase = "inline-flex items-center justify-center
                                 min-w-6 h-6 px-2 rounded-full text-xs font-bold
                                 bg-[#123617]/10 text-[#123617]
                                 dark:bg-white/10 dark:text-[#EAF3EA]";
                @endphp

                <button type="button" data-filter="all" class="{{ $tabBase }}">
                    Todos
                    <span class="{{ $chipBase }}">{{ $all->count() }}</span>
                </button>

                <button type="button" data-filter="pendiente" class="{{ $tabBase }}">
                    Pendientes
                    <span class="{{ $chipBase }} bg-yellow-500/20">{{ $countPend }}</span>
                </button>

                <button type="button" data-filter="aprobado" class="{{ $tabBase }}">
                    Aprobados
                    <span class="{{ $chipBase }} bg-green-600/15">{{ $countApr }}</span>
                </button>

                <button type="button" data-filter="rechazado" class="{{ $tabBase }}">
                    Rechazados
                    <span class="{{ $chipBase }} bg-red-600/15">{{ $countRech }}</span>
                </button>

            </div>

            <div class="w-full lg:max-w-xl">
                <div class="relative">
                    <input id="orderSearch" type="text"
                           placeholder="Buscar por #pedido, cliente, rut, teléfono, punto..."
                           class="w-full rounded-2xl pl-4 pr-24 py-2.5 text-sm
                                  border border-[#DDEEDD] dark:border-[#16351F]
                                  bg-white/90 dark:bg-[#07120A]/55
                                  text-[#123617] dark:text-[#EAF3EA]
                                  focus:outline-none focus:ring-2 focus:ring-[#92b95d]/35">

                    <button id="clearSearch" type="button"
                            class="absolute right-2 top-1/2 -translate-y-1/2
                                   rounded-xl px-3 py-1.5 text-xs font-semibold
                                   border border-[#DDEEDD] dark:border-[#16351F]
                                   bg-white/80 dark:bg-[#0B1A10]/65
                                   hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/75 transition">
                        Limpiar
                    </button>
                </div>

                <div id="searchMeta"
                     class="mt-2 text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60"></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                @php
                    $wrap = "rounded-2xl p-3 border border-[#DDEEDD] dark:border-[#16351F]
                             bg-white/60 dark:bg-[#07120A]/35";
                    $label = "block text-[11px] font-semibold text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mb-1";
                    $field = "w-full rounded-xl px-3 py-2 text-sm
                              border border-[#DDEEDD] dark:border-[#16351F]
                              bg-white/90 dark:bg-[#07120A]/60
                              text-[#123617] dark:text-[#EAF3EA]
                              focus:outline-none focus:ring-2 focus:ring-[#92b95d]/30";
                @endphp

                <div class="{{ $wrap }}">
                    <label class="{{ $label }}">Desde</label>
                    <input type="date" id="filterFrom" class="{{ $field }}">
                </div>

                <div class="{{ $wrap }}">
                    <label class="{{ $label }}">Hasta</label>
                    <input type="date" id="filterTo" class="{{ $field }}">
                </div>

                <div class="{{ $wrap }}">
                    <label class="{{ $label }}">Monto mín.</label>
                    <input type="number" id="filterMin" min="0" placeholder="0" class="{{ $field }}">
                </div>

                <div class="{{ $wrap }}">
                    <label class="{{ $label }}">Monto máx.</label>
                    <input type="number" id="filterMax" min="0" placeholder="999999999" class="{{ $field }}">
                </div>

            </div>

        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="ordersGrid">
        @forelse($all as $pedido)
            @php
                $rawEstado = strtolower(trim((string)($pedido->estado ?? 'pendiente_aprobacion')));

                $estado = match ($rawEstado) {
                    'pendiente', 'pendiente_aprobacion' => 'pendiente',
                    'aprobado' => 'aprobado',
                    'rechazado' => 'rechazado',
                    default => 'pendiente',
                };

                $isPend = $estado === 'pendiente';

                $badgeClass = $estado === 'pendiente' ? 'bg-yellow-500 text-[#07120A]'
                            : ($estado === 'aprobado' ? 'bg-green-600 text-white'
                            : 'bg-red-600 text-white');

                $badgeText = $estado === 'pendiente' ? 'Pendiente' : ($estado === 'aprobado' ? 'Aprobado' : 'Rechazado');

                $totalRaw = (int)($pedido->total ?? 0);
                $totalFmt = '$ ' . number_format($totalRaw, 0, ',', '.');

                $pedidoId = (int)$pedido->id;

                $clienteNombre = (string)($pedido->cliente_nombre ?? $pedido->user->name ?? '—');
                $clienteEmail  = (string)($pedido->cliente_email ?? $pedido->user->email ?? '—');
                $clienteRut    = (string)($pedido->cliente_rut ?? '—');
                $clientePhone  = (string)($pedido->cliente_phone ?? '—');

                $items     = $pedido->items ?? collect();
                $itemsText = $items->pluck('nombre')->implode(' ');

                $puntoMostrar = $labelPunto($pedido);

                $puntoCliente = (string)($puntoMostrar ?? '');
                $puntoAdmin   = (string)($pedido->punto_encuentro_confirmado ?? '');

                $horaCliente  = (string)($pedido->hora_estimada_cliente ?? '');
                $horaAdmin    = (string)($pedido->hora_estimada_confirmada ?? '');

                $msgCliente   = (string)($pedido->mensaje_cliente ?? '');
                $msgAdmin     = (string)($pedido->mensaje_admin ?? '');

                $searchBlob = mb_strtolower(trim(
                    "#{$pedidoId} {$clienteNombre} {$clienteEmail} {$clienteRut} {$clientePhone} {$puntoCliente} {$puntoAdmin} {$horaCliente} {$horaAdmin} {$msgCliente} {$msgAdmin} {$itemsText}"
                ));

                $dateIso = $pedido->created_at?->format('Y-m-d') ?? '';

                $comprobantePath = trim((string)($pedido->comprobante_path ?? ''));
                $comprobanteUrl = null;

                if ($comprobantePath !== '') {
                    if (str_starts_with($comprobantePath, 'http://') || str_starts_with($comprobantePath, 'https://')) {
                        $comprobanteUrl = $comprobantePath;
                    } else {
                        $comprobanteUrl = asset(ltrim($comprobantePath, '/'));
                    }
                }
            @endphp

            <div class="order-card rounded-2xl overflow-hidden
                        bg-white/90 dark:bg-[#0B1A10]/80
                        border border-[#DDEEDD] dark:border-[#16351F]
                        shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03)]"
                 data-estado="{{ $estado }}"
                 data-search="{{ $searchBlob }}"
                 data-date="{{ $dateIso }}"
                 data-total="{{ $totalRaw }}">

                <div class="p-4 border-b border-[#DDEEDD] dark:border-[#16351F]
                            flex items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                        Pedido #{{ $pedidoId }}
                    </div>

                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $badgeClass }}">
                        {{ $badgeText }}
                    </span>
                </div>

                <div class="p-4 space-y-4">
                    <div class="space-y-1">
                        <div class="text-sm">
                            <span class="text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Cliente:</span>
                            <span class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $clienteNombre }}</span>
                        </div>

                        <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                            {{ $clienteEmail }}
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs mt-2">
                            <div class="rounded-xl border border-[#DDEEDD] dark:border-[#16351F] bg-white/70 dark:bg-[#07120A]/40 px-3 py-2">
                                <div class="text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">RUT</div>
                                <div class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $clienteRut }}</div>
                            </div>
                            <div class="rounded-xl border border-[#DDEEDD] dark:border-[#16351F] bg-white/70 dark:bg-[#07120A]/40 px-3 py-2">
                                <div class="text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Teléfono</div>
                                <div class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $clientePhone }}</div>
                            </div>
                        </div>

                        <div class="text-sm mt-2">
                            <span class="text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Total:</span>
                            <span class="font-bold text-[#123617] dark:text-[#EAF3EA]">{{ $totalFmt }}</span>
                        </div>

                        <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                            Fecha: {{ $pedido->created_at?->format('d-m-Y H:i') ?? '—' }}
                        </div>
                    </div>

                    <div class="rounded-2xl p-3 border border-[#DDEEDD] dark:border-[#16351F]
                                bg-white/70 dark:bg-[#07120A]/40 space-y-2">
                        <div class="text-xs font-semibold text-[#123617] dark:text-[#EAF3EA]">Solicitud del cliente</div>

                        <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                            Punto:
                            <span class="font-semibold text-[#123617] dark:text-[#EAF3EA]">
                                {{ $puntoMostrar ?: '—' }}
                            </span>
                        </div>

                        <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                            Hora estimada: <span class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $pedido->hora_estimada_cliente ?? '—' }}</span>
                        </div>

                        @if(!empty($pedido->mensaje_cliente))
                            <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                Mensaje: <span class="text-[#123617] dark:text-[#EAF3EA]">{{ $pedido->mensaje_cliente }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="rounded-2xl p-3 border border-[#DDEEDD] dark:border-[#16351F]
                                bg-white/70 dark:bg-[#07120A]/40">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-xs font-semibold text-[#123617] dark:text-[#EAF3EA]">Ítems</div>
                            <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                {{ $items->sum('cantidad') }} u.
                            </div>
                        </div>

                        <div class="space-y-2">
                            @forelse($items as $it)
                                <div class="flex items-start justify-between gap-3 text-xs">
                                    <div class="text-[#123617] dark:text-[#EAF3EA]">
                                        <span class="font-semibold">{{ $it->nombre }}</span>
                                        <span class="opacity-70">x{{ (int)$it->cantidad }}</span>
                                    </div>
                                    <div class="text-[#123617] dark:text-[#EAF3EA] font-semibold">
                                        $ {{ number_format((int)($it->subtotal ?? 0), 0, ',', '.') }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                    Sin ítems.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-2xl p-3 border border-[#DDEEDD] dark:border-[#16351F]
                                bg-white/70 dark:bg-[#07120A]/40 space-y-2">
                        <div class="text-xs font-semibold text-[#123617] dark:text-[#EAF3EA]">
                            Comprobante de pago
                        </div>

                        @if($comprobanteUrl)
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ $comprobanteUrl }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold
                                          bg-[#123617] text-white hover:opacity-90 transition">
                                    Abrir en nueva pestaña
                                </a>

                                <button style ="display:none;"type="button"
                                        data-comprobante="{{ $comprobanteUrl }}"
                                        class="btnVerComprobante inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold
                                               border border-[#123617] text-[#123617]
                                               dark:text-[#EAF3EA] dark:border-[#EAF3EA]
                                               hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/70 transition">
                                    Ver en modal
                                </button>
                            </div>
                        @else
                            <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                No se ha subido comprobante.
                            </div>
                        @endif
                    </div>

                    @if(!$isPend)
                        <div class="rounded-2xl p-3 border border-[#DDEEDD] dark:border-[#16351F]
                                    bg-white/70 dark:bg-[#07120A]/40 space-y-2">
                            <div class="text-xs font-semibold text-[#123617] dark:text-[#EAF3EA]">Respuesta admin</div>

                            <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                Punto confirmado: <span class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $pedido->punto_encuentro_confirmado ?? '—' }}</span>
                            </div>

                            <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                Hora confirmada: <span class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $pedido->hora_estimada_confirmada ?? '—' }}</span>
                            </div>

                            @if(!empty($pedido->mensaje_admin))
                                <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                    Mensaje: <span class="text-[#123617] dark:text-[#EAF3EA]">{{ $pedido->mensaje_admin }}</span>
                                </div>
                            @endif

                            <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                Aprobado en: <span class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $pedido->aprobado_en ? \Carbon\Carbon::parse($pedido->aprobado_en)->format('d-m-Y H:i') : '—' }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                @if($isPend)
                    <div class="p-4 border-t border-[#DDEEDD] dark:border-[#16351F] space-y-3">
                        <div class="grid grid-cols-1 gap-2">
                            <input type="text"
                                   class="adminPunto w-full rounded-xl px-3 py-2 text-sm
                                          border border-[#DDEEDD] dark:border-[#16351F]
                                          bg-white/90 dark:bg-[#07120A]
                                          text-[#123617] dark:text-[#EAF3EA]"
                                   placeholder="Punto de encuentro confirmado (opcional)"
                                   value="{{ $pedido->punto_encuentro_confirmado ?? '' }}">

                            <input type="datetime-local"
                                   class="adminHora w-full rounded-xl px-3 py-2 text-sm
                                          border border-[#DDEEDD] dark:border-[#16351F]
                                          bg-white/90 dark:bg-[#07120A]
                                          text-[#123617] dark:text-[#EAF3EA]"
                                   value="{{ $pedido->hora_estimada_confirmada ? \Carbon\Carbon::parse($pedido->hora_estimada_confirmada)->format('Y-m-d\TH:i') : '' }}">

                            <textarea rows="2"
                                      class="adminMsg w-full rounded-xl px-3 py-2 text-sm
                                             border border-[#DDEEDD] dark:border-[#16351F]
                                             bg-white/90 dark:bg-[#07120A]
                                             text-[#123617] dark:text-[#EAF3EA]"
                                      placeholder="Mensaje admin (opcional)">{{ $pedido->mensaje_admin ?? '' }}</textarea>
                        </div>

                        <div class="flex gap-3">
                            <form method="POST" action="{{ route('admin.pedidos.aprobar', $pedido) }}" class="flex-1 orderApproveForm">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="punto_encuentro_confirmado">
                                <input type="hidden" name="hora_estimada_confirmada">
                                <input type="hidden" name="mensaje_admin">

                                <button type="submit"
                                        class="w-full rounded-xl px-4 py-2 text-sm font-semibold
                                               bg-green-600 text-white hover:opacity-90">
                                    Aprobar
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.pedidos.rechazar', $pedido) }}" class="flex-1 orderRejectForm">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="mensaje_admin">

                                <button type="submit"
                                        class="w-full rounded-xl px-4 py-2 text-sm font-semibold
                                               bg-red-600 text-white hover:opacity-90">
                                    Rechazar
                                </button>
                            </form>
                        </div>

                        <div class="text-[11px] text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                            Tip: puedes confirmar punto/hora y dejar un mensaje antes de aprobar.
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full">
                <div class="rounded-2xl px-5 py-10 text-center text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60
                            border border-[#DDEEDD] dark:border-[#16351F]
                            bg-white/90 dark:bg-[#0B1A10]/80">
                    No hay pedidos registrados.
                </div>
            </div>
        @endforelse
    </div>

    <div id="noResults"
         class="hidden rounded-2xl px-5 py-10 text-center text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60
                border border-[#DDEEDD] dark:border-[#16351F]
                bg-white/90 dark:bg-[#0B1A10]/80">
        No hay pedidos que coincidan con el filtro/búsqueda.
    </div>

</div>

<div id="comprobanteModal" class="fixed inset-0 z-[120] hidden items-center justify-center p-3 sm:p-6" aria-hidden="true">
    <div id="comprobanteOverlay" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

<div role="dialog" aria-modal="true"
     class="relative
            w-[92vw] sm:w-[720px]
            h-[80vh] sm:h-[480px]
            max-w-[720px] max-h-[480px]
            bg-white dark:bg-[#0B1A10]
            rounded-2xl overflow-hidden
            border border-[#DDEEDD] dark:border-[#16351F]
            shadow-[0px_30px_90px_-30px_rgba(0,0,0,0.75)]
            flex flex-col">

        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3
                    border-b border-[#DDEEDD] dark:border-[#16351F]
                    bg-white/80 dark:bg-[#07120A]/55 shrink-0">
            <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                Comprobante
            </div>

            <button id="closeComprobante"
                    type="button"
                    class="inline-flex items-center justify-center gap-2
                           text-xs font-semibold px-3 py-1.5 rounded-xl
                           border border-[#DDEEDD] dark:border-[#16351F]
                           bg-white/90 dark:bg-[#0B1A10]/70
                           hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/80 transition">
                Cerrar
            </button>
        </div>

        <!-- Contenido Imagen -->
        <div class="flex-1 bg-black p-4 overflow-hidden">
            <div class="w-full h-full overflow-auto rounded-xl bg-black flex items-center justify-center">
                <img id="comprobanteImg"
                     src=""
                     alt="Comprobante"
                     class="max-w-full max-h-full object-contain select-none" />
            </div>
        </div>

        <!-- Footer botones -->
        <div class="px-4 py-3 bg-black/95 shrink-0 flex flex-wrap gap-2">
            <a id="comprobanteOpenNew"
               href="#"
               target="_blank"
               rel="noopener"
               class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold
                      bg-[#123617] text-white hover:opacity-90 transition">
                Abrir en nueva pestaña
            </a>

            <a id="comprobanteDownload"
               href="#"
               download
               class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs font-semibold
                      border border-white/25 text-white/90
                      hover:bg-white/10 transition">
                Descargar
            </a>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cards = Array.from(document.querySelectorAll('.order-card'));
    const input = document.getElementById('orderSearch');
    const clearBtn = document.getElementById('clearSearch');
    const noResults = document.getElementById('noResults');
    const meta = document.getElementById('searchMeta');
    const tabBtns = Array.from(document.querySelectorAll('.tabBtn'));

    const fromInput = document.getElementById('filterFrom');
    const toInput   = document.getElementById('filterTo');
    const minInput  = document.getElementById('filterMin');
    const maxInput  = document.getElementById('filterMax');

    const comprobanteModal = document.getElementById('comprobanteModal');
    const comprobanteOverlay = document.getElementById('comprobanteOverlay');
    const closeComprobante = document.getElementById('closeComprobante');

    const comprobanteImg = document.getElementById('comprobanteImg');
    const comprobanteOpenNew = document.getElementById('comprobanteOpenNew');
    const comprobanteDownload = document.getElementById('comprobanteDownload');

    let currentFilter = 'all';

    const apply = () => {
        const q = (input?.value || '').trim().toLowerCase();

        const from = fromInput?.value || null;
        const to   = toInput?.value || null;

        const min = parseInt(minInput?.value || 0, 10) || 0;
        const max = parseInt(maxInput?.value || 0, 10) || 0;

        let visible = 0;

        cards.forEach(card => {
            const estado = (card.dataset.estado || 'pendiente');
            const blob = (card.dataset.search || '');

            const date = (card.dataset.date || '');
            const total = parseInt(card.dataset.total || 0, 10) || 0;

            const okEstado = (currentFilter === 'all') ? true : (estado === currentFilter);
            const okSearch = (!q) ? true : blob.includes(q);

            let okDate = true;
            if (from && date && date < from) okDate = false;
            if (to && date && date > to) okDate = false;

            let okAmount = true;
            if (min && total < min) okAmount = false;
            if (max && total > max) okAmount = false;

            const show = okEstado && okSearch && okDate && okAmount;
            card.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        noResults?.classList.toggle('hidden', visible !== 0);

        if (meta) {
            if (!q && currentFilter === 'all' && !from && !to && !min && !max) {
                meta.textContent = 'Tip: busca por cliente, rut, teléfono, #pedido, productos, punto de encuentro u hora.';
            } else {
                const fromTxt = from || '—';
                const toTxt = to || '—';
                const minTxt = min ? min.toLocaleString('es-CL') : '—';
                const maxTxt = max ? max.toLocaleString('es-CL') : '—';
                meta.textContent = `Filtro: ${currentFilter} · Fecha: ${fromTxt} → ${toTxt} · Monto: ${minTxt} → ${maxTxt} · Búsqueda: "${q || '—'}" · Resultados: ${visible}`;
            }
        }
    };

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            currentFilter = btn.dataset.filter || 'all';
            tabBtns.forEach(b => b.classList.remove('ring-2','ring-[#92b95d]/40'));
            btn.classList.add('ring-2','ring-[#92b95d]/40');
            apply();
        });
    });

    input?.addEventListener('input', apply);
    input?.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            input.value = '';
            apply();
        }
    });

    clearBtn?.addEventListener('click', () => {
        if (input) input.value = '';
        apply();
        input?.focus();
    });

    [fromInput, toInput, minInput, maxInput].forEach(el => {
        el?.addEventListener('input', apply);
        el?.addEventListener('change', apply);
    });

    document.querySelectorAll('.order-card').forEach(card => {
        const punto = card.querySelector('.adminPunto');
        const hora = card.querySelector('.adminHora');
        const msg = card.querySelector('.adminMsg');
        const fA = card.querySelector('.orderApproveForm');
        const fR = card.querySelector('.orderRejectForm');

        if (fA && punto && hora && msg) {
            fA.addEventListener('submit', () => {
                fA.querySelector('input[name="punto_encuentro_confirmado"]').value = punto.value || '';
                fA.querySelector('input[name="hora_estimada_confirmada"]').value = hora.value || '';
                fA.querySelector('input[name="mensaje_admin"]').value = msg.value || '';
            });
        }

        if (fR && msg) {
            fR.addEventListener('submit', () => {
                fR.querySelector('input[name="mensaje_admin"]').value = msg.value || '';
            });
        }
    });

    const openComprobante = (url) => {
        if (!url) return;

        if (comprobanteImg) comprobanteImg.src = url;
        if (comprobanteOpenNew) comprobanteOpenNew.href = url;
        if (comprobanteDownload) comprobanteDownload.href = url;

        comprobanteModal?.classList.remove('hidden');
        comprobanteModal?.classList.add('flex');
        document.documentElement.classList.add('overflow-hidden');
        document.body.classList.add('overflow-hidden');
    };

    const closeComprobanteModal = () => {
        comprobanteModal?.classList.add('hidden');
        comprobanteModal?.classList.remove('flex');

        if (comprobanteImg) comprobanteImg.src = '';
        if (comprobanteOpenNew) comprobanteOpenNew.href = '#';
        if (comprobanteDownload) comprobanteDownload.href = '#';

        document.documentElement.classList.remove('overflow-hidden');
        document.body.classList.remove('overflow-hidden');
    };

    document.querySelectorAll('.btnVerComprobante').forEach(btn => {
        btn.addEventListener('click', () => {
            openComprobante(btn.dataset.comprobante || '');
        });
    });

    closeComprobante?.addEventListener('click', closeComprobanteModal);
    comprobanteOverlay?.addEventListener('click', closeComprobanteModal);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && comprobanteModal && !comprobanteModal.classList.contains('hidden')) {
            closeComprobanteModal();
        }
    });

    tabBtns.find(b => b.dataset.filter === 'all')?.classList.add('ring-2','ring-[#92b95d]/40');
    apply();
});
</script>
@endsection