@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">

  <div class="w-full max-w-md
              bg-white/80 dark:bg-[#0B1A10]/70
              border border-[#DDEEDD] dark:border-[#16351F]
              backdrop-blur-xl rounded-2xl p-8
              shadow-[0px_26px_80px_-28px_rgba(18,54,23,0.55)]">

      <h1 class="text-xl font-semibold text-[#123617] dark:text-[#92b95d] mb-2">
          Recuperar contraseña
      </h1>

      <p class="text-sm text-[#3b6a33]/80 dark:text-[#EAF3EA]/70 mb-6">
          Ingresa tu usuario corporativo para enviarte el enlace de recuperación.
      </p>

      {{-- ✅ Mensaje de éxito --}}
      @if (session('status'))
          <div class="mb-4 p-3 rounded-lg border
                      border-green-300 bg-green-100
                      text-green-800 text-sm">
              ✔ {{ session('status') }}
          </div>
      @endif

      {{-- ❌ Errores --}}
      @if ($errors->any())
          <div class="mb-4 p-3 rounded-lg border
                      border-red-300 bg-red-100
                      text-red-700 text-sm">
              @foreach ($errors->all() as $error)
                  <div>• {{ $error }}</div>
              @endforeach
          </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}" class="space-y-4" id="resetForm">
          @csrf

          {{-- Usuario + dominio obligatorio --}}
          <div>
              <label class="block mb-1.5 text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                  Usuario corporativo
              </label>

              <div class="flex w-full overflow-hidden rounded-xl
                          border border-[#CFE6C9] dark:border-[#1B3B22]
                          bg-[#F6FBF4] dark:bg-[#07120A]
                          focus-within:ring-4 focus-within:ring-[#81a553]/20
                          focus-within:border-[#81a553]
                          transition-all">

                  <input
                      id="username"
                      type="text"
                      required
                      autocomplete="username"
                      placeholder="Usuario"
                      class="flex-1 px-4 py-2.5 outline-none bg-transparent
                             text-[#0f1a12] dark:text-[#EAF3EA]"
                  >

                  <div class="flex items-center px-3 select-none
                              text-[#3b6a33]/70 dark:text-[#EAF3EA]/60
                              border-l border-[#CFE6C9] dark:border-[#1B3B22]">
                      @dispensariolaherradura.cl
                  </div>
              </div>

              <input type="hidden" name="email" id="email">
          </div>

          <button type="submit"
              class="w-full py-3 rounded-xl font-semibold
                     bg-[#1e4e25] hover:bg-[#123617]
                     text-white transition">
              Enviar enlace
          </button>

          <div class="text-center text-sm mt-4">
              <a href="{{ route('login') }}"
                 class="text-[#1e4e25] dark:text-[#92b95d] hover:underline">
                 Volver al login
              </a>
          </div>

      </form>

  </div>

</div>

<script>
(function initCorporateEmail() {
    const usernameInput = document.getElementById('username');
    const emailHidden = document.getElementById('email');
    const form = document.getElementById('resetForm');
    const DOMAIN = '@dispensariolaherradura.cl';

    if (!usernameInput || !emailHidden) return;

    const normalize = () => {
        let v = (usernameInput.value || '').trim().toLowerCase();

        if (v.includes('@')) v = v.split('@')[0];
        v = v.replace(/[^a-z0-9._-]/g, '');

        usernameInput.value = v;
        emailHidden.value = v ? v + DOMAIN : '';
    };

    usernameInput.addEventListener('keydown', e => {
        if (e.key === '@') e.preventDefault();
    });

    usernameInput.addEventListener('input', normalize);
    usernameInput.addEventListener('blur', normalize);
    form.addEventListener('submit', normalize);

    normalize();
})();
</script>

@endsection