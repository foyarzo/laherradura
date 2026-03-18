@extends('layouts.app')

@section('content')
<div class="space-y-6 sm:space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#123617] dark:text-[#92b95d]">Carrito</h1>
            <p class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Revisa y ajusta tus productos antes de continuar.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
            <a href="{{ url()->previous() }}"
               class="w-full sm:w-auto text-center rounded-xl px-4 py-2 text-sm font-semibold
                      border border-[#DDEEDD] dark:border-[#16351F]
                      text-[#123617] dark:text-[#EAF3EA]
                      hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40 transition">
                Volver
            </a>

            <form method="POST" action="{{ route('tienda.carrito.clear') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit"
                        class="w-full sm:w-auto rounded-xl px-4 py-2 text-sm font-semibold
                               bg-red-600 text-white hover:opacity-90 transition">
                    Vaciar
                </button>
            </form>
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

    <div class="rounded-2xl overflow-hidden
                bg-white/90 dark:bg-[#0B1A10]/80
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.45)]">

        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-[860px] w-full text-sm">
                <thead class="bg-[#EAF6E7] dark:bg-[#0A2012]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[#123617]/70 dark:text-[#EAF3EA]/70">
                        <th class="px-5 py-3">Producto</th>
                        <th class="px-5 py-3">Precio</th>
                        <th class="px-5 py-3">Cantidad</th>
                        <th class="px-5 py-3">Subtotal</th>
                        <th class="px-5 py-3">Acción</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-[#DDEEDD] dark:divide-[#16351F]">
                    @forelse($items as $it)
                        @php
                            $price = (int)($it['price'] ?? 0);
                            $qty = (int)($it['qty'] ?? 1);
                            $sub = $price * $qty;
                            $stock = (int)($it['stock'] ?? 0);
                            $fallback = asset('assets/img/no_image.png');
                            $img = $it['image'] ?? $fallback;
                        @endphp

                        <tr class="hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl overflow-hidden
                                                border border-[#DDEEDD] dark:border-[#16351F]
                                                bg-white dark:bg-[#07120A]">
                                        <img src="{{ $img }}"
                                             alt="{{ $it['name'] }}"
                                             class="w-full h-full object-cover"
                                             loading="lazy"
                                             onerror="this.onerror=null;this.src='{{ $fallback }}';">
                                    </div>

                                    <div class="min-w-0">
                                        <div class="font-semibold text-[#123617] dark:text-[#EAF3EA] truncate max-w-[340px]">
                                            {{ $it['name'] }}
                                        </div>
                                        <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                            Stock: {{ $stock }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 whitespace-nowrap">
                                $ {{ number_format($price, 0, ',', '.') }}
                            </td>

                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('tienda.carrito.update') }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ (int)$it['id'] }}">
                                    <input type="number"
                                           name="qty"
                                           min="1"
                                           max="{{ $stock }}"
                                           value="{{ $qty }}"
                                           class="w-24 rounded-xl px-3 py-2 text-sm
                                                  border border-[#DDEEDD] dark:border-[#16351F]
                                                  bg-white/90 dark:bg-[#07120A]
                                                  text-[#123617] dark:text-[#EAF3EA]">
                                    <button type="submit"
                                            class="rounded-xl px-3 py-2 text-sm font-semibold
                                                   bg-[#123617] text-white hover:opacity-90 transition">
                                        Actualizar
                                    </button>
                                </form>
                            </td>

                            <td class="px-5 py-4 font-semibold whitespace-nowrap">
                                $ {{ number_format($sub, 0, ',', '.') }}
                            </td>

                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('tienda.carrito.remove', $it['id']) }}">
                                    @csrf
                                    <button type="submit"
                                            class="rounded-xl px-3 py-2 text-sm font-semibold
                                                   border border-[#DDEEDD] dark:border-[#16351F]
                                                   text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition">
                                        Quitar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                Tu carrito está vacío.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if(count($items))
                    <tfoot>
                        <tr class="bg-[#F6FBF4] dark:bg-[#07120A]/40">
                            <td class="px-5 py-4 font-semibold" colspan="3">Total</td>
                            <td class="px-5 py-4 font-bold whitespace-nowrap">
                                $ {{ number_format((int)$subtotal, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        <div class="md:hidden divide-y divide-[#DDEEDD] dark:divide-[#16351F]">
            @forelse($items as $it)
                @php
                    $price = (int)($it['price'] ?? 0);
                    $qty = (int)($it['qty'] ?? 1);
                    $sub = $price * $qty;
                    $stock = (int)($it['stock'] ?? 0);
                    $fallback = asset('assets/img/no_image.png');
                    $img = $it['image'] ?? $fallback;
                @endphp

                <div class="p-4">
                    <div class="flex gap-3">
                        <div class="w-14 h-14 rounded-2xl overflow-hidden shrink-0
                                    border border-[#DDEEDD] dark:border-[#16351F]
                                    bg-white dark:bg-[#07120A]">
                            <img src="{{ $img }}"
                                 alt="{{ $it['name'] }}"
                                 class="w-full h-full object-cover"
                                 loading="lazy"
                                 onerror="this.onerror=null;this.src='{{ $fallback }}';">
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="font-semibold text-[#123617] dark:text-[#EAF3EA] truncate">
                                {{ $it['name'] }}
                            </div>

                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs
                                             border border-[#CFE6C9] dark:border-[#1B3B22]
                                             bg-[#F6FBF4] dark:bg-[#07120A]
                                             text-[#123617] dark:text-[#EAF3EA]">
                                    $ {{ number_format($price, 0, ',', '.') }}
                                </span>

                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs
                                             border border-[#CFE6C9] dark:border-[#1B3B22]
                                             bg-white/70 dark:bg-white/5
                                             text-[#123617] dark:text-[#EAF3EA]">
                                    Stock: {{ $stock }}
                                </span>

                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                             border border-[#DDEEDD] dark:border-[#16351F]
                                             bg-[#EAF6E7] dark:bg-[#0A2012]
                                             text-[#123617] dark:text-[#EAF3EA]">
                                    Subtotal: $ {{ number_format($sub, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="mt-3 grid grid-cols-1 gap-2">
                                <form method="POST" action="{{ route('tienda.carrito.update') }}"
                                      class="grid grid-cols-[1fr_auto] gap-2 items-center">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ (int)$it['id'] }}">

                                    <input type="number"
                                           name="qty"
                                           min="1"
                                           max="{{ $stock }}"
                                           value="{{ $qty }}"
                                           class="w-full rounded-xl px-3 py-2 text-sm
                                                  border border-[#DDEEDD] dark:border-[#16351F]
                                                  bg-white/90 dark:bg-[#07120A]
                                                  text-[#123617] dark:text-[#EAF3EA]">

                                    <button type="submit"
                                            class="rounded-xl px-3 py-2 text-sm font-semibold
                                                   bg-[#123617] text-white hover:opacity-90 transition whitespace-nowrap">
                                        Actualizar
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('tienda.carrito.remove', $it['id']) }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full rounded-xl px-3 py-2 text-sm font-semibold
                                                   border border-[#DDEEDD] dark:border-[#16351F]
                                                   text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10 transition">
                                        Quitar del carrito
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                    Tu carrito está vacío.
                </div>
            @endforelse

            @if(count($items))
                <div class="p-4 bg-[#F6FBF4] dark:bg-[#07120A]/40">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-[#123617] dark:text-[#EAF3EA]">Total</span>
                        <span class="font-bold text-[#123617] dark:text-[#EAF3EA]">
                            $ {{ number_format((int)$subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @endif
        </div>

    </div>

@php
    // Map horarios por id para validación front (sin pedir otro endpoint)
    $horariosById = [];
    foreach (($puntosEncuentro ?? []) as $pp) {
        $horariosById[(string)$pp->id] = $pp->horario_semanal ?? [];
    }
@endphp

@if(count($items))
    <div class="rounded-2xl
                bg-white/90 dark:bg-[#0B1A10]/80
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.45)]
                p-4 sm:p-6 space-y-4">

        <div>
            <h2 class="text-lg font-semibold text-[#123617] dark:text-[#EAF3EA]">
                Datos para coordinar entrega
            </h2>
            <p class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                El pedido quedará <b>pendiente de aprobación</b>.
            </p>
        </div>

        <form method="POST"
              action="{{ route('tienda.carrito.checkout') }}"
              enctype="multipart/form-data"
              class="space-y-4"
              id="checkoutForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold mb-1 text-[#123617] dark:text-[#EAF3EA]">
                        Punto de encuentro
                    </label>

                    <select name="punto_encuentro_id"
                            id="punto_encuentro_id"
                            required
                            class="w-full rounded-xl px-3 py-2 text-sm
                                   border border-[#DDEEDD] dark:border-[#16351F]
                                   bg-white/90 dark:bg-[#07120A]
                                   text-[#123617] dark:text-[#EAF3EA]">
                        <option value="" disabled {{ old('punto_encuentro_id') ? '' : 'selected' }}>
                            Selecciona un punto...
                        </option>

                        @foreach(($puntosEncuentro ?? []) as $p)
                            <option value="{{ $p->id }}"
                                {{ (string)old('punto_encuentro_id') === (string)$p->id ? 'selected' : '' }}>
                                {{ $p->nombre }}@if(!empty($p->direccion)) — {{ $p->direccion }}@endif
                            </option>
                        @endforeach
                    </select>

                    @error('punto_encuentro_id')
                        <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                    @enderror

                    <div id="horarioHelp"
                         class="mt-2 text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60"></div>

                    <div id="horarioClientError"
                         class="mt-2 hidden text-xs font-semibold text-red-700 bg-red-50 border border-red-200 px-3 py-2 rounded-xl">
                        La fecha/hora elegida no calza con el horario del punto. Elige otra hora o cambia de punto.
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold mb-1 text-[#123617] dark:text-[#EAF3EA]">
                        Fecha y hora estimada
                    </label>

                    <input type="text"
                           name="hora_estimada_cliente"
                           id="hora_estimada_cliente"
                           required
                           value="{{ old('hora_estimada_cliente') }}"
                           placeholder="Selecciona fecha y hora…"
                           autocomplete="off"
                           class="w-full rounded-xl px-3 py-2 text-sm
                                  border border-[#DDEEDD] dark:border-[#16351F]
                                  bg-white/90 dark:bg-[#07120A]
                                  text-[#123617] dark:text-[#EAF3EA]">

                    @error('hora_estimada_cliente')
                        <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-[#123617] dark:text-[#EAF3EA]">
                    Comprobante de compra (obligatorio)
                </label>

                <div class="rounded-2xl border border-[#DDEEDD] dark:border-[#16351F]
                            bg-white/80 dark:bg-[#07120A]/60 p-4">
                    <input type="file"
                           name="comprobante"
                           id="comprobante"
                           accept="image/*"
                           required
                           class="block w-full text-sm
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-xl file:border-0
                                  file:bg-[#123617] file:text-white
                                  hover:file:opacity-90
                                  transition">

                    <div class="mt-3 hidden" id="compPreviewWrap">
                        <div class="text-xs font-semibold text-[#123617] dark:text-[#EAF3EA] mb-2">
                            Vista previa
                        </div>
                        <img id="compPreviewImg"
                             alt="Comprobante"
                             class="w-44 h-44 object-cover rounded-2xl
                                    border border-[#DDEEDD] dark:border-[#16351F]
                                    bg-white dark:bg-[#07120A]">
                    </div>
                </div>

                @error('comprobante')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1 text-[#123617] dark:text-[#EAF3EA]">
                    Mensaje
                </label>

                <textarea name="mensaje_cliente"
                          rows="3"
                          class="w-full rounded-xl px-3 py-2 text-sm
                                 border border-[#DDEEDD] dark:border-[#16351F]
                                 bg-white/90 dark:bg-[#07120A]
                                 text-[#123617] dark:text-[#EAF3EA]"
                          placeholder="Ej: llego en auto, prefiero tal punto, etc.">{{ old('mensaje_cliente') }}</textarea>

                @error('mensaje_cliente')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2">
                <button type="submit"
                        id="btnCheckout"
                        class="w-full sm:w-auto rounded-2xl px-5 py-3 text-sm font-semibold
                               bg-[#123617] text-white hover:opacity-90 transition">
                    Crear pedido (pendiente de aprobación)
                </button>
            </div>
        </form>

    </div>

    <div id="checkoutLoader"
         class="fixed inset-0 z-[9999] hidden items-center justify-center px-6">
        <div class="absolute inset-0 bg-black/45 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-md rounded-2xl
                    border border-white/10
                    bg-white/95
                    shadow-[0px_18px_60px_-20px_rgba(0,0,0,0.55)]
                    p-6">
            <div class="flex items-start gap-4">
                <div class="shrink-0">
                    <div class="h-12 w-12 rounded-2xl grid place-items-center
                                bg-[#123617]/10
                                border border-[#123617]/15">
                        <svg class="h-6 w-6 animate-spin text-[#123617]"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                    </div>
                </div>

                <div class="min-w-0 w-full">
                    <div class="text-base font-semibold" style="color:#000 !important;">
                        Creando tu pedido…
                    </div>

                    <div class="mt-1 text-sm" style="color:#000 !important; opacity:0.85;">
                        Estamos guardando el pedido y enviando el correo al admin. No cierres esta pestaña.
                    </div>

                    <div class="mt-3 text-xs font-semibold" id="loaderStep"
                         style="color:#000 !important;">
                        Iniciando…
                    </div>

                    <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-black/10">
                        <div id="loaderBar"
                             class="h-full w-0 rounded-full bg-[#123617] transition-[width] duration-500"></div>
                    </div>

                    <div class="mt-2 text-xs" id="loaderPct"
                         style="color:#000 !important; opacity:0.7;">
                        0%
                    </div>
                </div>
            </div>

            <button type="button"
                    id="btnForceReload"
                    class="mt-5 hidden w-full rounded-xl px-4 py-2 text-sm font-semibold
                           border border-black/10
                           text-black
                           hover:bg-black/5 transition">
                Si se quedó pegado, recargar
            </button>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const horariosById = @json($horariosById);

  const selectPunto = document.getElementById('punto_encuentro_id');
  const dtInput     = document.getElementById('hora_estimada_cliente');
  const helpEl      = document.getElementById('horarioHelp');
  const errEl       = document.getElementById('horarioClientError');

  const form      = document.getElementById('checkoutForm');
  const loader    = document.getElementById('checkoutLoader');
  const btn       = document.getElementById('btnCheckout');
  const btnReload = document.getElementById('btnForceReload');

  const stepEl = document.getElementById('loaderStep');
  const barEl  = document.getElementById('loaderBar');
  const pctEl  = document.getElementById('loaderPct');

  const hideHorarioError = () => errEl?.classList.add('hidden');
  const showHorarioError = () => errEl?.classList.remove('hidden');

  const dayNames = { mon:'Lun', tue:'Mar', wed:'Mié', thu:'Jue', fri:'Vie', sat:'Sáb', sun:'Dom' };
  const dayOrder = ['mon','tue','wed','thu','fri','sat','sun'];

  function pad2(n){ return String(n).padStart(2,'0'); }

  function formatHorarioText(horario) {
    if (!horario || typeof horario !== 'object') return '';
    const parts = [];
    dayOrder.forEach(day => {
      const ranges = horario[day];
      if (!Array.isArray(ranges) || !ranges.length) return;
      const rParts = ranges
        .filter(r => r && r.from && r.to && String(r.from) < String(r.to))
        .map(r => `${r.from}-${r.to}`);
      if (!rParts.length) return;
      parts.push(`${dayNames[day] || day} ${rParts.join(', ')}`);
    });
    return parts.join(' | ');
  }

  // dtInput.value siempre será "Y-m-d H:i" (dateFormat)
  function parseFlatpickrToDate(value) {
    const v = (value || '').trim();
    if (!v) return null;
    const m = v.match(/^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2})$/);
    if (!m) return null;

    const y  = parseInt(m[1], 10);
    const mo = parseInt(m[2], 10) - 1;
    const d  = parseInt(m[3], 10);
    const hh = parseInt(m[4], 10);
    const mm = parseInt(m[5], 10);

    const dt = new Date(y, mo, d, hh, mm, 0, 0);
    if (Number.isNaN(dt.getTime())) return null;
    return dt;
  }

  function getDayKey(dateObj) {
    // JS: 0=Dom ... 6=Sáb
    const js = dateObj.getDay();
    return ['sun','mon','tue','wed','thu','fri','sat'][js] || null;
  }

  function normalizeRanges(ranges) {
    if (!Array.isArray(ranges)) return [];
    return ranges
      .filter(r => r && r.from && r.to && String(r.from) < String(r.to))
      .map(r => ({ from: String(r.from), to: String(r.to) }))
      .sort((a,b) => a.from.localeCompare(b.from));
  }

  function isDayEnabled(horario, dateObj) {
    if (!horario || typeof horario !== 'object') return false;
    const dayKey = getDayKey(dateObj);
    if (!dayKey) return false;
    return normalizeRanges(horario[dayKey]).length > 0;
  }

  // ✅ valida hora (múltiples rangos, incluye almuerzo)
  // regla: [from, to) => incluye from, excluye to
  function isWithinAnyRange(horario, dateObj) {
    if (!horario || typeof horario !== 'object') return false;

    const dayKey = getDayKey(dateObj);
    if (!dayKey) return false;

    const ranges = normalizeRanges(horario[dayKey]);
    if (!ranges.length) return false;

    const time = `${pad2(dateObj.getHours())}:${pad2(dateObj.getMinutes())}`;
    return ranges.some(r => time >= r.from && time < r.to);
  }

  // ✅ si eligió una hora inválida, la corrige al próximo rango
  // - si cae en el almuerzo (entre rangos), salta al inicio del siguiente rango
  // - si es muy tarde (después de todos), salta al primer rango del día (mismo día)
  function coerceToNearestAllowed(horario, dateObj) {
    if (!horario || typeof horario !== 'object') return null;

    const dayKey = getDayKey(dateObj);
    if (!dayKey) return null;

    const ranges = normalizeRanges(horario[dayKey]);
    if (!ranges.length) return null;

    const time = `${pad2(dateObj.getHours())}:${pad2(dateObj.getMinutes())}`;

    // ya ok
    if (ranges.some(r => time >= r.from && time < r.to)) return dateObj;

    // si está antes de algún rango, tomar ese
    const next = ranges.find(r => time < r.from);
    const pick = next || ranges[0];

    const [hh, mm] = pick.from.split(':').map(x => parseInt(x, 10));
    return new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate(), hh, mm, 0, 0);
  }

function escapeHtml(str) {
  return String(str ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function updateHelp() {
  const puntoId = (selectPunto?.value || '').trim();
  const horario = horariosById[puntoId] || null;

  if (!helpEl) return;

  if (!puntoId) {
    helpEl.innerHTML = '';
    return;
  }

  if (!horario || Object.keys(horario).length === 0) {
    helpEl.innerHTML = `
      <div class="mt-2 rounded-xl border border-red-200
                  bg-red-50 dark:bg-red-900/20
                  px-4 py-3 text-sm
                  text-red-700 dark:text-red-300">
        Este punto no tiene horarios disponibles.
      </div>
    `;
    return;
  }

  const txt = formatHorarioText(horario);

  if (!txt) {
    helpEl.innerHTML = `
      <div class="mt-2 rounded-xl border border-red-200
                  bg-red-50 dark:bg-red-900/20
                  px-4 py-3 text-sm
                  text-red-700 dark:text-red-300">
        Este punto no tiene horarios disponibles.
      </div>
    `;
    return;
  }

  const days = txt.split('|').map(s => s.trim()).filter(Boolean);

  helpEl.innerHTML = `
    <div class="mt-2 rounded-xl border border-[#DDEEDD] dark:border-[#16351F]
                bg-white/70 dark:bg-white/5
                px-4 py-3">

      <div class="flex items-center gap-2">
        <span class="inline-flex h-2 w-2 rounded-full bg-[#123617]"></span>
        <div class="text-xs font-semibold text-[#123617] dark:text-[#EAF3EA]">
          Horario disponible
        </div>
      </div>

      <div class="mt-3 space-y-2">
        ${days.map(line => {
          const firstSpace = line.indexOf(' ');
          const dayLabel = firstSpace > 0 ? line.slice(0, firstSpace).trim() : line;
          const rest = firstSpace > 0 ? line.slice(firstSpace + 1).trim() : '';

          const ranges = rest
            .split(',')
            .map(r => r.trim())
            .filter(Boolean);

          return `
            <div class="flex flex-col sm:flex-row sm:items-center gap-2">
              <span class="inline-flex w-fit sm:w-[52px] justify-center
                           rounded-lg px-2.5 py-1 text-xs font-semibold
                           bg-[#F6FBF4] dark:bg-[#07120A]/40
                           border border-[#DDEEDD] dark:border-[#16351F]
                           text-[#123617] dark:text-[#EAF3EA]">
                ${escapeHtml(dayLabel)}
              </span>

              <div class="flex flex-wrap gap-2">
                ${ranges.map(r => `
                  <span class="inline-flex items-center
                               rounded-lg px-3 py-1.5
                               text-[15px] font-extrabold tracking-wide
                               border border-[#123617]/20 dark:border-[#92b95d]/20
                               bg-[#123617]/10 dark:bg-[#92b95d]/10
                               text-[#123617] dark:text-[#EAF3EA]
                               shadow-[0px_6px_16px_-12px_rgba(18,54,23,0.55)]">
                    ${escapeHtml(r)}
                  </span>
                `).join('')}
              </div>
            </div>
          `;
        }).join('')}
      </div>

      <div class="mt-4 relative overflow-hidden rounded-2xl
                  border border-red-300 dark:border-red-800/60
                  bg-red-50 dark:bg-red-900/25
                  px-4 py-4">

        <div class="absolute -top-10 -right-10 h-28 w-28 rounded-full
                    bg-red-500/20 blur-2xl"></div>

        <div class="flex items-start gap-3 relative z-10">

          <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center
                       rounded-xl bg-red-600 text-white text-lg
                       shadow-lg">
            ⚠️
          </span>

          <div class="min-w-0">
            <div class="inline-flex items-center rounded-full px-3 py-1
                        text-[11px] font-extrabold tracking-wide
                        bg-red-600 text-white">
              NOTA IMPORTANTE
            </div>

            <div class="mt-2 text-sm font-bold text-red-800 dark:text-red-200 leading-snug">
              Si escribes una hora fuera de horario, se te pondrá una advertencia.
            </div>

          </div>

        </div>
      </div>

    </div>
  `;
}

  // ✅ valida día + hora (no solo el día)
  function validateHorarioSelection(showError = false) {
    hideHorarioError();
    updateHelp();

    const puntoId = (selectPunto?.value || '').trim();
    const horario = horariosById[puntoId] || null;

    const dt = parseFlatpickrToDate(dtInput?.value || '');
    if (!puntoId || !dt) return true; // incompleto

    const ok = isWithinAnyRange(horario, dt);
    if (!ok && showError) showHorarioError();
    return ok;
  }

  // ✅ flatpickr: bloquear días sin rango + corregir hora al seleccionar/tipear
  let fp = null;

  function buildDisableFn() {
    return (date) => {
      const puntoId = (selectPunto?.value || '').trim();
      const horario = horariosById[puntoId] || null;
      if (!puntoId || !horario) return false;
      return !isDayEnabled(horario, date);
    };
  }

  // ✅ cuando el usuario TIPEA y presiona Enter (o sale del input),
  // forzamos parseo + ajuste al rango más cercano
  function commitManualDatetimeInput(showError = false) {
    hideHorarioError();
    updateHelp();

    const puntoId = (selectPunto?.value || '').trim();
    const horario = horariosById[puntoId] || null;

    if (!fp || !dtInput) return true;
    if (!puntoId || !horario) return true;

    // Lo que el usuario ve/tipea suele ser altInput (por altFormat)
    const typed = (fp.altInput ? fp.altInput.value : dtInput.value || '').trim();
    if (!typed) return true;

    // Intentamos parsear como altFormat, luego dateFormat
    let parsed = fp.parseDate(typed, fp.config.altFormat);
    if (!parsed) parsed = fp.parseDate(typed, fp.config.dateFormat);

    if (!parsed || Number.isNaN(parsed.getTime())) {
      fp.clear();
      if (showError) showHorarioError();
      return false;
    }

    // Si el día está deshabilitado, limpiamos
    if (!isDayEnabled(horario, parsed)) {
      fp.clear();
      if (showError) showHorarioError();
      return false;
    }

    // Ajuste de hora al rango permitido más cercano
    const fixed = coerceToNearestAllowed(horario, parsed);
    if (!fixed) {
      fp.clear();
      if (showError) showHorarioError();
      return false;
    }

    fp.setDate(fixed, true);

    const ok = isWithinAnyRange(horario, fixed);
    if (!ok && showError) showHorarioError();
    return ok;
  }

  function rebuildFlatpickr() {
    if (!dtInput) return;

    const prev = dtInput.value || '';

    if (fp) {
      fp.destroy();
      fp = null;
    }

    fp = flatpickr(dtInput, {
      enableTime: true,
      time_24hr: true,
      dateFormat: "Y-m-d H:i",
      altInput: true,
      altFormat: "d-m-Y H:i",
      allowInput: true,
      minuteIncrement: 5,
      minDate: "today",
      disable: [buildDisableFn()],

      onOpen: () => hideHorarioError(),

      // ✅ al cambiar (click en calendario): si la hora no calza, la ajusta
      onChange: (selectedDates) => {
        hideHorarioError();
        updateHelp();

        const puntoId = (selectPunto?.value || '').trim();
        const horario = horariosById[puntoId] || null;
        const d = selectedDates && selectedDates[0] ? selectedDates[0] : null;
        if (!puntoId || !horario || !d) return;

        const fixed = coerceToNearestAllowed(horario, d);
        if (fixed && fixed.getTime() !== d.getTime()) {
          fp.setDate(fixed, true);
        }
      },

      // ✅ al cerrar: commit (por si quedó texto escrito)
      onClose: () => {
        commitManualDatetimeInput(false);
      }
    });

    if (prev) fp.setDate(prev, true);

    // ✅ Interceptar Enter y blur sobre el input visible (altInput)
    const inputEl = fp.altInput || dtInput;

    inputEl.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        commitManualDatetimeInput(true);
      }
    });

    inputEl.addEventListener('blur', () => {
      commitManualDatetimeInput(false);
    });
  }

  rebuildFlatpickr();
  updateHelp();

  // cambio punto => rearmar calendario + corregir hora si queda inválida
  selectPunto?.addEventListener('change', () => {
    hideHorarioError();
    rebuildFlatpickr();
    updateHelp();

    const puntoId = (selectPunto?.value || '').trim();
    const horario = horariosById[puntoId] || null;

    if (fp && puntoId && horario) {
      const current = fp.selectedDates && fp.selectedDates[0] ? fp.selectedDates[0] : null;
      if (current) {
        const fixed = coerceToNearestAllowed(horario, current);
        if (fixed) fp.setDate(fixed, true);
      }
    }

    validateHorarioSelection(false);
  });

  // si el usuario escribe manualmente: validación suave
  dtInput?.addEventListener('input', () => validateHorarioSelection(false));

  // preview comprobante
  const fileInput = document.getElementById('comprobante');
  const wrap = document.getElementById('compPreviewWrap');
  const img  = document.getElementById('compPreviewImg');

  if (fileInput && wrap && img) {
    fileInput.addEventListener('change', () => {
      const file = fileInput.files && fileInput.files[0];
      if (!file) {
        wrap.classList.add('hidden');
        img.removeAttribute('src');
        return;
      }
      const url = URL.createObjectURL(file);
      img.src = url;
      wrap.classList.remove('hidden');
      img.onload = () => URL.revokeObjectURL(url);
    });
  }

  // loader helpers
  function setProgress(pct, text) {
    if (barEl) barEl.style.width = `${pct}%`;
    if (pctEl) pctEl.textContent = `${pct}%`;
    if (stepEl && text) stepEl.textContent = text;
  }

  function cleanupTimers() {
    if (window.__checkoutTimers) {
      window.__checkoutTimers.forEach(clearTimeout);
      window.__checkoutTimers = null;
    }
    if (window.__checkoutStuckTimer) {
      clearTimeout(window.__checkoutStuckTimer);
      window.__checkoutStuckTimer = null;
    }
  }

  function showLoaderWithSteps() {
    if (btnReload) btnReload.classList.add('hidden');
    setProgress(0, 'Iniciando…');

    loader.classList.remove('hidden');
    loader.classList.add('flex');
    document.documentElement.classList.add('overflow-hidden');
    document.body.classList.add('overflow-hidden');

    if (btn) {
      btn.disabled = true;
      btn.classList.add('opacity-70','cursor-not-allowed');
    }

    const steps = [
      { t: 150,  p: 8,  msg: 'Validando datos…' },
      { t: 600,  p: 18, msg: 'Preparando comprobante…' },
      { t: 1400, p: 40, msg: 'Subiendo comprobante…' },
      { t: 2600, p: 65, msg: 'Creando pedido…' },
      { t: 3800, p: 82, msg: 'Enviando correo al admin…' },
      { t: 5400, p: 95, msg: 'Finalizando…' },
    ];

    window.__checkoutTimers = steps.map(s =>
      setTimeout(() => setProgress(s.p, s.msg), s.t)
    );

    window.__checkoutStuckTimer = setTimeout(() => {
      if (btnReload) btnReload.classList.remove('hidden');
    }, 20000);
  }

  function hideLoader() {
    cleanupTimers();
    loader.classList.add('hidden');
    loader.classList.remove('flex');
    document.documentElement.classList.remove('overflow-hidden');
    document.body.classList.remove('overflow-hidden');
    if (btn) {
      btn.disabled = false;
      btn.classList.remove('opacity-70','cursor-not-allowed');
    }
    setProgress(0, 'Iniciando…');
  }

  // submit => valida hora + rango (front)
  if (form && loader) {
    form.addEventListener('submit', (e) => {
      if (form.checkValidity && !form.checkValidity()) return;

      // ✅ Commit final de lo tipeado antes de validar
      const committedOk = commitManualDatetimeInput(true);
      if (!committedOk) {
        e.preventDefault();
        const target = (fp && (fp.altInput || dtInput)) || dtInput || selectPunto || form;
        target?.scrollIntoView?.({ behavior: 'smooth', block: 'center' });
        return;
      }

      // ✅ validación FINAL
      const okHorario = validateHorarioSelection(true);
      if (!okHorario) {
        e.preventDefault();
        const target = (fp && (fp.altInput || dtInput)) || dtInput || selectPunto || form;
        target?.scrollIntoView?.({ behavior: 'smooth', block: 'center' });
        return;
      }

      cleanupTimers();
      showLoaderWithSteps();
    });

    window.addEventListener('pageshow', (e) => {
      if (e.persisted) hideLoader();
    });

    if (btnReload) {
      btnReload.addEventListener('click', () => window.location.reload());
    }
  }

  // inicial (si viene con old())
  validateHorarioSelection(false);
});
</script>
@endif

</div>
@endsection