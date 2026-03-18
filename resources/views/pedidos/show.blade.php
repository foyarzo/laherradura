@extends('layouts.app')

@section('content')
@php
    $estado = $pedido->estado ?? 'pendiente_aprobacion';
    $badge = match ($estado) {
        'aprobado' => 'bg-green-100 text-green-800 border-green-200',
        'rechazado' => 'bg-red-100 text-red-800 border-red-200',
        default => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    };

    $fmt = fn($dt) => $dt ? \Carbon\Carbon::parse($dt)->format('d-m-Y H:i') : null;

    $hayRespuestaAdmin =
        filled($pedido->mensaje_admin)
        || filled($pedido->punto_encuentro_confirmado)
        || filled($pedido->hora_estimada_confirmada);

    $labelRespuesta = $hayRespuestaAdmin ? 'RESPONDIDO' : 'PENDIENTE';
@endphp

<div class="space-y-6 sm:space-y-8">

    {{-- Banner respuesta admin (responsive) --}}
    @if($hayRespuestaAdmin)
        <div class="rounded-2xl p-4 sm:p-5 border border-[#81a553]/35
                    bg-gradient-to-r from-[#EAF6E7] via-white to-[#EAF6E7]
                    dark:from-[#0A2012] dark:via-[#0B1A10] dark:to-[#0A2012]
                    shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.35)]">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 sm:gap-4">
                <div class="flex items-start gap-3">
                    <div class="relative mt-1 shrink-0">
                        <span class="inline-flex h-3 w-3 rounded-full bg-[#2f7a36]"></span>
                        <span class="absolute -inset-1 rounded-full bg-[#2f7a36]/25 animate-ping"></span>
                    </div>

                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm font-extrabold tracking-wide text-[#123617] dark:text-[#EAF3EA]">
                                ¡El admin respondió tu pedido!
                            </p>
                            <span class="text-[10px] px-2 py-1 rounded-full border
                                         bg-white/70 dark:bg-[#0D1E12]/60
                                         border-[#81a553]/35 text-[#123617] dark:text-[#EAF3EA] font-bold">
                                {{ $labelRespuesta }}
                            </span>
                        </div>

                        <p class="mt-1 text-xs text-[#3b6a33]/80 dark:text-[#EAF3EA]/70">
                            Toca para ver el punto, hora y mensaje confirmados.
                        </p>
                    </div>
                </div>

                <button type="button"
                        data-open-admin-modal
                        class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs font-bold
                               bg-[#1e4e25] text-white hover:bg-[#123617]
                               dark:bg-[#92b95d] dark:hover:bg-[#81a553] dark:text-[#07120A]">
                    Ver respuesta
                </button>
            </div>
        </div>
    @endif

    {{-- Header responsive --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold text-[#123617] dark:text-[#92b95d]">
                Pedido #{{ $pedido->id }}
            </h1>
            <p class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mt-1">
                Estado y coordinación del pedido.
            </p>
        </div>

        <span class="w-fit inline-flex items-center px-3 py-1 rounded-full border text-xs font-bold {{ $badge }}">
            {{ strtoupper(str_replace('_',' ', $estado)) }}
        </span>
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

    {{-- Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Items --}}
        <div class="lg:col-span-2 rounded-2xl overflow-hidden
                    bg-white/90 dark:bg-[#0B1A10]/80
                    border border-[#DDEEDD] dark:border-[#16351F]
                    shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.45)]">

            <div class="px-5 sm:px-6 py-4 border-b border-[#DDEEDD] dark:border-[#16351F]">
                <h2 class="font-semibold text-[#123617] dark:text-[#EAF3EA]">Items</h2>
            </div>

            {{-- MOBILE: cards (sin scroll horizontal) --}}
            <div class="block sm:hidden p-4 space-y-4">
                @foreach($pedido->items as $it)
                    <div class="rounded-xl border border-[#DDEEDD] dark:border-[#16351F]
                                bg-white/80 dark:bg-[#07120A]/60 p-4 space-y-2">
                        <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA] break-words">
                            {{ $it->nombre }}
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <div class="text-[11px] text-[#3b6a33]/60 dark:text-[#EAF3EA]/60">Precio</div>
                                <div class="font-medium">$ {{ number_format((int)$it->precio, 0, ',', '.') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[11px] text-[#3b6a33]/60 dark:text-[#EAF3EA]/60">Cant.</div>
                                <div class="font-medium">{{ (int)$it->cantidad }}</div>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-[#DDEEDD] dark:border-[#16351F] text-right text-sm font-bold">
                            Subtotal: $ {{ number_format((int)$it->subtotal, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach

                <div class="rounded-xl border border-[#DDEEDD] dark:border-[#16351F]
                            bg-[#F6FBF4] dark:bg-[#07120A]/40 p-4 text-right">
                    <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Total</div>
                    <div class="text-lg font-extrabold text-[#123617] dark:text-[#EAF3EA]">
                        $ {{ number_format((int)$pedido->total, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- DESKTOP: tabla --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[#EAF6E7] dark:bg-[#0A2012]">
                        <tr class="text-left text-xs uppercase tracking-wide text-[#123617]/70 dark:text-[#EAF3EA]/70">
                            <th class="px-6 py-3">Producto</th>
                            <th class="px-6 py-3">Precio</th>
                            <th class="px-6 py-3">Cant.</th>
                            <th class="px-6 py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#DDEEDD] dark:divide-[#16351F]">
                        @foreach($pedido->items as $it)
                            <tr class="hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40 transition-colors">
                                <td class="px-6 py-4 font-semibold text-[#123617] dark:text-[#EAF3EA]">
                                    {{ $it->nombre }}
                                </td>
                                <td class="px-6 py-4">$ {{ number_format((int)$it->precio, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">{{ (int)$it->cantidad }}</td>
                                <td class="px-6 py-4 font-semibold">
                                    $ {{ number_format((int)$it->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-[#F6FBF4] dark:bg-[#07120A]/40">
                            <td class="px-6 py-4 font-semibold" colspan="3">Total</td>
                            <td class="px-6 py-4 font-bold">
                                $ {{ number_format((int)$pedido->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Tu solicitud --}}
        <div class="space-y-4 sm:space-y-6">
            <div class="rounded-2xl p-5 sm:p-6
                        bg-white/90 dark:bg-[#0B1A10]/80
                        border border-[#DDEEDD] dark:border-[#16351F]
                        shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.45)]">

                <h3 class="font-semibold text-[#123617] dark:text-[#EAF3EA] mb-4">
                    Tu solicitud
                </h3>

                <div class="text-sm space-y-4 text-[#123617] dark:text-[#EAF3EA]">
                    <div>
                        <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Punto de encuentro</div>
                        <div class="font-semibold break-words">{{ $pedido->punto_encuentro ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Hora estimada</div>
                        <div class="font-semibold">{{ $fmt($pedido->hora_estimada_cliente) ?? '—' }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Mensaje</div>
                        <div class="whitespace-pre-line break-words">{{ $pedido->mensaje_cliente ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <a href="{{ route('tienda.home') }}"
               class="w-full inline-flex items-center justify-center rounded-2xl px-5 py-3 text-sm font-semibold
                      border border-[#DDEEDD] dark:border-[#16351F]
                      text-[#123617] dark:text-[#EAF3EA]
                      hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40">
                Volver a la tienda
            </a>
        </div>

    </div>
</div>

{{-- MODAL (responsive + scroll interno) --}}
<div id="adminReplyModal" class="fixed inset-0 z-[60] hidden" aria-hidden="true">
    <div data-close-admin-modal class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    <div class="relative h-[100svh] flex items-center justify-center p-3 sm:p-4">
        <div role="dialog" aria-modal="true"
             class="w-full max-w-2xl rounded-2xl overflow-hidden
                    bg-white dark:bg-[#0B1A10]
                    border border-[#DDEEDD] dark:border-[#16351F]
                    shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_30px_80px_-30px_rgba(0,0,0,0.55)]
                    max-h-[92svh] flex flex-col">

            <div class="px-5 sm:px-6 py-4 border-b border-[#DDEEDD] dark:border-[#16351F]
                        bg-[#F6FBF4] dark:bg-[#07120A]/50">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-extrabold tracking-wide text-[#123617] dark:text-[#EAF3EA]">
                            Respuesta del admin
                        </h3>
                        <p class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mt-1">
                            Pedido #{{ $pedido->id }} · {{ $labelRespuesta }}
                        </p>
                    </div>

                    <button type="button"
                            data-close-admin-modal
                            class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-bold
                                   border border-[#DDEEDD] dark:border-[#16351F]
                                   text-[#123617] dark:text-[#EAF3EA] hover:bg-white/60 dark:hover:bg-[#0D1E12]/50">
                        Cerrar
                    </button>
                </div>
            </div>

            <div class="p-4 sm:p-6 space-y-4 overflow-y-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="rounded-xl p-4 border border-red-400/30 bg-red-50 dark:bg-red-900/20">
                        <div class="text-[11px] text-red-600 dark:text-red-300">Punto confirmado</div>
                        <div class="mt-1 font-extrabold text-red-700 dark:text-red-200 break-words">
                            {{ $pedido->punto_encuentro_confirmado ?? '—' }}
                        </div>
                    </div>

                    <div class="rounded-xl p-4 border border-red-400/30 bg-red-50 dark:bg-red-900/20">
                        <div class="text-[11px] text-red-600 dark:text-red-300">Hora confirmada</div>
                        <div class="mt-1 font-extrabold text-red-700 dark:text-red-200">
                            {{ $fmt($pedido->hora_estimada_confirmada) ?? '—' }}
                        </div>
                    </div>
                </div>

                <div class="rounded-xl p-4 border border-red-500/40 bg-red-100 dark:bg-red-900/25">
                    <div class="text-[11px] text-red-600 dark:text-red-300 mb-1">Mensaje admin</div>
                    <div class="whitespace-pre-line font-extrabold text-red-800 dark:text-red-200 break-words">
                        {{ $pedido->mensaje_admin ?? '—' }}
                    </div>
                </div>
            </div>

            <div class="px-5 sm:px-6 py-4 border-t border-[#DDEEDD] dark:border-[#16351F]
                        bg-[#F6FBF4] dark:bg-[#07120A]/50 flex items-center justify-end gap-2">
                <button type="button"
                        data-close-admin-modal
                        class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs font-bold
                               border border-[#DDEEDD] dark:border-[#16351F]
                               text-[#123617] dark:text-[#EAF3EA] hover:bg-white/60 dark:hover:bg-[#0D1E12]/50">
                    Entendido
                </button>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    const hasReply = @json((bool) $hayRespuestaAdmin);

    const modal = document.getElementById('adminReplyModal');
    if (!modal) return;

    const openBtns = document.querySelectorAll('[data-open-admin-modal]');
    const closeBtns = modal.querySelectorAll('[data-close-admin-modal]');

    const openModal = () => {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
    };

    openBtns.forEach(btn => btn.addEventListener('click', openModal));
    closeBtns.forEach(btn => btn.addEventListener('click', closeModal));

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });

    if (hasReply) {
        setTimeout(openModal, 250);
    }
})();
</script>
@endsection
