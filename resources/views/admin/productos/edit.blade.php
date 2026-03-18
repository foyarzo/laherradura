@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <div>
        <h1 class="text-2xl font-semibold text-[#123617] dark:text-[#92b95d]">
            Editar Producto
        </h1>
        <p class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
            Modifica la información del producto
        </p>
    </div>

    <form method="POST"
          action="{{ route('admin.productos.update', $product) }}"
          enctype="multipart/form-data"
          class="rounded-2xl p-8
                 bg-white/90 dark:bg-[#0B1A10]/80
                 border border-[#DDEEDD] dark:border-[#16351F]
                 shadow-[0px_18px_50px_-20px_rgba(18,54,23,0.45)]
                 space-y-6">

        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Nombre --}}
            <div>
                <label class="block text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                    Nombre
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $product->name) }}"
                       class="mt-2 w-full rounded-xl px-4 py-2
                              border border-[#CFE6C9] dark:border-[#1B3B22]
                              bg-[#F6FBF4] dark:bg-[#07120A]
                              focus:ring-4 focus:ring-[#81a553]/20
                              focus:border-[#81a553]
                              transition"
                       required>
                @error('name')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            {{-- Categoría --}}
            <div>
                <label class="block text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                    Categoría
                </label>

                <select name="category_id"
                        class="mt-2 w-full rounded-xl px-4 py-2
                               border border-[#CFE6C9] dark:border-[#1B3B22]
                               bg-[#F6FBF4] dark:bg-[#07120A]
                               focus:ring-4 focus:ring-[#81a553]/20
                               focus:border-[#81a553]
                               transition">
                    <option value="">Sin categoría</option>

                    @foreach(($categories ?? []) as $cat)
                        <option value="{{ $cat->id }}"
                            {{ (string)old('category_id', $product->category_id) === (string)$cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <p class="mt-1 text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                    Selecciona una categoría para organizar el producto (opcional).
                </p>

                @error('category_id')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            {{-- SKU --}}
            <div>
                <label class="block text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                    SKU
                </label>
                <input type="text"
                       name="sku"
                       value="{{ old('sku', $product->sku) }}"
                       class="mt-2 w-full rounded-xl px-4 py-2
                              border border-[#CFE6C9] dark:border-[#1B3B22]
                              bg-[#F6FBF4] dark:bg-[#07120A]">
                @error('sku')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            {{-- Precio --}}
            <div>
                <label class="block text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                    Precio
                </label>
                <input type="number"
                       name="price"
                       value="{{ old('price', $product->price) }}"
                       min="0"
                       class="mt-2 w-full rounded-xl px-4 py-2
                              border border-[#CFE6C9] dark:border-[#1B3B22]
                              bg-[#F6FBF4] dark:bg-[#07120A]"
                       required>
                @error('price')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            {{-- Stock tienda --}}
            <div>
                <label class="block text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                    Stock (tienda)
                </label>
                <input type="number"
                       name="stock"
                       value="{{ old('stock', $product->stock) }}"
                       min="0"
                       class="mt-2 w-full rounded-xl px-4 py-2
                              border border-[#CFE6C9] dark:border-[#1B3B22]
                              bg-[#F6FBF4] dark:bg-[#07120A]"
                       required>
                @error('stock')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

            {{-- Stock bodega --}}
            <div>
                <label class="block text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                    Stock bodega
                </label>
                <input type="number"
                       name="stock_bodega"
                       value="{{ old('stock_bodega', $product->stock_bodega ?? 0) }}"
                       min="0"
                       class="mt-2 w-full rounded-xl px-4 py-2
                              border border-[#CFE6C9] dark:border-[#1B3B22]
                              bg-[#F6FBF4] dark:bg-[#07120A]"
                       required>
                @error('stock_bodega')
                    <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                @enderror
            </div>

        </div>

        {{-- Descripción --}}
        <div>
            <label class="block text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                Descripción
            </label>
            <textarea name="description"
                      rows="4"
                      class="mt-2 w-full rounded-xl px-4 py-2
                             border border-[#CFE6C9] dark:border-[#1B3B22]
                             bg-[#F6FBF4] dark:bg-[#07120A]
                             focus:ring-4 focus:ring-[#81a553]/20
                             focus:border-[#81a553]
                             transition"
                      placeholder="Describe el producto...">{{ old('description', $product->description) }}</textarea>
            @error('description')
                <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
            @enderror
        </div>

        {{-- Imagen --}}
        <div class="space-y-4">

            <label class="block text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                Imagen del Producto
            </label>

            <div class="flex flex-col md:flex-row gap-6 items-start">

                <div class="w-40 h-40 rounded-2xl overflow-hidden
                            border border-[#DDEEDD] dark:border-[#16351F]
                            bg-white dark:bg-[#07120A] shadow-inner">
                    @php
                        $fallback = asset('assets/img/no_image.png');
                        $currentImage = $product->image ? asset($product->image) : $fallback;
                    @endphp

                    <img id="imagePreview"
                         src="{{ $currentImage }}"
                         class="w-full h-full object-cover"
                         alt="Imagen actual">
                </div>

                <div class="flex-1">
                    <input type="file"
                           name="image"
                           accept="image/*"
                           id="image"
                           class="block w-full text-sm
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-xl file:border-0
                                  file:bg-[#1e4e25] file:text-white
                                  hover:file:bg-[#123617]
                                  transition">

                    @error('image')
                        <div class="mt-1 text-xs text-red-600">{{ $message }}</div>
                    @enderror

                    <p class="text-xs mt-2 text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                        Si no seleccionas una nueva imagen, se mantendrá la actual.
                    </p>
                </div>

            </div>

        </div>

        {{-- Activo --}}
        <div class="flex items-center gap-3">
            <input type="checkbox"
                   name="is_active"
                   value="1"
                   class="w-4 h-4 accent-[#5e8d42]"
                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>

            <label class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                Producto Activo
            </label>
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl
                           bg-[#1e4e25] hover:bg-[#123617]
                           text-white font-semibold
                           shadow-md
                           transition">
                Guardar Cambios
            </button>
        </div>

    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('image');
    const preview = document.getElementById('imagePreview');

    if (!input || !preview) return;

    input.addEventListener('change', () => {
        const file = input.files && input.files[0];
        if (!file) return;

        const url = URL.createObjectURL(file);
        preview.src = url;
        preview.onload = () => URL.revokeObjectURL(url);
    });
});
</script>
@endsection