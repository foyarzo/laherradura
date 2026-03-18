@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6
            bg-[#F6FBF4] dark:bg-[#07120A]">

  <div class="w-full max-w-md
              bg-white/90 dark:bg-[#0B1A10]/80
              border border-[#DDEEDD] dark:border-[#16351F]
              backdrop-blur-xl rounded-2xl p-8
              shadow-[0px_26px_80px_-28px_rgba(18,54,23,0.55)]">

    {{-- Header --}}
    <div class="mb-6">
      <h1 class="text-2xl font-semibold tracking-tight
                 text-[#123617] dark:text-[#92b95d]">
        Restablecer contraseña
      </h1>

      <p class="text-sm mt-2
                text-[#3b6a33]/80 dark:text-[#EAF3EA]/70">
        Crea una nueva contraseña segura para continuar.
      </p>
    </div>

    {{-- Mensaje éxito --}}
    @if (session('status'))
      <div class="mb-4 p-3 rounded-xl border
                  border-[#92b95d]/40
                  bg-[#EAF6E7] dark:bg-[#0A2012]
                  text-[#123617] dark:text-[#92b95d]
                  text-sm">
        ✔ {{ session('status') }}
      </div>
    @endif

    {{-- Errores --}}
    @if ($errors->any())
      <div class="mb-4 p-3 rounded-xl border
                  border-red-400/40
                  bg-red-50 dark:bg-red-900/20
                  text-red-700 dark:text-red-300
                  text-sm">
        @foreach ($errors->all() as $error)
          <div>• {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4" id="resetForm">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      {{-- Usuario + dominio fijo --}}
      <div>
        <label class="block mb-1.5 text-[13px] font-semibold text-[#123617] dark:text-[#EAF3EA]">
          Usuario corporativo
        </label>

        <div class="group flex w-full overflow-hidden rounded-xl
                    border border-[#CFE6C9] dark:border-[#1B3B22]
                    bg-[#F6FBF4] dark:bg-[#07120A]
                    shadow-[0px_1px_0px_rgba(18,54,23,0.06)]
                    focus-within:ring-4 focus-within:ring-[#92b95d]/20
                    focus-within:border-[#92b95d]
                    transition-all">

          <input
            id="username"
            type="text"
            required
            autocomplete="username"
            autocapitalize="none"
            spellcheck="false"
            placeholder="Usuario"
            value="{{ old('email', $email ?? '') ? preg_replace('/@.*/', '', old('email', $email ?? '')) : '' }}"
            class="flex-1 px-4 py-2.5 outline-none bg-transparent
                   text-[#123617] dark:text-[#EAF3EA]
                   placeholder:text-[#3b6a33]/55 dark:placeholder:text-[#EAF3EA]/35"
          >

          <div class="flex items-center gap-2 px-3 select-none
                      text-[#3b6a33]/70 dark:text-[#EAF3EA]/60
                      border-l border-[#CFE6C9] dark:border-[#1B3B22]
                      bg-white/50 dark:bg-white/5">
            <span class="font-mono text-[13px]">@</span>
            <span class="text-[12px] lg:text-[13px]">dispensariolaherradura.cl</span>
          </div>
        </div>

        {{-- email real que se envía --}}
        <input type="hidden" name="email" id="email" value="{{ old('email', $email ?? '') }}">

        <div class="mt-2 text-[12px] text-[#3b6a33]/70 dark:text-[#EAF3EA]/50">
          Tip: escribe solo el usuario (antes del @).
        </div>
      </div>

      {{-- Nueva contraseña --}}
      <input type="password"
             name="password"
             required
             autocomplete="new-password"
             placeholder="Nueva contraseña"
             class="w-full rounded-xl px-4 py-2.5
                    bg-[#F6FBF4] dark:bg-[#07120A]
                    border border-[#CFE6C9] dark:border-[#1B3B22]
                    text-[#123617] dark:text-[#EAF3EA]
                    placeholder:text-[#3b6a33]/55 dark:placeholder:text-[#EAF3EA]/35
                    outline-none
                    focus:ring-4 focus:ring-[#92b95d]/20
                    focus:border-[#92b95d]
                    transition">

      {{-- Confirmación --}}
      <input type="password"
             name="password_confirmation"
             required
             autocomplete="new-password"
             placeholder="Confirmar nueva contraseña"
             class="w-full rounded-xl px-4 py-2.5
                    bg-[#F6FBF4] dark:bg-[#07120A]
                    border border-[#CFE6C9] dark:border-[#1B3B22]
                    text-[#123617] dark:text-[#EAF3EA]
                    placeholder:text-[#3b6a33]/55 dark:placeholder:text-[#EAF3EA]/35
                    outline-none
                    focus:ring-4 focus:ring-[#92b95d]/20
                    focus:border-[#92b95d]
                    transition">

      {{-- Botón --}}
      <button type="submit"
              class="w-full py-3 rounded-xl font-semibold
                     bg-[#1e4e25] hover:bg-[#123617]
                     text-white
                     shadow-[0px_14px_34px_-18px_rgba(18,54,23,0.95)]
                     transition-all active:translate-y-[1px]">
        Guardar contraseña
      </button>

      {{-- Seguridad --}}
      <div class="text-xs text-center
                  text-[#3b6a33]/70 dark:text-[#EAF3EA]/50 mt-2">
        Usa al menos 8 caracteres, incluyendo mayúsculas y números.
      </div>
    </form>
  </div>
</div>

<script>
(function initCorporateEmailReset() {
  const usernameInput = document.getElementById('username');
  const emailHidden = document.getElementById('email');
  const form = document.getElementById('resetForm');
  const DOMAIN = '@dispensariolaherradura.cl';

  if (!usernameInput || !emailHidden) return;

  const normalize = () => {
    let v = (usernameInput.value || '').trim();

    // Si pegan un email completo, toma solo el user
    if (v.includes('@')) v = v.split('@')[0];

    // normaliza
    v = v.toLowerCase().replace(/\s+/g, '');
    // permite letras/números/punto/guion/underscore
    v = v.replace(/[^a-z0-9._-]/g, '');

    usernameInput.value = v;
    emailHidden.value = v ? (v + DOMAIN) : '';
  };

  // bloquear ingreso de '@'
  usernameInput.addEventListener('keydown', (e) => {
    if (e.key === '@') e.preventDefault();
  });

  usernameInput.addEventListener('input', normalize);
  usernameInput.addEventListener('blur', normalize);
  usernameInput.addEventListener('paste', () => setTimeout(normalize, 0));

  if (form) form.addEventListener('submit', normalize);

  normalize();
})();
</script>
@endsection