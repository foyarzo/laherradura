@extends('layouts.app')

@section('content')

@php
    $cartCount = collect(session('cart', []))->sum('qty');

    $visibleProducts = collect($products ?? [])
        ->filter(function ($p) {
            $stock = (int)($p->stock ?? 0);
            $active = (int)($p->is_active ?? 0);
            return $active === 1 && $stock > 0;
        })
        ->values();

    $categories = collect($categories ?? [])
        ->when(empty($categories ?? null), function () use ($visibleProducts) {
            return $visibleProducts
                ->map(fn($p) => $p->category ?? null)
                ->filter()
                ->unique('id')
                ->sortBy('name')
                ->values();
        });

    $visibleCount = $visibleProducts->count();
@endphp

<div class="space-y-7">

    <div class="rounded-2xl overflow-hidden
                bg-white/90 dark:bg-[#0B1A10]/80
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.35)]">

        <div class="p-5 sm:p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-bold
                                     bg-[#123617]/10 text-[#123617]
                                     dark:bg-white/10 dark:text-[#EAF3EA]">
                            Catálogo
                        </span>
                    </div>

                    <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight
                               text-[#123617] dark:text-[#92b95d]">
                        Tienda · Productos
                    </h1>

                    <p class="mt-1 text-sm text-[#3b6a33]/80 dark:text-[#EAF3EA]/70 max-w-2xl">
                        Busca, filtra y agrega al carrito sin perderte. En mobile queda todo cómodo: filtros arriba, productos abajo, carrito siempre a mano.
                    </p>
                </div>

                <div class="w-full lg:w-auto">
                    <a href="{{ route('tienda.carrito') }}"
                       class="relative w-full lg:w-auto inline-flex items-center justify-between gap-3 rounded-2xl px-4 py-3
                              bg-[#123617] text-white hover:opacity-95 transition
                              shadow-[0px_18px_50px_-25px_rgba(18,54,23,0.65)]">
                        <span class="flex items-center gap-2 font-semibold">
                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 border border-white/20">
                                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h15l-1.5 9h-12z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6 5 3H2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                </svg>
                            </span>
                            Carrito
                        </span>

                        @if($cartCount > 0)
                            <span class="inline-flex items-center justify-center min-w-8 h-8 px-2 rounded-full
                                         bg-white text-[#123617] text-xs font-extrabold">
                                {{ $cartCount }}
                            </span>
                        @else
                            <span class="text-xs text-white/80">
                                vacío
                            </span>
                        @endif
                    </a>

                    <div class="mt-2 text-[11px] text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                        Tip: en el carrito podrás ajustar cantidades.
                    </div>
                </div>

            </div>

            <div class="mt-5 grid grid-cols-1 lg:grid-cols-12 gap-3">

                <div class="lg:col-span-5">
                    <div class="relative">
                        <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#3b6a33]/60 dark:text-[#EAF3EA]/50">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.3-4.3"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
                            </svg>
                        </span>

                        <input id="productSearch"
                               type="text"
                               placeholder="Buscar por nombre… (ej: kush, cbd, 15000)"
                               class="w-full rounded-2xl pl-11 pr-4 py-3 text-sm
                                      border border-[#DDEEDD] dark:border-[#16351F]
                                      bg-white/90 dark:bg-[#07120A]/55
                                      text-[#123617] dark:text-[#EAF3EA]
                                      shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03)]
                                      focus:outline-none focus:ring-2 focus:ring-[#92b95d]/40">
                    </div>

                    <div id="searchMeta" class="mt-2 text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60"></div>
                </div>

                <div class="lg:col-span-3">
                    <select id="filterCategory"
                            class="w-full rounded-2xl px-4 py-3 text-sm
                                   border border-[#DDEEDD] dark:border-[#16351F]
                                   bg-white/90 dark:bg-[#07120A]/55
                                   text-[#123617] dark:text-[#EAF3EA]
                                   shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03)]
                                   focus:outline-none focus:ring-2 focus:ring-[#92b95d]/40">
                        <option value="all">Categoría: Todas</option>
                        <option value="none">Sin categoría</option>
                        @foreach(($categories ?? []) as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <select id="filterEstado"
                            class="w-full rounded-2xl px-4 py-3 text-sm
                                   border border-[#DDEEDD] dark:border-[#16351F]
                                   bg-white/90 dark:bg-[#07120A]/55
                                   text-[#123617] dark:text-[#EAF3EA]
                                   shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03)]
                                   focus:outline-none focus:ring-2 focus:ring-[#92b95d]/40">
                        <option value="all">Estado: Todos</option>
                        <option value="active">Solo activos</option>
                        <option value="inactive">Solo inactivos</option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <select id="filterStock"
                            class="w-full rounded-2xl px-4 py-3 text-sm
                                   border border-[#DDEEDD] dark:border-[#16351F]
                                   bg-white/90 dark:bg-[#07120A]/55
                                   text-[#123617] dark:text-[#EAF3EA]
                                   shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03)]
                                   focus:outline-none focus:ring-2 focus:ring-[#92b95d]/40">
                        <option value="all">Stock: Todos</option>
                        <option value="instock">Con stock</option>
                        <option value="low">Stock bajo (≤ 5)</option>
                        <option value="out">Sin stock</option>
                    </select>
                </div>

                <div class="lg:col-span-12 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                        Total: <span id="totalCount" class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $visibleCount }}</span>
                        <span class="mx-2">·</span>
                        Mostrando: <span id="shownCount" class="font-semibold text-[#123617] dark:text-[#EAF3EA]">{{ $visibleCount }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button id="clearSearch" type="button"
                                class="w-full sm:w-auto rounded-xl px-4 py-2 text-sm font-semibold
                                       border border-[#DDEEDD] dark:border-[#16351F]
                                       bg-white/90 dark:bg-[#07120A]/55
                                       text-[#123617] dark:text-[#EAF3EA]
                                       hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/75 transition">
                            Limpiar
                        </button>

                        <button id="toggleCompact" type="button"
                                class="w-full sm:w-auto rounded-xl px-4 py-2 text-sm font-semibold
                                       border border-[#DDEEDD] dark:border-[#16351F]
                                       bg-white/90 dark:bg-[#07120A]/55
                                       text-[#123617] dark:text-[#EAF3EA]
                                       hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/75 transition"
                                aria-pressed="false">
                            Vista compacta
                        </button>
                    </div>
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

    <div class="rounded-2xl overflow-hidden
                bg-white/90 dark:bg-[#0B1A10]/80
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.45)]">

        <div class="p-4 sm:p-5 border-b border-[#DDEEDD] dark:border-[#16351F]
                    flex items-center justify-between gap-3">

            <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                Catálogo
            </div>

            <div class="px-4 py-2 rounded-xl
                        bg-red-600 text-white
                        font-extrabold text-xs sm:text-sm
                        shadow-lg tracking-wide"
                 style="animation: zoomSoft 2.2s ease-in-out infinite;">
                1 UNIDAD = 5 GRAMOS
            </div>

        </div>

        <style>
        @keyframes zoomSoft {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.07); }
        }
        #productModalDescWrap {
            max-height: 170px;
            scrollbar-width: thin;
            scrollbar-color: rgba(146,185,93,0.42) transparent;
            cursor: grab;
        }
        #productModalDescWrap::-webkit-scrollbar { width: 6px; }
        #productModalDescWrap::-webkit-scrollbar-thumb { background: rgba(146,185,93,0.42); border-radius: 999px; }
        #productModalDescWrap::-webkit-scrollbar-track { background: transparent; }
        </style>

        <div class="p-3 sm:p-5">
            <div id="productsGrid"
                 class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-5">

                @forelse($visibleProducts as $product)
                    @php
                        $stock = (int)($product->stock ?? 0);
                        $active = (bool)($product->is_active ?? false);

                        $fallback = asset('assets/img/no_image.png');
                        $src = $product->image ? asset($product->image) : $fallback;

                        $disabled = (!$active || $stock <= 0);

                        $priceInt = (int)($product->price ?? 0);
                        $priceFmt = '$ ' . number_format($priceInt, 0, ',', '.');

                        $statusText = $active ? 'Activo' : 'Inactivo';

                        if ($stock <= 0) {
                            $stockText = 'Sin stock';
                        } elseif ($stock <= 5) {
                            $stockText = "Bajo ({$stock})";
                        } else {
                            $stockText = (string)$stock;
                        }

                        $stockState = $stock <= 0 ? 'out' : ($stock <= 5 ? 'low' : 'instock');

                        $desc = trim((string)($product->description ?? ''));
                        $descMeta = $desc !== '' ? ' ' . $desc : '';

                        $sku = (string)($product->sku ?? '');

                        $catId = $product->category_id ?? ($product->category->id ?? null);
                        $catName = $product->category->name ?? null;
                        $catLabel = $catName ? $catName : 'Sin categoría';

                        $catMeta = $catName ? (' ' . $catName) : ' sin categoria';
                    @endphp

                    <div class="product-card group rounded-2xl overflow-hidden
                                border border-[#DDEEDD] dark:border-[#16351F]
                                bg-white/80 dark:bg-[#07120A]/55
                                hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/75
                                transition shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03)]
                                cursor-pointer"
                         role="button"
                         tabindex="0"
                         aria-label="Ver detalle {{ $product->name }}"
                         data-meta="{{ mb_strtolower(($product->name ?? '').$descMeta.$catMeta.' '.$priceInt.' '.$stock.' '.$statusText.' '.$stockText) }}"
                         data-active="{{ $active ? '1' : '0' }}"
                         data-stock="{{ $stock }}"
                         data-stockstate="{{ $stockState }}"
                         data-category="{{ $catId ? (string)$catId : 'none' }}"
                         data-modal-name="{{ e($product->name ?? '') }}"
                         data-modal-desc="{{ e($desc) }}"
                         data-modal-price="{{ $priceFmt }}"
                         data-modal-stock="{{ e($stockText) }}"
                         data-modal-status="{{ e($statusText) }}"
                         data-modal-sku="{{ e($sku) }}"
                         data-modal-img="{{ $src }}"
                         data-modal-disabled="{{ $disabled ? '1' : '0' }}"
                         data-modal-category="{{ e($catLabel) }}"
                    >
                        <div class="relative">
                            <div class="w-full aspect-square bg-white dark:bg-[#07120A] overflow-hidden">
                                <img src="{{ $src }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover transition-transform duration-200 group-hover:scale-[1.03]"
                                     loading="lazy"
                                     onerror="this.onerror=null;this.src='{{ $fallback }}';">
                            </div>

                            <div class="absolute top-2 sm:top-3 left-2 sm:left-3 flex flex-wrap gap-2">
                                <span class="px-2 py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold
                                            {{ $active ? 'bg-green-600 text-white' : 'bg-red-600 text-white' }}">
                                    {{ $active ? 'Activo' : 'Inactivo' }}
                                </span>

                                @if($stock <= 0)
                                    <span class="px-2 py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold bg-red-600 text-white">
                                        Sin stock
                                    </span>
                                @elseif($stock <= 5)
                                    <span class="px-2 py-1 rounded-full text-[10px] sm:text-[11px] font-extrabold bg-yellow-500 text-[#07120A]">
                                        Stock bajo
                                    </span>
                                @endif
                            </div>

                            <div class="absolute bottom-2 sm:bottom-3 right-2 sm:right-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-[11px] font-extrabold
                                             bg-black/50 text-white backdrop-blur border border-white/15">
                                    {{ $priceFmt }}
                                </span>
                            </div>
                        </div>

                        <div class="p-3 sm:p-4 space-y-3">

                            <div class="min-w-0">
                                <div class="text-[13px] sm:text-sm font-semibold text-[#123617] dark:text-[#EAF3EA] line-clamp-2 leading-snug">
                                    {{ $product->name }}
                                </div>

                                <div class="mt-1 flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] sm:text-[11px] font-extrabold
                                                 border border-[#CFE6C9] dark:border-[#1B3B22]
                                                 bg-[#F6FBF4] dark:bg-[#07120A]
                                                 text-[#123617] dark:text-[#EAF3EA]">
                                        {{ $catLabel }}
                                    </span>
                                </div>

                                @if($desc !== '')
                                    <div class="mt-2 text-xs text-[#3b6a33]/75 dark:text-[#EAF3EA]/60
                                                line-clamp-2 sm:line-clamp-3 leading-snug">
                                        {{ $desc }}
                                    </div>
                                @endif

                                <div class="mt-2 flex items-center justify-between gap-2">
                                    <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                                        Stock: <span class="font-semibold">{{ $stockText }}</span>
                                    </div>

                                    @if(!$disabled)
                                        <span class="text-[11px] font-bold text-[#123617] dark:text-[#EAF3EA]
                                                     bg-[#123617]/10 dark:bg-white/10 rounded-full px-2 py-1">
                                            Disponible
                                        </span>
                                    @else
                                        <span class="text-[11px] font-bold text-gray-600 bg-gray-200 rounded-full px-2 py-1">
                                            No disponible
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-1">
                                @if(!$disabled)
                                    <form method="POST" action="{{ route('tienda.carrito.add', $product) }}"
                                          class="add-to-cart-form grid grid-cols-12 gap-2 items-stretch"
                                          data-stop-modal>
                                        @csrf

                                        <div class="col-span-5 min-[420px]:col-span-4">
                                            <input type="number"
                                                   name="qty"
                                                   min="1"
                                                   max="{{ $stock }}"
                                                   value="1"
                                                   class="qty-input w-full rounded-xl px-3 py-2 text-sm
                                                          border border-[#DDEEDD] dark:border-[#16351F]
                                                          bg-white/90 dark:bg-[#07120A]
                                                          text-[#123617] dark:text-[#EAF3EA]">
                                        </div>

                                        <div class="col-span-7 min-[420px]:col-span-8">
                                            <button type="submit"
                                                    class="w-full rounded-xl px-4 py-2 text-sm font-semibold
                                                           bg-[#123617] text-white hover:opacity-90 transition"
                                                    data-stop-modal>
                                                Agregar
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <button type="button" disabled
                                            class="w-full rounded-xl px-4 py-2 text-sm font-semibold
                                                   bg-gray-200 text-gray-500 cursor-not-allowed"
                                            data-stop-modal>
                                        No disponible
                                    </button>
                                @endif
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="px-5 py-10 text-center text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                            No hay productos registrados.
                        </div>
                    </div>
                @endforelse

            </div>

            <div id="noResults"
                 class="hidden mt-6 rounded-2xl px-5 py-8 text-center text-sm
                        border border-[#DDEEDD] dark:border-[#16351F]
                        bg-white/80 dark:bg-[#07120A]/60
                        text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                No se encontraron productos con ese criterio.
            </div>
        </div>
    </div>

</div>

<div id="productModal" class="fixed inset-0 z-[90] hidden flex items-center justify-center" aria-hidden="true">

    <div data-close-product-modal class="absolute inset-0 bg-black/45 backdrop-blur-sm"></div>

    <div role="dialog" aria-modal="true" aria-labelledby="productModalTitle"
         class="relative w-full max-w-md sm:max-w-lg
                max-h-[85vh]
                overflow-hidden
                rounded-2xl
                bg-white dark:bg-[#0B1A10]
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_24px_70px_-28px_rgba(0,0,0,0.65)]
                flex flex-col mx-4">

        <div class="px-4 sm:px-5 py-3 border-b border-[#DDEEDD] dark:border-[#16351F]
                    bg-[#F6FBF4] dark:bg-[#07120A]/55">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h3 id="productModalTitle"
                        class="text-base font-extrabold text-[#123617] dark:text-[#EAF3EA] line-clamp-2 leading-snug">
                        —
                    </h3>
                    <p id="productModalSub"
                       class="mt-1 text-[12px] text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 line-clamp-1">
                        —
                    </p>
                </div>

                <button type="button" data-close-product-modal
                        class="shrink-0 inline-flex items-center justify-center h-9 w-9 rounded-xl
                               border border-[#DDEEDD] dark:border-[#16351F]
                               text-[#123617] dark:text-[#EAF3EA]
                               hover:bg-white/60 dark:hover:bg-[#0D1E12]/50
                               focus:outline-none focus:ring-2 focus:ring-[#92b95d]/35"
                        aria-label="Cerrar">
                    ✕
                </button>
            </div>
        </div>

        <div class="p-4 sm:p-5 flex-1 min-h-0 flex flex-col gap-4">

            <div class="grid grid-cols-12 gap-3 items-start">
                <div class="col-span-5">
                    <div class="w-full aspect-square rounded-xl overflow-hidden
                                border border-[#DDEEDD] dark:border-[#16351F]
                                bg-white dark:bg-[#07120A]">
                        <img id="productModalImg" src="" alt="Imagen producto" class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="col-span-7 space-y-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span id="productModalStatus"
                              class="inline-flex items-center gap-2 text-[11px] font-bold px-2.5 py-1 rounded-full border">
                            —
                        </span>

                        <span class="inline-flex items-center gap-2 text-[11px] font-extrabold rounded-full px-2.5 py-1
                                     bg-[#123617]/10 text-[#123617]
                                     dark:bg-white/10 dark:text-[#EAF3EA]">
                            <span id="productModalPrice">—</span>
                        </span>

                        <span class="inline-flex items-center gap-2 text-[11px] font-semibold rounded-full px-2.5 py-1
                                     border border-[#DDEEDD] dark:border-[#16351F]
                                     bg-white/70 dark:bg-[#07120A]/55
                                     text-[#123617] dark:text-[#EAF3EA]">
                            Stock: <span id="productModalStock" class="font-extrabold">—</span>
                        </span>

                        <span class="inline-flex items-center gap-2 text-[11px] font-semibold rounded-full px-2.5 py-1
                                     border border-[#DDEEDD] dark:border-[#16351F]
                                     bg-white/70 dark:bg-[#07120A]/55
                                     text-[#123617] dark:text-[#EAF3EA]">
                            Categoría: <span id="productModalCategory" class="font-extrabold">—</span>
                        </span>
                    </div>

                    <div class="rounded-xl p-3 border border-[#DDEEDD] dark:border-[#16351F]
                                bg-[#F6FBF4] dark:bg-[#07120A]/55">
                        <div class="text-[11px] text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">SKU</div>
                        <div id="productModalSku" class="mt-1 text-sm font-extrabold text-[#123617] dark:text-[#EAF3EA] break-all">
                            —
                        </div>
                    </div>

                    <div class="rounded-xl p-3 border border-[#DDEEDD] dark:border-[#16351F]
                                bg-[#F6FBF4] dark:bg-[#07120A]/55">
                        <div class="text-[11px] text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Estado</div>
                        <div id="productModalEstadoTxt" class="mt-1 text-sm font-extrabold text-[#123617] dark:text-[#EAF3EA]">
                            —
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl p-4 border border-[#DDEEDD] dark:border-[#16351F]
                        bg-white/70 dark:bg-[#0D1E12]/35">
                <div class="text-[11px] font-extrabold text-[#123617] dark:text-[#92b95d] uppercase tracking-wide">
                    Descripción
                </div>

                <div id="productModalDescWrap"
                     class="mt-3 rounded-xl border border-[#DDEEDD] dark:border-[#16351F]
                            bg-[#F6FBF4]/70 dark:bg-[#07120A]/40
                            p-3 h-[170px] overflow-y-auto pr-2">
                    <div id="productModalDesc"
                         class="text-sm leading-relaxed whitespace-pre-line
                                text-[#2f4f3a] dark:text-[#EAF3EA]/80">
                        —
                    </div>
                </div>
            </div>

        </div>

        <div class="px-4 sm:px-5 py-3 border-t border-[#DDEEDD] dark:border-[#16351F]
                    bg-white/80 dark:bg-[#07120A]/40">
            <div class="flex justify-end">
                <button type="button" data-close-product-modal
                        class="rounded-xl px-4 py-2 text-sm font-semibold
                               border border-[#DDEEDD] dark:border-[#16351F]
                               bg-white/90 dark:bg-[#07120A]/55
                               text-[#123617] dark:text-[#EAF3EA]
                               hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/75 transition">
                    Cerrar
                </button>
            </div>
        </div>

    </div>
</div>

<div class="fixed bottom-4 left-4 right-4 z-[40] lg:hidden">
    <a href="{{ route('tienda.carrito') }}"
       class="w-full inline-flex items-center justify-between gap-3 rounded-2xl px-4 py-3
              bg-[#123617] text-white hover:opacity-95 transition
              shadow-[0px_18px_60px_-25px_rgba(18,54,23,0.75)]">
        <span class="font-semibold">Ir al carrito</span>
        @if($cartCount > 0)
            <span class="inline-flex items-center justify-center min-w-8 h-8 px-2 rounded-full
                         bg-white text-[#123617] text-xs font-extrabold">
                {{ $cartCount }}
            </span>
        @else
            <span class="text-xs text-white/80">vacío</span>
        @endif
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('productSearch');
    const clearBtn = document.getElementById('clearSearch');
    const cards = Array.from(document.querySelectorAll('.product-card'));
    const shownCount = document.getElementById('shownCount');
    const totalCount = document.getElementById('totalCount');
    const noResults = document.getElementById('noResults');
    const searchMeta = document.getElementById('searchMeta');

    const filterCategory = document.getElementById('filterCategory');
    const filterEstado = document.getElementById('filterEstado');
    const filterStock = document.getElementById('filterStock');

    const toggleCompact = document.getElementById('toggleCompact');
    const grid = document.getElementById('productsGrid');

    const total = cards.length;
    if (totalCount) totalCount.textContent = total;
    if (shownCount) shownCount.textContent = total;

    const getCategoryPass = (card) => {
        const v = (filterCategory?.value || 'all');
        if (v === 'all') return true;

        const cardCat = card.dataset.category || 'none';
        if (v === 'none') return cardCat === 'none' || cardCat === '' || cardCat === 'null';
        return cardCat === v;
    };

    const getEstadoPass = (card) => {
        const v = (filterEstado?.value || 'all');
        if (v === 'all') return true;
        const isActive = (card.dataset.active === '1');
        return v === 'active' ? isActive : !isActive;
    };

    const getStockPass = (card) => {
        const v = (filterStock?.value || 'all');
        if (v === 'all') return true;
        const state = (card.dataset.stockstate || 'all');
        if (v === 'instock') return state === 'instock' || state === 'low';
        if (v === 'low') return state === 'low';
        if (v === 'out') return state === 'out';
        return true;
    };

    const applyFilter = () => {
        const q = (input?.value || '').trim().toLowerCase();
        let visible = 0;

        cards.forEach(card => {
            const meta = card.dataset.meta || '';
            const matchText = q.length === 0 ? true : meta.includes(q);

            const matchCat = getCategoryPass(card);
            const matchEstado = getEstadoPass(card);
            const matchStock = getStockPass(card);

            const ok = matchText && matchCat && matchEstado && matchStock;

            card.classList.toggle('hidden', !ok);
            if (ok) visible++;
        });

        if (shownCount) shownCount.textContent = visible;
        if (noResults) noResults.classList.toggle('hidden', visible !== 0);

        if (searchMeta) {
            if (!q && (filterCategory?.value === 'all') && (filterEstado?.value === 'all') && (filterStock?.value === 'all')) {
                searchMeta.textContent = '';
            } else {
                const parts = [];
                if (q) parts.push(`"${q}"`);
                if (filterCategory?.value && filterCategory.value !== 'all') parts.push(`cat: ${filterCategory.value}`);
                if (filterEstado?.value && filterEstado.value !== 'all') parts.push(`estado: ${filterEstado.value}`);
                if (filterStock?.value && filterStock.value !== 'all') parts.push(`stock: ${filterStock.value}`);
                searchMeta.textContent = `Filtros: ${parts.join(' · ')} · Resultados: ${visible}`;
            }
        }
    };

    const modal = document.getElementById('productModal');
    const modalCloseEls = Array.from(document.querySelectorAll('[data-close-product-modal]'));

    const elTitle = document.getElementById('productModalTitle');
    const elSub = document.getElementById('productModalSub');
    const elImg = document.getElementById('productModalImg');
    const elDesc = document.getElementById('productModalDesc');
    const elDescWrap = document.getElementById('productModalDescWrap');
    const elPrice = document.getElementById('productModalPrice');
    const elStock = document.getElementById('productModalStock');
    const elSku = document.getElementById('productModalSku');
    const elEstadoTxt = document.getElementById('productModalEstadoTxt');
    const elStatus = document.getElementById('productModalStatus');
    const elCategory = document.getElementById('productModalCategory');

    let lastFocused = null;

    const openModal = (card) => {
        if (!modal || !card) return;

        lastFocused = document.activeElement;

        const name = card.dataset.modalName || 'Producto';
        const desc = card.dataset.modalDesc || '';
        const price = card.dataset.modalPrice || '';
        const stock = card.dataset.modalStock || '';
        const statusTxt = card.dataset.modalStatus || '';
        const sku = card.dataset.modalSku || '—';
        const img = card.dataset.modalImg || '';
        const disabled = (card.dataset.modalDisabled === '1');
        const cat = card.dataset.modalCategory || 'Sin categoría';

        if (elTitle) elTitle.textContent = name;
        if (elSub) {
            elSub.textContent = disabled
                ? 'No disponible para agregar (sin stock o inactivo)'
                : 'Disponible para agregar al carrito';
        }

        if (elImg) {
            elImg.src = img;
            elImg.alt = name;
        }

        if (elPrice) elPrice.textContent = price || '—';
        if (elStock) elStock.textContent = stock || '—';
        if (elSku) elSku.textContent = sku || '—';
        if (elEstadoTxt) elEstadoTxt.textContent = statusTxt || '—';
        if (elCategory) elCategory.textContent = cat || 'Sin categoría';

        const safeDesc = (desc && desc.trim().length) ? desc : 'Sin descripción.';
        if (elDesc) elDesc.textContent = safeDesc;
        if (elDescWrap) elDescWrap.scrollTop = 0;

        const isActive = (card.dataset.active === '1');
        if (elStatus) {
            elStatus.textContent = isActive ? 'Activo' : 'Inactivo';
            elStatus.className =
                'inline-flex items-center gap-2 font-semibold text-xs px-3 py-1 rounded-full border ' +
                (isActive
                    ? 'border-green-200 bg-green-50 text-green-700 dark:border-green-700/30 dark:bg-green-900/20 dark:text-green-300'
                    : 'border-red-200 bg-red-50 text-red-700 dark:border-red-700/30 dark:bg-red-900/20 dark:text-red-300'
                );
        }

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.documentElement.classList.add('overflow-hidden');

        const btn = modal.querySelector('[data-close-product-modal]');
        btn?.focus();
    };

    const closeModal = () => {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('overflow-hidden');
        if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
    };

    modalCloseEls.forEach(el => el.addEventListener('click', closeModal));

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
    });

    document.querySelectorAll('[data-stop-modal]').forEach(el => {
        el.addEventListener('click', (e) => e.stopPropagation());
        el.addEventListener('keydown', (e) => e.stopPropagation());
    });

    cards.forEach(card => {
        card.addEventListener('click', (e) => {
            if (e.target.closest('[data-stop-modal], button, input, a, form, select, textarea, label')) return;
            openModal(card);
        });

        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                if (e.target.closest('[data-stop-modal], button, input, a, form, select, textarea, label')) return;
                e.preventDefault();
                openModal(card);
            }
        });
    });

    if (input) {
        input.addEventListener('input', applyFilter);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                input.value = '';
                applyFilter();
            }
        });
    }

    [filterCategory, filterEstado, filterStock].forEach(el => el?.addEventListener('change', applyFilter));

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            if (input) input.value = '';
            if (filterCategory) filterCategory.value = 'all';
            if (filterEstado) filterEstado.value = 'all';
            if (filterStock) filterStock.value = 'all';
            applyFilter();
            input?.focus();
        });
    }

    if (toggleCompact && grid) {
        toggleCompact.addEventListener('click', () => {
            const pressed = toggleCompact.getAttribute('aria-pressed') === 'true';
            const next = !pressed;

            toggleCompact.setAttribute('aria-pressed', next ? 'true' : 'false');
            toggleCompact.textContent = next ? 'Vista normal' : 'Vista compacta';

            grid.classList.toggle('xl:grid-cols-6', next);
            grid.classList.toggle('xl:grid-cols-5', !next);

            grid.classList.toggle('gap-2', next);
            grid.classList.toggle('gap-3', !next);
        });
    }

    document.querySelectorAll('.add-to-cart-form').forEach(form => {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('button[type="submit"]');
            if (!btn) return;
            if (btn.dataset.loading === '1') return;
            btn.dataset.loading = '1';
            btn.disabled = true;
            btn.textContent = 'Agregando...';
        });
    });

    applyFilter();
});
</script>

@endsection