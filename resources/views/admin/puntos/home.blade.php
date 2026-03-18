@extends('layouts.app')

@section('content')
@php
    $puntos = $puntos ?? collect();
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-semibold text-[#123617] dark:text-[#92b95d]">
                Puntos de encuentro
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Crea y administra los puntos disponibles para coordinar entregas.
            </p>
        </div>

        <button type="button" id="btnNew"
                class="w-full sm:w-auto rounded-2xl px-4 py-2 text-sm font-semibold bg-[#123617] text-white hover:opacity-90">
            + Nuevo punto
        </button>
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

    @if ($errors->any())
        <div class="rounded-2xl px-5 py-3 border border-red-200 bg-red-50 text-red-800">
            <div class="font-semibold mb-1">Revisa lo siguiente:</div>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden bg-white/90 dark:bg-[#0B1A10]/80 border border-[#DDEEDD] dark:border-[#16351F] shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03)]">
        <div class="p-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
            <div class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Total:
                <span class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $puntos->count() }}</span>
            </div>

            <div class="w-full sm:w-auto">
                <input id="q" type="text" placeholder="Buscar por nombre o dirección..."
                       class="w-full sm:w-80 rounded-2xl px-4 py-2 text-sm border border-[#DDEEDD] dark:border-[#16351F]
                              bg-white/90 dark:bg-[#07120A]/55 text-[#123617] dark:text-[#EAF3EA]
                              focus:outline-none focus:ring-2 focus:ring-[#92b95d]/40">
            </div>
        </div>

        <div id="list" class="p-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($puntos as $p)
                @php
                    $label = trim(($p->nombre ?? '') . (!empty($p->direccion) ? ' — '.$p->direccion : ''));
                    $blob = mb_strtolower(trim(($p->nombre ?? '').' '.($p->direccion ?? '').' '.($p->descripcion ?? '')));
                    $lat = $p->lat ?? null;
                    $lng = $p->lng ?? null;
                    $horario = $p->horario_semanal ?? [];
                @endphp

                <div class="group rounded-2xl p-5 bg-white dark:bg-[#0B1A10] border border-[#DDEEDD] dark:border-[#16351F]
                            shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                     data-search="{{ $blob }}">

                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-[#123617] dark:text-[#EAF3EA] text-sm sm:text-base leading-snug">
                                {{ $label ?: ('Punto #'.$p->id) }}
                            </h3>

                            <span class="text-[10px] px-2 py-1 rounded-full font-bold {{ $p->activo ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                                {{ $p->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2 text-[11px]">
                            <span class="px-2 py-1 rounded-full bg-[#123617]/10 text-[#123617] dark:bg-white/10 dark:text-[#EAF3EA]">
                                Orden: {{ (int)($p->orden ?? 0) }}
                            </span>

                            @if(!is_null($lat) && !is_null($lng))
                                <span class="px-2 py-1 rounded-full bg-blue-600/10 text-blue-700 dark:bg-white/10 dark:text-[#EAF3EA] break-all">
                                    📍 {{ $lat }}, {{ $lng }}
                                </span>
                            @endif
                        </div>

                        @if(!empty($p->descripcion))
                            <p class="mt-3 text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 line-clamp-3">
                                {{ $p->descripcion }}
                            </p>
                        @endif
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <button type="button"
                                class="btnEdit flex-1 rounded-xl px-3 py-2 text-xs font-semibold border border-[#DDEEDD] dark:border-[#16351F]
                                       bg-white dark:bg-[#07120A]/55 hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/75 transition"
                                data-id="{{ $p->id }}"
                                data-nombre="{{ e($p->nombre ?? '') }}"
                                data-direccion="{{ e($p->direccion ?? '') }}"
                                data-descripcion="{{ e($p->descripcion ?? '') }}"
                                data-orden="{{ (int)($p->orden ?? 0) }}"
                                data-activo="{{ $p->activo ? '1' : '0' }}"
                                data-lat="{{ !is_null($lat) ? e($lat) : '' }}"
                                data-lng="{{ !is_null($lng) ? e($lng) : '' }}"
                                data-horario='@json($horario)'>
                            Editar
                        </button>

                        <form method="POST" action="{{ route('admin.puntos.toggle', $p) }}" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full rounded-xl px-3 py-2 text-xs font-semibold {{ $p->activo ? 'bg-yellow-500 text-[#07120A]' : 'bg-green-600 text-white' }}
                                           hover:opacity-90 transition">
                                {{ $p->activo ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.puntos.destroy', $p) }}" class="w-full"
                              onsubmit="return confirm('¿Eliminar este punto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full rounded-xl px-3 py-2 text-xs font-semibold bg-red-600 text-white hover:opacity-90 transition">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-10 text-center text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                    Aún no tienes puntos de encuentro.
                </div>
            @endforelse
        </div>
    </div>
</div>

<div id="modal" class="hidden fixed inset-0 z-50" aria-hidden="true">
    <div id="modalOverlay" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    <div class="relative w-full h-full flex items-center justify-center p-3 sm:p-4">
        <div role="dialog" aria-modal="true"
             class="w-full max-w-3xl rounded-2xl bg-white dark:bg-[#0B1A10] border border-[#DDEEDD] dark:border-[#16351F] shadow-2xl overflow-hidden">

            <div class="p-4 flex items-center justify-between border-b border-[#DDEEDD] dark:border-[#16351F]">
                <div class="font-semibold text-[#123617] dark:text-[#EAF3EA]" id="modalTitle">Nuevo punto</div>
                <button type="button" id="btnClose"
                        class="rounded-xl px-3 py-1.5 text-xs font-semibold border border-[#DDEEDD] dark:border-[#16351F]
                               text-[#123617] dark:text-[#EAF3EA] hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/75">
                    Cerrar
                </button>
            </div>

            <form method="POST" id="form" action="{{ route('admin.puntos.store') }}" class="p-4 space-y-3 max-h-[82vh] overflow-auto">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">

                <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
                <input type="hidden" name="lng" id="lng" value="{{ old('lng') }}">

                <div class="relative">
                    <label class="block text-xs font-semibold mb-1 text-[#123617] dark:text-[#EAF3EA]">
                        Buscar en el mapa (dirección o lugar)
                    </label>

                    <input id="geoSearch" type="text" autocomplete="off"
                           placeholder="Ej: Santa Isabel Fundo el Carmen, Temuco"
                           class="w-full rounded-xl px-3 py-2 text-sm border border-[#DDEEDD] dark:border-[#16351F]
                                  bg-white/90 dark:bg-[#07120A] text-[#123617] dark:text-[#EAF3EA]
                                  focus:outline-none focus:ring-2 focus:ring-[#92b95d]/40">

                    <div id="geoResults"
                         class="hidden absolute z-50 mt-2 w-full rounded-xl overflow-hidden border border-[#DDEEDD] dark:border-[#16351F]
                                bg-white dark:bg-[#0B1A10] shadow-xl">
                        <div id="geoResultsInner" class="max-h-64 overflow-auto divide-y divide-[#DDEEDD] dark:divide-[#16351F]"></div>
                    </div>

                    <div class="mt-1 text-[11px] text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                        Tip: al seleccionar un resultado se marca el mapa y se rellena dirección/coords.
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-[#123617] dark:text-[#EAF3EA]">Nombre</label>
                        <input name="nombre" id="nombre" required
                               class="w-full rounded-xl px-3 py-2 text-sm border border-[#DDEEDD] dark:border-[#16351F]
                                      bg-white/90 dark:bg-[#07120A] text-[#123617] dark:text-[#EAF3EA]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1 text-[#123617] dark:text-[#EAF3EA]">Dirección (opcional)</label>
                        <input name="direccion" id="direccion"
                               class="w-full rounded-xl px-3 py-2 text-sm border border-[#DDEEDD] dark:border-[#16351F]
                                      bg-white/90 dark:bg-[#07120A] text-[#123617] dark:text-[#EAF3EA]">
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-[#123617] dark:text-[#EAF3EA]">Descripción (opcional)</label>
                        <textarea name="descripcion" id="descripcion" rows="4"
                                  class="w-full rounded-xl px-3 py-2 text-sm border border-[#DDEEDD] dark:border-[#16351F]
                                         bg-white/90 dark:bg-[#07120A] text-[#123617] dark:text-[#EAF3EA]"></textarea>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-3">
                            <label class="block text-xs font-semibold text-[#123617] dark:text-[#EAF3EA]">Ubicación en mapa</label>
                            <div class="text-[11px] text-[#3b6a33]/70 dark:text-[#EAF3EA]/60" id="coordsHint">
                                Click en el mapa para marcar el punto.
                            </div>
                        </div>

                        <div class="rounded-2xl overflow-hidden border border-[#DDEEDD] dark:border-[#16351F]">
                            <div id="map" class="w-full h-[220px] sm:h-[260px] md:h-[300px]"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" id="latView" readonly placeholder="Lat"
                                   class="w-full rounded-xl px-3 py-2 text-xs border border-[#DDEEDD] dark:border-[#16351F]
                                          bg-white/80 dark:bg-[#07120A]/70 text-[#123617] dark:text-[#EAF3EA]">
                            <input type="text" id="lngView" readonly placeholder="Lng"
                                   class="w-full rounded-xl px-3 py-2 text-xs border border-[#DDEEDD] dark:border-[#16351F]
                                          bg-white/80 dark:bg-[#07120A]/70 text-[#123617] dark:text-[#EAF3EA]">
                        </div>

                        <button type="button" id="btnClearMap"
                                class="w-full rounded-xl px-3 py-2 text-xs font-semibold border border-[#DDEEDD] dark:border-[#16351F]
                                       bg-white/80 dark:bg-[#07120A]/55 text-[#123617] dark:text-[#EAF3EA]
                                       hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/75">
                            Quitar marcador
                        </button>
                    </div>
                </div>

                @php
                    $days = [
                        'mon' => 'Lunes',
                        'tue' => 'Martes',
                        'wed' => 'Miércoles',
                        'thu' => 'Jueves',
                        'fri' => 'Viernes',
                        'sat' => 'Sábado',
                        'sun' => 'Domingo',
                    ];
                @endphp

                <div class="rounded-2xl border border-[#DDEEDD] dark:border-[#16351F] p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                                Horario de retiro <span class="text-red-600">*</span>
                            </div>
                            <div class="text-[11px] text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                Selecciona al menos 1 día y define 1 o 2 rangos (ideal para almuerzo).
                            </div>
                        </div>
                        <div id="horarioError"
                             class="hidden text-[11px] font-semibold text-red-700 bg-red-50 border border-red-200 px-3 py-1.5 rounded-xl">
                            Debes definir al menos un día con horario válido.
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($days as $k => $label)
                            <div class="rounded-xl border border-[#DDEEDD] dark:border-[#16351F] p-3">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox"
                                           class="dayToggle w-4 h-4 rounded border border-[#DDEEDD] dark:border-[#16351F]"
                                           data-day="{{ $k }}">
                                    <span class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $label }}</span>
                                </label>

                                <div class="mt-2 space-y-3 dayRanges" data-day="{{ $k }}" style="display:none;">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-[#3b6a33]/80 dark:text-[#EAF3EA]/70 mb-1">Desde</label>
                                            <input type="time"
                                                   name="horario[{{ $k }}][from]"
                                                   class="dayFrom w-full rounded-xl px-3 py-2 text-sm border border-[#DDEEDD] dark:border-[#16351F]
                                                          bg-white/90 dark:bg-[#07120A] text-[#123617] dark:text-[#EAF3EA]"
                                                   data-day="{{ $k }}" disabled>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-[#3b6a33]/80 dark:text-[#EAF3EA]/70 mb-1">Hasta</label>
                                            <input type="time"
                                                   name="horario[{{ $k }}][to]"
                                                   class="dayTo w-full rounded-xl px-3 py-2 text-sm border border-[#DDEEDD] dark:border-[#16351F]
                                                          bg-white/90 dark:bg-[#07120A] text-[#123617] dark:text-[#EAF3EA]"
                                                   data-day="{{ $k }}" disabled>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-[#3b6a33]/80 dark:text-[#EAF3EA]/70 mb-1">Desde (rango 2)</label>
                                            <input type="time"
                                                   name="horario[{{ $k }}][from2]"
                                                   class="dayFrom2 w-full rounded-xl px-3 py-2 text-sm border border-[#DDEEDD] dark:border-[#16351F]
                                                          bg-white/90 dark:bg-[#07120A] text-[#123617] dark:text-[#EAF3EA]"
                                                   data-day="{{ $k }}" disabled>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-semibold text-[#3b6a33]/80 dark:text-[#EAF3EA]/70 mb-1">Hasta (rango 2)</label>
                                            <input type="time"
                                                   name="horario[{{ $k }}][to2]"
                                                   class="dayTo2 w-full rounded-xl px-3 py-2 text-sm border border-[#DDEEDD] dark:border-[#16351F]
                                                          bg-white/90 dark:bg-[#07120A] text-[#123617] dark:text-[#EAF3EA]"
                                                   data-day="{{ $k }}" disabled>
                                        </div>
                                    </div>

                                    <div class="text-[11px] text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                        Si no necesitas almuerzo, deja el rango 2 vacío.
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-[#123617] dark:text-[#EAF3EA]">Orden</label>
                        <input type="number" name="orden" id="orden" min="0" value="0"
                               class="w-full rounded-xl px-3 py-2 text-sm border border-[#DDEEDD] dark:border-[#16351F]
                                      bg-white/90 dark:bg-[#07120A] text-[#123617] dark:text-[#EAF3EA]">
                    </div>

                    <div class="flex items-center gap-2 sm:pt-6">
                        <input type="checkbox" name="activo" id="activo" value="1" checked
                               class="w-4 h-4 rounded border border-[#DDEEDD] dark:border-[#16351F]">
                        <label for="activo" class="text-sm text-[#123617] dark:text-[#EAF3EA]">Activo</label>
                    </div>
                </div>

                <div class="sticky bottom-0 -mx-4 px-4 py-3 bg-white/95 dark:bg-[#0B1A10]/95 border-t border-[#DDEEDD] dark:border-[#16351F]">
                    <div class="flex flex-col sm:flex-row justify-end gap-2">
                        <button type="button" id="btnCancel"
                                class="w-full sm:w-auto rounded-xl px-4 py-2 text-sm font-semibold border border-[#DDEEDD] dark:border-[#16351F]
                                       text-[#123617] dark:text-[#EAF3EA] hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/75">
                            Cancelar
                        </button>

                        <button type="submit"
                                class="w-full sm:w-auto rounded-xl px-4 py-2 text-sm font-semibold bg-[#123617] text-white hover:opacity-90">
                            Guardar
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const puntosBaseUrl = @json(url('/admin/puntos-encuentro'));

    const modal = document.getElementById('modal');
    const overlay = document.getElementById('modalOverlay');
    const btnNew = document.getElementById('btnNew');
    const btnClose = document.getElementById('btnClose');
    const btnCancel = document.getElementById('btnCancel');
    const title = document.getElementById('modalTitle');

    const form = document.getElementById('form');
    const formMethod = document.getElementById('formMethod');

    const nombre = document.getElementById('nombre');
    const direccion = document.getElementById('direccion');
    const descripcion = document.getElementById('descripcion');
    const orden = document.getElementById('orden');
    const activo = document.getElementById('activo');

    const q = document.getElementById('q');
    const rows = Array.from(document.querySelectorAll('#list [data-search]'));

    const lat = document.getElementById('lat');
    const lng = document.getElementById('lng');
    const latView = document.getElementById('latView');
    const lngView = document.getElementById('lngView');
    const coordsHint = document.getElementById('coordsHint');
    const btnClearMap = document.getElementById('btnClearMap');

    const geoSearch = document.getElementById('geoSearch');
    const geoResults = document.getElementById('geoResults');
    const geoResultsInner = document.getElementById('geoResultsInner');

    const horarioError = document.getElementById('horarioError');
    const dayToggles = Array.from(document.querySelectorAll('.dayToggle'));

    let map = null;
    let marker = null;

    let searchTimer = null;
    let searchAbort = null;

    const openModal = () => {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        hideResults();
        hideHorarioError();
    };

    const escapeHtml = (s) => (s || '').replace(/[&<>"']/g, m => ({
        '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[m]));

    const hideResults = () => geoResults?.classList.add('hidden');
    const showResults = () => geoResults?.classList.remove('hidden');

    const showHorarioError = () => horarioError?.classList.remove('hidden');
    const hideHorarioError = () => horarioError?.classList.add('hidden');

    const setCoords = (la, ln, fly = true) => {
        const laNum = (la === '' || la === null || typeof la === 'undefined') ? '' : parseFloat(la);
        const lnNum = (ln === '' || ln === null || typeof ln === 'undefined') ? '' : parseFloat(ln);

        lat.value = (laNum === '' || Number.isNaN(laNum)) ? '' : laNum.toFixed(6);
        lng.value = (lnNum === '' || Number.isNaN(lnNum)) ? '' : lnNum.toFixed(6);

        latView.value = lat.value;
        lngView.value = lng.value;

        if (!map) return;

        if (lat.value && lng.value) {
            const pos = [parseFloat(lat.value), parseFloat(lng.value)];
            if (!marker) marker = L.marker(pos).addTo(map);
            else marker.setLatLng(pos);

            if (fly) map.setView(pos, Math.max(map.getZoom(), 15));
            coordsHint.textContent = 'Marcado ✅';
        } else {
            if (marker) { map.removeLayer(marker); marker = null; }
            coordsHint.textContent = 'Click en el mapa para marcar el punto.';
        }
    };

    const initMapIfNeeded = () => {
        if (map) {
            setTimeout(() => map.invalidateSize(), 120);
            if (lat.value && lng.value) setCoords(lat.value, lng.value, true);
            return;
        }

        const defaultLat = -33.45;
        const defaultLng = -70.66;

        map = L.map('map', { scrollWheelZoom: true }).setView([defaultLat, defaultLng], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        map.on('click', async (e) => {
            setCoords(e.latlng.lat, e.latlng.lng, false);
            coordsHint.textContent = 'Buscando dirección...';

            const res = await reverseGeocode(e.latlng.lat, e.latlng.lng);
            if (res?.display_name) {
                if (!direccion.value) direccion.value = res.display_name;
                if (!nombre.value) nombre.value = guessNameFromDisplay(res.display_name);
                coordsHint.textContent = 'Marcado ✅ (dirección sugerida)';
            } else {
                coordsHint.textContent = 'Marcado ✅';
            }
        });

        if (lat.value && lng.value) setCoords(lat.value, lng.value, true);

        setTimeout(() => map.invalidateSize(), 120);
    };

    const guessNameFromDisplay = (display) => {
        const first = (display || '').split(',')[0]?.trim();
        return first || '';
    };

    const getEl = (cls, day) => document.querySelector(`.${cls}[data-day="${day}"]`);
    const getDayBlock = (day) => document.querySelector(`.dayRanges[data-day="${day}"]`);

    const setDayEnabled = (day, enabled) => {
        const block = getDayBlock(day);
        const from  = getEl('dayFrom', day);
        const to    = getEl('dayTo', day);
        const from2 = getEl('dayFrom2', day);
        const to2   = getEl('dayTo2', day);

        if (block) block.style.display = enabled ? '' : 'none';

        [from, to, from2, to2].forEach(el => {
            if (!el) return;
            el.disabled = !enabled;
            if (!enabled) el.value = '';
        });
    };

    const resetHorarioUI = () => {
        hideHorarioError();
        dayToggles.forEach(t => {
            t.checked = false;
            setDayEnabled(t.dataset.day, false);
        });
    };

    const applyHorarioUIFromData = (data) => {
        resetHorarioUI();
        if (!data || typeof data !== 'object') return;

        Object.keys(data).forEach(day => {
            const ranges = data[day];
            if (!Array.isArray(ranges) || !ranges.length) return;

            const toggle = document.querySelector(`.dayToggle[data-day="${day}"]`);
            if (toggle) {
                toggle.checked = true;
                setDayEnabled(day, true);
            }

            const r0 = ranges[0] || {};
            const r1 = ranges[1] || {};

            const from  = getEl('dayFrom', day);
            const to    = getEl('dayTo', day);
            const from2 = getEl('dayFrom2', day);
            const to2   = getEl('dayTo2', day);

            if (from && r0.from) from.value = String(r0.from);
            if (to && r0.to) to.value = String(r0.to);

            if (from2 && r1.from) from2.value = String(r1.from);
            if (to2 && r1.to) to2.value = String(r1.to);
        });
    };

    const hasValidRange = (from, to) => !!(from && to && from < to);

    const isHorarioValid = () => {
        let ok = false;

        dayToggles.forEach(t => {
            if (!t.checked) return;
            const day = t.dataset.day;

            const from  = getEl('dayFrom', day)?.value || '';
            const to    = getEl('dayTo', day)?.value || '';
            const from2 = getEl('dayFrom2', day)?.value || '';
            const to2   = getEl('dayTo2', day)?.value || '';

            if (hasValidRange(from, to) || hasValidRange(from2, to2)) ok = true;

            if ((from2 || to2) && !hasValidRange(from2, to2)) ok = false;
        });

        return ok;
    };

    form?.addEventListener('submit', (e) => {
        hideHorarioError();
        if (!isHorarioValid()) {
            e.preventDefault();
            showHorarioError();
            const first = document.querySelector('.dayToggle') || form;
            first?.scrollIntoView?.({ behavior: 'smooth', block: 'center' });
        }
    });

    dayToggles.forEach(t => {
        t.addEventListener('change', () => {
            setDayEnabled(t.dataset.day, t.checked);
            hideHorarioError();
        });
    });

    const resetForm = () => {
        form.action = @json(route('admin.puntos.store'));
        formMethod.value = 'POST';
        title.textContent = 'Nuevo punto';

        geoSearch.value = '';
        nombre.value = '';
        direccion.value = '';
        descripcion.value = '';
        orden.value = 0;
        activo.checked = true;

        setCoords('', '', false);
        hideResults();
        resetHorarioUI();
    };

    const nominatimSearchUrl = (term) => {
        const params = new URLSearchParams({
            format: 'jsonv2',
            q: term,
            addressdetails: '1',
            limit: '6'
        });
        return `https://nominatim.openstreetmap.org/search?${params.toString()}`;
    };

    const nominatimReverseUrl = (la, ln) => {
        const params = new URLSearchParams({
            format: 'jsonv2',
            lat: String(la),
            lon: String(ln),
            addressdetails: '1'
        });
        return `https://nominatim.openstreetmap.org/reverse?${params.toString()}`;
    };

    const geocode = async (term) => {
        if (searchAbort) searchAbort.abort();
        searchAbort = new AbortController();

        const r = await fetch(nominatimSearchUrl(term), {
            signal: searchAbort.signal,
            headers: { 'Accept': 'application/json' }
        });
        if (!r.ok) return [];
        return await r.json();
    };

    const reverseGeocode = async (la, ln) => {
        try {
            const r = await fetch(nominatimReverseUrl(la, ln), { headers: { 'Accept': 'application/json' } });
            if (!r.ok) return null;
            return await r.json();
        } catch {
            return null;
        }
    };

    const renderResults = (items) => {
        geoResultsInner.innerHTML = '';

        if (!items?.length) {
            geoResultsInner.innerHTML = `<div class="p-3 text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Sin resultados.</div>`;
            showResults();
            return;
        }

        items.forEach((it) => {
            const display = it.display_name || '';
            const nameGuess = guessNameFromDisplay(display);

            const row = document.createElement('button');
            row.type = 'button';
            row.className = "w-full text-left p-3 text-sm hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/60 transition";
            row.innerHTML = `
                <div class="font-semibold text-[#123617] dark:text-[#EAF3EA]">${escapeHtml(nameGuess || 'Lugar')}</div>
                <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">${escapeHtml(display)}</div>
            `;

            row.addEventListener('click', () => {
                const la = parseFloat(it.lat);
                const ln = parseFloat(it.lon);
                setCoords(la, ln, true);

                if (!nombre.value) nombre.value = nameGuess || '';
                direccion.value = display;

                const city = it.address?.city || it.address?.town || it.address?.village || it.address?.municipality || '';
                const state = it.address?.state || '';
                const extra = [city, state].filter(Boolean).join(', ');
                if (!descripcion.value && extra) descripcion.value = extra;

                hideResults();
            });

            geoResultsInner.appendChild(row);
        });

        showResults();
    };

    const handleGeoInput = () => {
        const term = (geoSearch.value || '').trim();
        if (term.length < 3) {
            hideResults();
            return;
        }

        geoResultsInner.innerHTML = `<div class="p-3 text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Buscando...</div>`;
        showResults();

        clearTimeout(searchTimer);
        searchTimer = setTimeout(async () => {
            try {
                const items = await geocode(term);
                renderResults(items);
            } catch (e) {
                if (e?.name === 'AbortError') return;
                geoResultsInner.innerHTML = `<div class="p-3 text-xs text-red-700 bg-red-50">Error buscando. Intenta nuevamente.</div>`;
                showResults();
            }
        }, 250);
    };

    geoSearch?.addEventListener('input', handleGeoInput);

    geoSearch?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const firstBtn = geoResultsInner?.querySelector('button');
            if (firstBtn && !geoResults.classList.contains('hidden')) firstBtn.click();
        }
        if (e.key === 'Escape') hideResults();
    });

    document.addEventListener('click', (e) => {
        if (!geoResults || !geoSearch) return;
        const inside = geoResults.contains(e.target) || geoSearch.contains(e.target);
        if (!inside) hideResults();
    });

    q?.addEventListener('input', () => {
        const term = (q.value || '').trim().toLowerCase();
        rows.forEach(r => {
            const blob = (r.dataset.search || '');
            r.classList.toggle('hidden', term ? !blob.includes(term) : false);
        });
    });

    btnNew?.addEventListener('click', () => {
        resetForm();
        openModal();
        initMapIfNeeded();
        requestAnimationFrame(() => geoSearch?.focus());
    });

    btnClose?.addEventListener('click', closeModal);
    btnCancel?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);

    btnClearMap?.addEventListener('click', () => setCoords('', '', false));

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    document.querySelectorAll('.btnEdit').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;

            form.action = (puntosBaseUrl.replace(/\/$/, '') + '/' + id);
            formMethod.value = 'PUT';
            title.textContent = 'Editar punto';

            geoSearch.value = '';
            nombre.value = btn.dataset.nombre || '';
            direccion.value = btn.dataset.direccion || '';
            descripcion.value = btn.dataset.descripcion || '';
            orden.value = btn.dataset.orden || 0;
            activo.checked = (btn.dataset.activo === '1');

            setCoords(btn.dataset.lat || '', btn.dataset.lng || '', true);

            let horario = {};
            try { horario = JSON.parse(btn.dataset.horario || '{}'); } catch { horario = {}; }
            applyHorarioUIFromData(horario);

            openModal();
            initMapIfNeeded();

            requestAnimationFrame(() => nombre?.focus());
        });
    });
});
</script>

@endsection