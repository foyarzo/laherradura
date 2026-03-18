@extends('layouts.app')

@section('content')

<div class="min-h-[calc(100vh-100px)] flex items-center justify-center
            px-4 py-8 bg-[#F6FBF4] dark:bg-[#07120A]">

  <div class="w-full max-w-2xl">

    <div class="rounded-2xl p-6 sm:p-8
                bg-white/90 dark:bg-[#0B1A10]/80 backdrop-blur
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_18px_50px_-20px_rgba(18,54,23,0.45)]">

      <h1 class="text-xl sm:text-2xl font-bold
                 text-[#123617] dark:text-[#92b95d] mb-6">
        Editar Mensaje de Bienvenida
      </h1>

      @if(session('success'))
        <div class="mb-4 p-3 rounded-xl
                    bg-green-50 dark:bg-green-900/20
                    border border-green-400 text-green-700 dark:text-green-300">
          {{ session('success') }}
        </div>
      @endif

      <form method="POST" action="{{ route('admin.mensaje.update') }}">
        @csrf

        <div>
          <label class="block text-sm font-semibold
                        text-[#123617] dark:text-[#92b95d] mb-2">
            Contenido del mensaje
          </label>

          <textarea
              name="mensaje"
              rows="8"
              class="w-full rounded-xl border
                     border-[#DDEEDD] dark:border-[#16351F]
                     bg-white dark:bg-[#07120A]
                     p-4 text-sm text-[#123617] dark:text-[#EAF3EA]
                     focus:outline-none focus:ring-2 focus:ring-[#92b95d]">
              {{ old('mensaje', $mensaje) }}
          </textarea>

          @error('mensaje')
            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
          @enderror
        </div>

        <div class="mt-6 flex justify-end">
          <button type="submit"
                  class="px-6 py-2 rounded-xl
                         bg-[#123617] text-white
                         hover:bg-[#0f2c13]
                         transition text-sm">
            Guardar cambios
          </button>
        </div>
      </form>

    </div>

  </div>
</div>

@endsection