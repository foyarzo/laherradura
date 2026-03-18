@extends('layouts.app')

@section('content')
<div class="max-w-3xl space-y-6">

<div id="loaderIA"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-[#0B1A10] rounded-2xl p-8 flex flex-col items-center gap-4 shadow-2xl border border-[#DDEEDD] dark:border-[#16351F]">
        <div class="w-14 h-14 border-4 border-[#92b95d]/30 border-t-[#92b95d] rounded-full animate-spin"></div>
        <p class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA] text-center">
            Generando contenido con IA...
            <br>
            <span class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Esto puede tardar unos segundos
            </span>
        </p>
    </div>
</div>

    <div>
        <h1 class="text-2xl font-semibold text-[#123617] dark:text-[#92b95d]">
            Crear Producto
        </h1>
    </div>

    <form method="POST"
          enctype="multipart/form-data"
          action="{{ route('admin.productos.store') }}"
          class="rounded-2xl p-6 bg-white/90 dark:bg-[#0B1A10]/80
                 border border-[#DDEEDD] dark:border-[#16351F]
                 space-y-5">

        @csrf

        <div>
            <label class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">Nombre</label>
            <input type="text"
                   name="name"
                   id="name"
                   value="{{ old('name') }}"
                   class="mt-2 w-full rounded-lg border px-4 py-2
                          bg-white dark:bg-[#07120A]/60
                          border-[#DDEEDD] dark:border-[#16351F]
                          text-[#123617] dark:text-[#EAF3EA]"
                   required>
            @error('name')
                <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
        </div>

        {{-- ✅ Categoría + botón modal --}}
        <div>
            <div class="flex items-center justify-between gap-3">
                <label class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">Categoría</label>

                <button type="button"
                        id="btnOpenCategoryModal"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold
                               bg-white/85 dark:bg-[#07120A]/55
                               border border-[#DDEEDD] dark:border-[#16351F]
                               text-[#123617] dark:text-[#EAF3EA]
                               hover:bg-black/5 dark:hover:bg-white/5 transition">
                    <svg class="w-4 h-4 opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Crear categoría
                </button>
            </div>

            <select name="category_id"
                    id="category_id"
                    class="mt-2 w-full rounded-lg border px-4 py-2
                           bg-white dark:bg-[#07120A]/60
                           border-[#DDEEDD] dark:border-[#16351F]
                           text-[#123617] dark:text-[#EAF3EA]">
                <option value="">Sin categoría</option>

                @foreach(($categories ?? []) as $cat)
                    <option value="{{ $cat->id }}"
                        {{ (string)old('category_id') === (string)$cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <p class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mt-1">
                Selecciona una categoría para organizar el producto (opcional).
            </p>

            @error('category_id')
                <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
        </div>

        {{-- 🔥 Descripción con IA --}}
        <div>
            <label class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">Descripción</label>

            <textarea name="description"
                      id="description"
                      rows="4"
                      class="mt-2 w-full rounded-lg border px-4 py-2 resize-y
                             bg-white dark:bg-[#07120A]/60
                             border-[#DDEEDD] dark:border-[#16351F]
                             text-[#123617] dark:text-[#EAF3EA]"
                      placeholder="Describe el producto (opcional)">{{ old('description') }}</textarea>

            <button type="button"
                    id="btnGenerarIA"
                    class="mt-2 px-4 py-2 text-sm rounded-lg bg-[#92b95d] hover:bg-[#7da34f] text-[#07120A] font-semibold transition">
                Generar descripción
            </button>

            <p class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mt-1">
                La IA generará una descripción automática basada en el nombre del producto.
            </p>

            @error('description')
                <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">Precio</label>
            <input type="number"
                   name="price"
                   value="{{ old('price', 0) }}"
                   min="0"
                   class="mt-2 w-full rounded-lg border px-4 py-2
                          bg-white dark:bg-[#07120A]/60
                          border-[#DDEEDD] dark:border-[#16351F]
                          text-[#123617] dark:text-[#EAF3EA]"
                   required>
            @error('price')
                <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">Stock (tienda)</label>
                <input type="number"
                       name="stock"
                       value="{{ old('stock', 0) }}"
                       min="0"
                       class="mt-2 w-full rounded-lg border px-4 py-2
                              bg-white dark:bg-[#07120A]/60
                              border-[#DDEEDD] dark:border-[#16351F]
                              text-[#123617] dark:text-[#EAF3EA]"
                       required>
                @error('stock')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">Stock bodega</label>
                <input type="number"
                       name="stock_bodega"
                       value="{{ old('stock_bodega', 0) }}"
                       min="0"
                       class="mt-2 w-full rounded-lg border px-4 py-2
                              bg-white dark:bg-[#07120A]/60
                              border-[#DDEEDD] dark:border-[#16351F]
                              text-[#123617] dark:text-[#EAF3EA]"
                       required>
                @error('stock_bodega')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div>
            <label class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">SKU</label>
            <input type="text"
                   name="sku"
                   id="sku"
                   value="{{ old('sku') }}"
                   class="mt-2 w-full rounded-lg border px-4 py-2
                          bg-white dark:bg-[#07120A]/60
                          border-[#DDEEDD] dark:border-[#16351F]
                          text-[#123617] dark:text-[#EAF3EA]">
            <p class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mt-1">
                Si lo dejas vacío se generará automáticamente.
            </p>
            @error('sku')
                <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
        </div>

        {{-- Imagen + IA --}}
        <div>
            <label class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">Imagen</label>

            <input type="file"
                   name="image"
                   id="image"
                   accept="image/*"
                   class="mt-2 w-full rounded-lg border px-4 py-2
                          bg-white dark:bg-[#07120A]/60
                          border-[#DDEEDD] dark:border-[#16351F]
                          text-[#123617] dark:text-[#EAF3EA]">

            <input type="hidden" name="ai_image_path" id="ai_image_path" value="{{ old('ai_image_path') }}">

            <div class="flex items-center gap-2 mt-2">
                <button type="button"
                        id="btnGenerarImgIA"
                        class="px-4 py-2 text-sm rounded-lg bg-[#92b95d] hover:bg-[#7da34f] text-[#07120A] font-semibold transition">
                    Generar imagen con IA
                </button>

                <button type="button"
                        id="btnLimpiarImgIA"
                        class="px-4 py-2 text-sm rounded-lg border border-[#DDEEDD] dark:border-[#16351F]
                               text-[#123617] dark:text-[#EAF3EA] hover:bg-black/5 dark:hover:bg-white/5 transition">
                    Quitar imagen IA
                </button>
            </div>

            <div id="previewWrap" class="mt-3 hidden">
                <img id="previewImg"
                     class="w-40 h-40 object-cover rounded-xl border border-[#DDEEDD] dark:border-[#16351F]"
                     alt="Preview">
            </div>

            <div id="previewIAWrap" class="mt-3 hidden">
                <img id="previewIAImg"
                     class="w-40 h-40 object-cover rounded-xl border border-[#DDEEDD] dark:border-[#16351F]"
                     alt="Preview IA">
                <p class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mt-1">
                    Imagen generada por IA (se usará si no subes una imagen manual).
                </p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
            <label class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">Producto Activo</label>
        </div>

        <button type="submit"
                class="px-6 py-2 rounded-lg
                       bg-[#1e4e25] text-white
                       hover:bg-[#123617] transition">
            Crear Producto
        </button>

    </form>
</div>

{{-- ✅ MODAL CREAR CATEGORÍA (solo para esta vista) --}}
<div id="categoryModal" class="fixed inset-0 z-[90] hidden flex items-center justify-center" aria-hidden="true">
    <div data-close-category-modal class="absolute inset-0 bg-black/45 backdrop-blur-sm"></div>

    <div role="dialog" aria-modal="true" aria-labelledby="categoryModalTitle"
         class="relative w-full max-w-md mx-4
                rounded-2xl
                bg-white dark:bg-[#0B1A10]
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_24px_70px_-28px_rgba(0,0,0,0.65)]
                overflow-hidden">

        <div class="px-5 py-4 border-b border-[#DDEEDD] dark:border-[#16351F]
                    bg-[#F6FBF4] dark:bg-[#07120A]/55">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <h2 id="categoryModalTitle" class="text-base font-semibold text-[#123617] dark:text-[#EAF3EA]">
                        Crear Categoría
                    </h2>
                    <p class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                        Se agregará automáticamente al selector.
                    </p>
                </div>

                <button type="button" data-close-category-modal
                        class="rounded-xl px-3 py-2 text-xs font-semibold
                               border border-[#DDEEDD] dark:border-[#16351F]
                               bg-white/80 dark:bg-[#07120A]/55
                               hover:bg-black/5 dark:hover:bg-white/5 transition">
                    Cerrar
                </button>
            </div>
        </div>

        <form id="categoryForm" class="p-5 space-y-4">
            @csrf

            <div>
                <label class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">Nombre</label>
                <input type="text"
                       id="category_name"
                       class="mt-2 w-full rounded-lg border px-4 py-2
                              bg-white dark:bg-[#07120A]/60
                              border-[#DDEEDD] dark:border-[#16351F]
                              text-[#123617] dark:text-[#EAF3EA]"
                       placeholder="Ej: Flores, Extractos..."
                       required>
                <div id="categoryError" class="mt-1 text-xs text-red-600 hidden"></div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-1">
                <button type="button" data-close-category-modal
                        class="px-4 py-2 rounded-lg text-sm font-semibold
                               border border-[#DDEEDD] dark:border-[#16351F]
                               bg-white/80 dark:bg-[#07120A]/55
                               text-[#123617] dark:text-[#EAF3EA]
                               hover:bg-black/5 dark:hover:bg-white/5 transition">
                    Cancelar
                </button>

                <button type="submit"
                        id="btnSaveCategory"
                        class="px-4 py-2 rounded-lg text-sm font-semibold
                               bg-[#1e4e25] hover:bg-[#123617] text-white
                               dark:bg-[#92b95d] dark:hover:bg-[#81a553]
                               dark:text-[#07120A]
                               transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const nameInput = document.getElementById("name");
  const skuInput  = document.getElementById("sku");
  const imgInput  = document.getElementById("image");

  const previewWrap = document.getElementById("previewWrap");
  const previewImg  = document.getElementById("previewImg");

  const btnIA = document.getElementById("btnGenerarIA");
  const descriptionInput = document.getElementById("description");

  const btnImgIA = document.getElementById("btnGenerarImgIA");
  const btnLimpiarImgIA = document.getElementById("btnLimpiarImgIA");
  const aiImagePathInput = document.getElementById("ai_image_path");
  const previewIAWrap = document.getElementById("previewIAWrap");
  const previewIAImg = document.getElementById("previewIAImg");

  // ✅ Loader overlay (debe existir en el DOM: #loaderIA)
  const loaderIA = document.getElementById("loaderIA");
  const showLoader = () => { if (loaderIA) loaderIA.classList.remove("hidden"); };
  const hideLoader = () => { if (loaderIA) loaderIA.classList.add("hidden"); };

  // ✅ Evita “doble hide” si algún flujo se encadena
  let pendingIA = 0;
  const lockUI = () => { pendingIA++; showLoader(); };
  const unlockUI = () => { pendingIA = Math.max(0, pendingIA - 1); if (pendingIA === 0) hideLoader(); };

  const missing = [];
  if (!nameInput) missing.push("#name");
  if (!skuInput) missing.push("#sku");
  if (!imgInput) missing.push("#image");
  if (!previewWrap) missing.push("#previewWrap");
  if (!previewImg) missing.push("#previewImg");
  if (!btnIA) missing.push("#btnGenerarIA");
  if (!descriptionInput) missing.push("#description");
  if (missing.length) return;

  // =========================
  // ✅ MODAL CATEGORÍA (AJAX)
  // =========================
  const modal = document.getElementById("categoryModal");
  const openBtn = document.getElementById("btnOpenCategoryModal");
  const closeBtns = document.querySelectorAll("[data-close-category-modal]");
  const categoryForm = document.getElementById("categoryForm");
  const categoryName = document.getElementById("category_name");
  const categoryError = document.getElementById("categoryError");
  const btnSaveCategory = document.getElementById("btnSaveCategory");
  const categorySelect = document.getElementById("category_id");

  const openModal = () => {
    categoryError?.classList.add("hidden");
    categoryError && (categoryError.textContent = "");
    categoryName && (categoryName.value = "");
    modal?.classList.remove("hidden");
    setTimeout(() => categoryName?.focus(), 50);
  };
  const closeModal = () => modal?.classList.add("hidden");

  openBtn?.addEventListener("click", openModal);
  closeBtns.forEach(btn => btn.addEventListener("click", closeModal));
  document.addEventListener("keydown", (e) => { if (e.key === "Escape") closeModal(); });

  const getCsrf = () => {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    return csrfMeta ? csrfMeta.getAttribute("content") : null;
  };

  const safeJson = (raw) => { try { return JSON.parse(raw); } catch { return null; } };

  const showCatError = (msg) => {
    if (!categoryError) return;
    categoryError.textContent = msg || "Ocurrió un error.";
    categoryError.classList.remove("hidden");
  };

  categoryForm?.addEventListener("submit", async (e) => {
    e.preventDefault();

    const name = (categoryName?.value || "").trim();
    if (!name) { showCatError("Escribe un nombre de categoría."); return; }

    const csrf = getCsrf();
    if (!csrf) { showCatError("Falta CSRF token."); return; }

    btnSaveCategory.disabled = true;
    btnSaveCategory.classList.add("opacity-60");

    try {
      const response = await fetch("{{ route('admin.categories.store') }}", {
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
        // Laravel validation JSON: { message, errors: { name: [...] } }
        const msg = data?.errors?.name?.[0] || data?.message || `Error ${response.status}`;
        showCatError(msg);
        return;
      }

      if (!data?.ok || !data?.category?.id) {
        showCatError("Respuesta inválida al crear categoría.");
        return;
      }

      // ✅ Insertar en select y seleccionar
      const opt = document.createElement("option");
      opt.value = String(data.category.id);
      opt.textContent = data.category.name || name;
      categorySelect.appendChild(opt);
      categorySelect.value = opt.value;

      closeModal();

    } catch (err) {
      showCatError("Error de red o JavaScript.");
    } finally {
      btnSaveCategory.disabled = false;
      btnSaveCategory.classList.remove("opacity-60");
    }
  });

  // =========================
  // ✅ SKU auto
  // =========================
  let manualEdit = false;

  skuInput.addEventListener("input", () => {
    manualEdit = skuInput.value.trim().length > 0;
  });

  nameInput.addEventListener("input", () => {
    if (manualEdit) return;

    const prefix = "LHW";
    let base = nameInput.value
      .toUpperCase()
      .replace(/[^A-Z0-9]/g, "")
      .substring(0, 10);

    if (!base) {
      skuInput.value = "";
      return;
    }

    const random = Math.random().toString(36).substring(2, 6).toUpperCase();
    skuInput.value = `${prefix}-${base}-${random}`;
  });

  // =========================
  // ✅ Preview imagen manual
  // =========================
  imgInput.addEventListener("change", () => {
    const file = imgInput.files && imgInput.files[0];
    if (!file) {
      previewWrap.classList.add("hidden");
      previewImg.removeAttribute("src");
      return;
    }

    const url = URL.createObjectURL(file);
    previewImg.src = url;
    previewWrap.classList.remove("hidden");
    previewImg.onload = () => URL.revokeObjectURL(url);
  });

  const pickErrorMessage = (j, fallback = "Ocurrió un error.") => {
    if (!j) return fallback;
    return (j.error_msg || j.error || j.message || j?.error?.message || j?.error?.error || fallback);
  };

  // IA descripción
  btnIA.addEventListener("click", async () => {
    const name = nameInput.value.trim();
    if (!name) { alert("Primero escribe el nombre del producto"); return; }

    const csrf = getCsrf();
    if (!csrf) { alert("Falta CSRF token en el layout. Revisa <meta name='csrf-token'>"); return; }

    const url = "{{ route('admin.ai.generar.descripcion') }}";

    const originalText = btnIA.innerText;
    btnIA.innerText = "Generando...";
    btnIA.disabled = true;
    lockUI();

    try {
      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": csrf
        },
        body: JSON.stringify({ name })
      });

      const raw = await response.text();

      if (!response.ok) {
        const j = safeJson(raw);
        const msg = pickErrorMessage(j, `Error ${response.status}`);
        alert(msg);
        return;
      }

      const data = safeJson(raw);
      if (!data || typeof data.descripcion !== "string") {
        alert("La API respondió pero no trajo 'descripcion'.");
        return;
      }

      let desc = data.descripcion;

      const hasNewlines = desc.includes("\n");
      if (!hasNewlines) {
        desc = desc.replace(/\s+/g, " ").trim();
        desc = desc
          .replace(/Título:/g, '\nTítulo:')
          .replace(/Tipo:/g, '\nTipo:')
          .replace(/THC:/g, '\nTHC:')
          .replace(/CBD:/g, '\nCBD:')
          .replace(/Terpenos probables:/g, '\nTerpenos probables:')
          .replace(/Aromas y sabores:/g, '\nAromas y sabores:')
          .replace(/Efectos esperables:/g, '\nEfectos esperables:')
          .replace(/Usos terapéuticos habituales:/g, '\nUsos terapéuticos habituales:')
          .replace(/Recomendación de uso:/g, '\nRecomendación de uso:')
          .replace(/Nota:/g, '\nNota:')
          .trim();
      } else {
        desc = desc.replace(/\r\n/g, "\n").trim();
      }

      descriptionInput.value = desc;

    } catch (error) {
      alert("Error de red o JavaScript.");
    } finally {
      btnIA.innerText = originalText || "✨ Generar descripción con IA";
      btnIA.disabled = false;
      unlockUI();
    }
  });

  // IA imagen
  if (btnImgIA && aiImagePathInput && previewIAWrap && previewIAImg) {
    btnImgIA.addEventListener("click", async () => {
      const name = nameInput.value.trim();
      const description = (descriptionInput.value || "").trim();

      if (!name) { alert("Primero escribe el nombre del producto"); return; }

      const csrf = getCsrf();
      if (!csrf) { alert("Falta CSRF token en el layout."); return; }

      const url = "{{ route('admin.ai.generar.imagen') }}";

      const original = btnImgIA.innerText;
      btnImgIA.innerText = "Generando imagen...";
      btnImgIA.disabled = true;
      lockUI();

      try {
        const response = await fetch(url, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": csrf
          },
          body: JSON.stringify({ name, description })
        });

        const raw = await response.text();

        if (!response.ok) {
          const j = safeJson(raw);
          const msg = pickErrorMessage(j, "No se pudo generar la imagen.");
          const debugUrl = j?.debug_url ? `\n\nDebug URL:\n${j.debug_url}` : "";
          alert(`Imagen IA falló (${response.status}):\n${msg}${debugUrl}`);
          return;
        }

        const data = safeJson(raw);
        if (!data) { alert(`Imagen IA: respuesta inválida`); return; }

        if (!data.ok || !data.url || !data.path) {
          const msg = pickErrorMessage(data, "No se pudo generar la imagen.");
          alert(`Imagen IA: ${msg}`);
          return;
        }

        aiImagePathInput.value = data.path;
        previewIAImg.src = data.url;
        previewIAWrap.classList.remove("hidden");

      } catch (e) {
        alert("Error de red o JavaScript.");
      } finally {
        btnImgIA.innerText = original || "Generar imagen con IA";
        btnImgIA.disabled = false;
        unlockUI();
      }
    });

    btnLimpiarImgIA?.addEventListener("click", () => {
      aiImagePathInput.value = "";
      previewIAImg.removeAttribute("src");
      previewIAWrap.classList.add("hidden");
    });
  }
});
</script>
@endsection