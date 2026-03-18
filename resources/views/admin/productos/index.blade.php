@extends('layouts.app')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-[#123617] dark:text-[#92b95d]">
                Gestión de Productos
            </h1>
            <p class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Administración del catálogo
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
            {{-- ✅ CRUD categorías (modal) --}}
            <button type="button"
                    id="btnOpenCategoriesCrud"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold
                           bg-white/85 dark:bg-[#07120A]/55
                           border border-[#DDEEDD] dark:border-[#16351F]
                           text-[#123617] dark:text-[#EAF3EA]
                           hover:bg-[#F6FBF4] dark:hover:bg-white/5
                           transition-all">
                <svg class="w-4 h-4 opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                Categorías
            </button>

            {{-- Crear producto --}}
            <a href="{{ route('admin.productos.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold
                      bg-[#1e4e25] hover:bg-[#123617] text-white
                      dark:bg-[#92b95d] dark:hover:bg-[#81a553]
                      dark:text-[#07120A]
                      transition-all">
                <svg class="w-4 h-4 opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Producto
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="px-4 py-3 rounded-lg bg-green-100 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="px-4 py-3 rounded-lg bg-red-100 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="px-4 py-3 rounded-lg bg-red-100 text-red-800 text-sm">
            <div class="font-semibold mb-1">Revisa lo siguiente:</div>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Card container --}}
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

        <div class="p-4 sm:p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($products as $product)
                    @php
                        $stock = (int)($product->stock ?? 0);
                        $fallback = asset('assets/img/no_image.png');
                        $src = $product->image ? asset($product->image) : $fallback;
                        $isActive = ((int)($product->is_active ?? 0) === 1);
                        $desc = trim((string)($product->description ?? ''));
                        $categoryName = $product->category->name ?? null; // requiere relación en Product
                    @endphp

                    <div class="rounded-2xl overflow-hidden
                                border border-[#DDEEDD] dark:border-[#16351F]
                                bg-white/80 dark:bg-[#07120A]/55
                                shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03)]
                                hover:shadow-[0px_18px_50px_-30px_rgba(18,54,23,0.45)]
                                transition">

                        <div class="p-4 flex gap-4">
                            <div class="w-16 h-16 rounded-2xl overflow-hidden shrink-0
                                        border border-[#DDEEDD] dark:border-[#16351F]
                                        bg-white dark:bg-[#07120A]">
                                <img src="{{ $src }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover"
                                     loading="lazy"
                                     onerror="this.onerror=null;this.src='{{ $fallback }}';">
                            </div>

                            <div class="min-w-0 flex-1">
                                {{-- Nombre --}}
                                <div class="font-semibold text-[#123617] dark:text-[#EAF3EA] truncate">
                                    {{ $product->name }}
                                </div>

                                {{-- Categoría --}}
                                @if($categoryName)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold
                                                     border border-[#CFE6C9] dark:border-[#1B3B22]
                                                     bg-[#F6FBF4] dark:bg-[#07120A]
                                                     text-[#123617] dark:text-[#EAF3EA]">
                                            {{ $categoryName }}
                                        </span>
                                    </div>
                                @endif

                                {{-- Descripción --}}
                                @if($desc !== '')
                                    <div class="mt-2 text-xs text-[#3b6a33]/75 dark:text-[#EAF3EA]/60
                                                line-clamp-2 leading-snug">
                                        {{ $desc }}
                                    </div>
                                @endif

                                {{-- Badges --}}
                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs
                                                 border border-[#CFE6C9] dark:border-[#1B3B22]
                                                 bg-[#F6FBF4] dark:bg-[#07120A]
                                                 text-[#123617] dark:text-[#EAF3EA]">
                                        $ {{ number_format((int)($product->price ?? 0), 0, ',', '.') }}
                                    </span>

                                    @if($isActive)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold
                                                     bg-green-50 text-green-700 border border-green-200
                                                     dark:bg-green-900/20 dark:text-green-300 dark:border-green-700/30">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span> Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold
                                                     bg-red-50 text-red-700 border border-red-200
                                                     dark:bg-red-900/20 dark:text-red-300 dark:border-red-700/30">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span> Inactivo
                                        </span>
                                    @endif

                                    @if($stock <= 0)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                                     bg-red-50 text-red-700 border border-red-200
                                                     dark:bg-red-900/20 dark:text-red-300 dark:border-red-700/30">
                                            Sin stock
                                        </span>
                                    @elseif($stock <= 5)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                                     bg-yellow-50 text-yellow-700 border border-yellow-200
                                                     dark:bg-yellow-900/20 dark:text-yellow-300 dark:border-yellow-700/30">
                                            Bajo ({{ $stock }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold
                                                     border border-[#CFE6C9] dark:border-[#1B3B22]
                                                     bg-[#EAF6E7] dark:bg-[#0A2012]
                                                     text-[#123617] dark:text-[#EAF3EA]">
                                            Stock: {{ $stock }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Acciones --}}
                                <div class="mt-4 flex gap-2">
                                    <a href="{{ route('admin.productos.edit', $product) }}"
                                       class="flex-1 inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-xl
                                              bg-white/80 dark:bg-[#0D1E12]/70 backdrop-blur
                                              border border-[#CFE6C9] dark:border-[#1B3B22]
                                              text-[#123617] dark:text-[#EAF3EA]
                                              hover:border-[#81a553] dark:hover:border-[#3b6a33]
                                              transition-all">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('admin.productos.destroy', $product) }}"
                                          class="flex-1"
                                          onsubmit="return confirm('¿Eliminar este producto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-xl
                                                       bg-[#FFECEC] dark:bg-[#1D0002]
                                                       border border-[#F53003]/25
                                                       text-[#B10E00] dark:text-[#FF4433]
                                                       hover:opacity-90 transition-all">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>

                @empty
                    <div class="col-span-full px-5 py-8 text-center text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                        No hay productos registrados.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
    @keyframes zoomSoft {
      0%, 100% { transform: scale(1); }
      50%      { transform: scale(1.07); }
    }
    </style>

    <div>
        {{ $products->links() }}
    </div>

</div>

{{-- =========================================================
   ✅ MODAL CRUD CATEGORÍAS (crear / editar / eliminar)
   Requiere:
   - $categories (lista) disponible en esta vista
   - rutas: admin.categories.store / admin.categories.update / admin.categories.destroy
   ========================================================= --}}
<div id="categoriesCrudModal" class="fixed inset-0 z-[90] hidden flex items-center justify-center" aria-hidden="true">
    <div data-close-categories-crud class="absolute inset-0 bg-black/45 backdrop-blur-sm"></div>

    <div role="dialog" aria-modal="true" aria-labelledby="categoriesCrudTitle"
         class="relative w-full max-w-2xl mx-4
                rounded-2xl
                bg-white dark:bg-[#0B1A10]
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_24px_70px_-28px_rgba(0,0,0,0.65)]
                overflow-hidden">

        <div class="px-5 py-4 border-b border-[#DDEEDD] dark:border-[#16351F]
                    bg-[#F6FBF4] dark:bg-[#07120A]/55">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h2 id="categoriesCrudTitle" class="text-base font-semibold text-[#123617] dark:text-[#EAF3EA]">
                        Categorías
                    </h2>
                    <p class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                        Crea, edita o elimina categorías.
                    </p>
                </div>

                <button type="button" data-close-categories-crud
                        class="rounded-xl px-3 py-2 text-xs font-semibold
                               border border-[#DDEEDD] dark:border-[#16351F]
                               bg-white/80 dark:bg-[#07120A]/55
                               hover:bg-black/5 dark:hover:bg-white/5 transition">
                    Cerrar
                </button>
            </div>
        </div>

        <div class="p-5 space-y-5">

            {{-- ✅ Crear --}}
            <form id="categoryCreateForm" method="POST" action="{{ route('admin.categories.store') }}"
                  class="rounded-2xl border border-[#DDEEDD] dark:border-[#16351F]
                         bg-white/70 dark:bg-[#07120A]/40 p-4">
                @csrf
                <div class="flex flex-col sm:flex-row gap-3 sm:items-end">
                    <div class="flex-1">
                        <label class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">Nueva categoría</label>
                        <input type="text"
                               name="name"
                               id="newCategoryName"
                               class="mt-2 w-full rounded-lg border px-4 py-2
                                      bg-white dark:bg-[#07120A]/60
                                      border-[#DDEEDD] dark:border-[#16351F]
                                      text-[#123617] dark:text-[#EAF3EA]"
                               placeholder="Ej: Flores, Extractos..."
                               required>
                        <div id="catCreateError" class="mt-1 text-xs text-red-600 hidden"></div>
                    </div>

                    <button type="submit"
                            id="btnCreateCategory"
                            class="px-4 py-2 rounded-lg text-sm font-semibold
                                   bg-[#1e4e25] hover:bg-[#123617] text-white
                                   dark:bg-[#92b95d] dark:hover:bg-[#81a553]
                                   dark:text-[#07120A]
                                   transition">
                        Crear
                    </button>
                </div>
            </form>

            {{-- ✅ Listado / Editar / Eliminar --}}
            <div class="rounded-2xl border border-[#DDEEDD] dark:border-[#16351F] overflow-hidden">
                <div class="px-4 py-3 border-b border-[#DDEEDD] dark:border-[#16351F]
                            bg-[#F6FBF4] dark:bg-[#07120A]/55">
                    <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                        Lista de categorías
                    </div>
                </div>

                <div class="divide-y divide-[#DDEEDD] dark:divide-[#16351F]" id="categoriesList">
                    @forelse(($categories ?? []) as $cat)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center gap-3"
                             data-cat-row
                             data-id="{{ $cat->id }}"
                             data-name="{{ e($cat->name) }}">

                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA] truncate">
                                    <span data-cat-name>{{ $cat->name }}</span>
                                </div>
                                <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 truncate">
                                    Slug: {{ $cat->slug ?? '-' }}
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="button"
                                        class="btnEditCat inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-xl
                                               bg-white/80 dark:bg-[#0D1E12]/70
                                               border border-[#CFE6C9] dark:border-[#1B3B22]
                                               text-[#123617] dark:text-[#EAF3EA]
                                               hover:border-[#81a553] dark:hover:border-[#3b6a33]
                                               transition">
                                    Editar
                                </button>

                                <form class="catDeleteForm" method="POST" action="{{ route('admin.categories.destroy', $cat) }}"
                                      onsubmit="return confirm('¿Eliminar esta categoría? (Debe estar sin productos)');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-xl
                                                   bg-[#FFECEC] dark:bg-[#1D0002]
                                                   border border-[#F53003]/25
                                                   text-[#B10E00] dark:text-[#FF4433]
                                                   hover:opacity-90 transition-all">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- ✅ mini modal editar inline (simple) --}}
                        <div class="hidden p-4 bg-white/70 dark:bg-[#07120A]/35 border-t border-[#DDEEDD] dark:border-[#16351F]"
                             data-cat-edit>
                            <form class="catUpdateForm"
                                  method="POST"
                                  action="{{ route('admin.categories.update', $cat) }}">
                                @csrf
                                @method('PUT')

                                <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                                    <input type="text"
                                           name="name"
                                           value="{{ $cat->name }}"
                                           class="flex-1 rounded-lg border px-4 py-2 text-sm
                                                  bg-white dark:bg-[#07120A]/60
                                                  border-[#DDEEDD] dark:border-[#16351F]
                                                  text-[#123617] dark:text-[#EAF3EA]"
                                           required>

                                    <div class="flex gap-2">
                                        <button type="submit"
                                                class="px-3 py-2 rounded-lg text-xs font-semibold
                                                       bg-[#1e4e25] hover:bg-[#123617] text-white
                                                       dark:bg-[#92b95d] dark:hover:bg-[#81a553]
                                                       dark:text-[#07120A]
                                                       transition">
                                            Guardar
                                        </button>

                                        <button type="button"
                                                class="btnCancelEdit px-3 py-2 rounded-lg text-xs font-semibold
                                                       border border-[#DDEEDD] dark:border-[#16351F]
                                                       bg-white/80 dark:bg-[#07120A]/55
                                                       text-[#123617] dark:text-[#EAF3EA]
                                                       hover:bg-black/5 dark:hover:bg-white/5 transition">
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="p-6 text-center text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                            Aún no hay categorías.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // ==========================
  // Modal CRUD categorías
  // ==========================
  const crudModal = document.getElementById("categoriesCrudModal");
  const openCrudBtn = document.getElementById("btnOpenCategoriesCrud");
  const closeCrudBtns = document.querySelectorAll("[data-close-categories-crud]");

  const openCrud = () => crudModal?.classList.remove("hidden");
  const closeCrud = () => crudModal?.classList.add("hidden");

  openCrudBtn?.addEventListener("click", openCrud);
  closeCrudBtns.forEach(btn => btn.addEventListener("click", closeCrud));
  document.addEventListener("keydown", (e) => { if (e.key === "Escape") closeCrud(); });

  // Toggle editar inline
  document.querySelectorAll(".btnEditCat").forEach((btn) => {
    btn.addEventListener("click", () => {
      const row = btn.closest("[data-cat-row]");
      if (!row) return;
      const edit = row.nextElementSibling; // el bloque data-cat-edit viene justo debajo
      if (!edit || !edit.hasAttribute("data-cat-edit")) return;

      edit.classList.toggle("hidden");
    });
  });

  document.querySelectorAll(".btnCancelEdit").forEach((btn) => {
    btn.addEventListener("click", () => {
      const edit = btn.closest("[data-cat-edit]");
      edit?.classList.add("hidden");
    });
  });

  // Crear por AJAX (para no recargar). Si falla, mostramos error.
  const createForm = document.getElementById("categoryCreateForm");
  const createError = document.getElementById("catCreateError");
  const newName = document.getElementById("newCategoryName");
  const btnCreate = document.getElementById("btnCreateCategory");
  const list = document.getElementById("categoriesList");

  const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || null;
  const safeJson = (raw) => { try { return JSON.parse(raw); } catch { return null; } };

  const showCreateError = (msg) => {
    if (!createError) return;
    createError.textContent = msg || "Ocurrió un error.";
    createError.classList.remove("hidden");
  };
  const clearCreateError = () => {
    if (!createError) return;
    createError.textContent = "";
    createError.classList.add("hidden");
  };

  createForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearCreateError();

    const name = (newName?.value || "").trim();
    if (!name) { showCreateError("Escribe un nombre."); return; }

    const csrf = getCsrf();
    if (!csrf) { showCreateError("Falta CSRF token."); return; }

    btnCreate.disabled = true;
    btnCreate.classList.add("opacity-60");

    try {
      const response = await fetch(createForm.action, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": csrf
        },
        body: JSON.stringify({ name })
      });

      const raw = await response.text();
      const data = safeJson(raw);

      if (!response.ok) {
        const msg = data?.errors?.name?.[0] || data?.message || `Error ${response.status}`;
        showCreateError(msg);
        return;
      }

      if (!data?.ok || !data?.category?.id) {
        showCreateError("Respuesta inválida al crear.");
        return;
      }

      // Insertar fila nueva (solo visual). Para editar/eliminar “real”, igual sirven las rutas si recargas.
      const cat = data.category;
      const rowHtml = `
        <div class="p-4 flex flex-col sm:flex-row sm:items-center gap-3"
             data-cat-row data-id="${cat.id}" data-name="${(cat.name || "").replace(/"/g,'&quot;')}">
          <div class="flex-1 min-w-0">
            <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA] truncate">
              <span data-cat-name>${cat.name}</span>
            </div>
            <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 truncate">
              Slug: ${cat.slug || "-"}
            </div>
          </div>
          <div class="flex gap-2">
            <span class="inline-flex items-center px-3 py-2 text-xs font-semibold rounded-xl
                         border border-[#CFE6C9] dark:border-[#1B3B22]
                         bg-[#F6FBF4] dark:bg-[#07120A]
                         text-[#123617] dark:text-[#EAF3EA]">
              Creada ✅ (recarga para editar/eliminar)
            </span>
          </div>
        </div>
      `;
      list?.insertAdjacentHTML("afterbegin", rowHtml);

      newName.value = "";

    } catch (err) {
      showCreateError("Error de red o JavaScript.");
    } finally {
      btnCreate.disabled = false;
      btnCreate.classList.remove("opacity-60");
    }
  });
});
</script>
@endsection