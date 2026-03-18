<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'La Herradura') }}</title>

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

  <!-- AOS Animations -->
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @endif

  @unless (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    <style>
      @layer theme{:root,:host{--font-sans:'Instrument Sans',ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";--font-serif:ui-serif,Georgia,Cambria,"Times New Roman",Times,serif;--font-mono:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;--color-black:#000;--color-white:#fff;--spacing:.25rem;--breakpoint-sm:40rem;--breakpoint-md:48rem;--breakpoint-lg:64rem;--breakpoint-xl:80rem;--breakpoint-2xl:96rem;--container-4xl:56rem;--text-xs:.75rem;--text-sm:.875rem;--font-weight-medium:500;--leading-normal:1.5;--radius-md:.375rem;--radius-lg:.5rem;--radius-xl:.75rem;--radius-2xl:1rem;--shadow-2xl:0 25px 50px -12px #00000040;--default-font-family:var(--font-sans)}}
      @layer base{*,:after,:before,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}html,:host{-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;line-height:1.5;font-family:var(--default-font-family,ui-sans-serif,system-ui,sans-serif)}body{line-height:inherit}a{color:inherit;text-decoration:inherit}img,svg,video{display:block;max-width:100%;height:auto}button,input{font:inherit;color:inherit;background-color:#0000}}
      @layer utilities{
        .min-h-screen{min-height:100vh}.flex{display:flex}.items-center{align-items:center}.justify-center{justify-content:center}.flex-col{flex-direction:column}.lg\:flex-row{flex-direction:row}
        .p-6{padding:calc(var(--spacing)*6)}.lg\:p-8{padding:calc(var(--spacing)*8)}.gap-4{gap:calc(var(--spacing)*4)}
      }
    </style>
  @endunless
</head>

<body class="bg-[#F6FBF4] dark:bg-[#07120A] text-[#0f1a12] dark:text-[#EAF3EA] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">

  @auth
    <script>
      window.location.href = @json(route('home'));
    </script>
  @endauth

  <header
    data-aos="fade-down"
    data-aos-duration="700"
    class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6">
    <nav class="flex items-center justify-end gap-4">
      @auth
        @if(auth()->user()->hasRole('admin'))
          <a href="{{ route('admin.home') }}"
             class="inline-block px-5 py-1.5 rounded-md text-sm leading-normal
                    bg-white/80 dark:bg-[#0D1E12]/70 backdrop-blur
                    border border-[#CFE6C9] dark:border-[#1B3B22]
                    text-[#123617] dark:text-[#92b95d]
                    shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_8px_22px_-14px_rgba(18,54,23,0.35)]
                    hover:border-[#81a553] dark:hover:border-[#3b6a33]
                    transition-all">
            Panel Admin
          </a>
        @endif

        @if(auth()->user()->hasRole('operador'))
          <a href="{{ route('operador.home') }}"
             class="inline-block px-5 py-1.5 rounded-md text-sm leading-normal
                    bg-white/80 dark:bg-[#0D1E12]/70 backdrop-blur
                    border border-[#CFE6C9] dark:border-[#1B3B22]
                    text-[#123617] dark:text-[#92b95d]
                    shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_8px_22px_-14px_rgba(18,54,23,0.35)]
                    hover:border-[#81a553] dark:hover:border-[#3b6a33]
                    transition-all">
            Panel Operador
          </a>
        @endif

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
                  class="inline-block px-5 py-1.5 rounded-md text-sm leading-normal
                         bg-white/80 dark:bg-[#0D1E12]/70 backdrop-blur
                         border border-[#CFE6C9] dark:border-[#1B3B22]
                         text-[#123617] dark:text-[#EAF3EA]
                         shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_8px_22px_-14px_rgba(18,54,23,0.35)]
                         hover:border-[#81a553] dark:hover:border-[#3b6a33]
                         transition-all">
            Cerrar sesión
          </button>
        </form>
      @endauth
    </nav>
  </header>

<div class="relative flex items-center justify-center w-full lg:grow">
  <!-- glow suave atrás del card -->
  <div class="pointer-events-none absolute inset-0 -z-10 flex items-center justify-center">
    <div class="h-[520px] w-[520px] lg:h-[620px] lg:w-[620px] rounded-full blur-3xl opacity-40
                bg-[radial-gradient(circle_at_30%_30%,rgba(146,185,93,0.55),transparent_55%)]"></div>
  </div>

  <main
    data-aos="zoom-in"
    data-aos-duration="900"
    data-aos-delay="80"
    class="relative flex max-w-[360px] w-full flex-col-reverse lg:max-w-5xl lg:flex-row
           rounded-2xl overflow-hidden
           border border-[#DDEEDD] dark:border-[#16351F]
           bg-white/70 dark:bg-[#0B1A10]/55 backdrop-blur-xl
           shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_26px_80px_-28px_rgba(18,54,23,0.55)]">

    <!-- borde highlight sutil -->
    <div class="pointer-events-none absolute inset-0 rounded-2xl
                shadow-[inset_0px_0px_0px_1px_rgba(146,185,93,0.18)]"></div>

    <!-- Lado formulario -->
    <div
      data-aos="fade-right"
      data-aos-duration="900"
      data-aos-delay="160"
      class="relative flex-1 p-7 pb-10 lg:p-16
             bg-white/85 dark:bg-[#0B1A10]/70 backdrop-blur
             lg:border-r border-[#DDEEDD] dark:border-[#16351F]">

      <!-- header bonito -->
      <div class="mb-7">
        <div class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-[12px]
                    border border-[#CFE6C9] dark:border-[#1B3B22]
                    bg-[#F6FBF4]/70 dark:bg-white/5
                    text-[#1e4e25]/80 dark:text-[#EAF3EA]/70">
          <span class="h-1.5 w-1.5 rounded-full bg-[#92b95d]"></span>
          Acceso corporativo
        </div>

        <h1 class="mt-4 text-[22px] lg:text-[26px] font-semibold tracking-tight
                   text-[#123617] dark:text-[#92b95d]">
          Iniciar sesión
        </h1>

        <p class="mt-2 text-[13px] lg:text-[14px]
                  text-[#3b6a33]/80 dark:text-[#EAF3EA]/70">
          Escribe tu usuario y tu contraseña para continuar.
        </p>
      </div>

      @if ($errors->any())
        <div
          data-aos="fade-in"
          data-aos-duration="700"
          class="mb-6 rounded-xl border border-[#81a553]/40 dark:border-[#81a553]/35 p-4
                 bg-[#EAF6E7] dark:bg-[#0A2012]">
          <p class="mb-2 font-semibold text-[#123617] dark:text-[#92b95d]">Revisa lo siguiente:</p>
          <ul class="space-y-1 text-[#1e4e25]/80 dark:text-[#EAF3EA]/75">
            @foreach ($errors->all() as $error)
              <li>• {{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('login.post') }}" class="space-y-4" id="loginForm">
        @csrf

        <!-- Usuario + @ + dominio -->
        <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="220">
          <label class="block mb-1.5 text-[13px] font-semibold text-[#123617] dark:text-[#EAF3EA]">
            Usuario
          </label>

          <div class="group flex w-full overflow-hidden rounded-xl
                      border border-[#CFE6C9] dark:border-[#1B3B22]
                      bg-[#F6FBF4] dark:bg-[#07120A]
                      shadow-[0px_1px_0px_rgba(18,54,23,0.06)]
                      focus-within:ring-4 focus-within:ring-[#81a553]/20 dark:focus-within:ring-[#92b95d]/15
                      focus-within:border-[#81a553] dark:focus-within:border-[#92b95d]
                      transition-all">

            <input
              id="username"
              type="text"
              required
              autocomplete="username"
              autocapitalize="none"
              spellcheck="false"
              placeholder="Usuario"
              value="{{ old('email') ? preg_replace('/@.*/', '', old('email')) : '' }}"
              class="flex-1 px-4 py-2.5 outline-none bg-transparent
                     text-[#0f1a12] dark:text-[#EAF3EA]
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

          <input type="hidden" name="email" id="email" value="{{ old('email') }}">
        </div>

        <!-- Password -->
        <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
          <label class="block mb-1.5 text-[13px] font-semibold text-[#123617] dark:text-[#EAF3EA]">
            Contraseña
          </label>

          <div class="relative">
            <input
              id="password"
              name="password"
              type="password"
              required
              autocomplete="current-password"
              placeholder="••••••••"
              class="w-full rounded-xl px-4 py-2.5 pr-12
                     bg-[#F6FBF4] dark:bg-[#07120A]
                     border border-[#CFE6C9] dark:border-[#1B3B22]
                     text-[#0f1a12] dark:text-[#EAF3EA]
                     placeholder:text-[#3b6a33]/55 dark:placeholder:text-[#EAF3EA]/35
                     outline-none
                     focus:border-[#81a553] focus:ring-4 focus:ring-[#81a553]/20
                     dark:focus:border-[#92b95d] dark:focus:ring-[#92b95d]/15
                     shadow-[0px_1px_0px_rgba(18,54,23,0.06)]
                     transition-all"
            >

            <button
              type="button"
              onclick="togglePassword()"
              class="absolute inset-y-0 right-3 flex items-center
                     text-[#3b6a33]/70 dark:text-[#EAF3EA]/60
                     hover:text-[#1e4e25] dark:hover:text-[#92b95d]
                     transition"
              aria-label="Mostrar/ocultar contraseña"
            >
              <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5
                         c4.477 0 8.268 2.943 9.542 7
                         -1.274 4.057-5.065 7-9.542 7
                         -4.477 0-8.268-2.943-9.542-7z"/>
              </svg>

              <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                   viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13.875 18.825A10.05 10.05 0 0112 19
                         c-4.478 0-8.268-2.943-9.543-7
                         a9.956 9.956 0 012.223-3.592M6.223 6.223
                         A9.956 9.956 0 0112 5
                         c4.478 0 8.268 2.943 9.543 7
                         a9.97 9.97 0 01-4.132 5.411M15 12
                         a3 3 0 01-4.243 2.829M9.88 9.88
                         A3 3 0 0115 12M3 3l18 18"/>
              </svg>
            </button>
          </div>
        </div>

      <!-- remember + helper + recuperar -->
      <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="380"
          class="flex items-center justify-between">

        <label class="inline-flex items-center gap-3 select-none">
          <input
            type="checkbox"
            name="remember"
            class="h-4 w-4 rounded
                  border border-[#CFE6C9] dark:border-[#1B3B22]
                  accent-[#5e8d42]"
          >
          <span class="text-[13px] text-[#1e4e25]/75 dark:text-[#EAF3EA]/70">
            Recordarme
          </span>
        </label>

        <a href="{{ route('password.request') }}"
          class="text-[13px] font-medium
                  text-[#1e4e25] dark:text-[#92b95d]
                  hover:underline hover:text-[#123617]
                  dark:hover:text-[#81a553]
                  transition">
            ¿Olvidaste tu contraseña?
        </a>

      </div>

        <!-- CTA -->
        <button
          data-aos="fade-up" data-aos-duration="800" data-aos-delay="460"
          type="submit"
          class="inline-flex w-full items-center justify-center gap-2 px-5 py-3 rounded-xl
                 bg-[#1e4e25] hover:bg-[#123617]
                 text-white font-semibold
                 border border-[#123617]/60
                 shadow-[0px_14px_34px_-18px_rgba(18,54,23,0.95)]
                 dark:bg-[#92b95d] dark:hover:bg-[#81a553]
                 dark:text-[#07120A] dark:border-[#92b95d]
                 transition-all active:translate-y-[1px]"
        >
          Entrar
        </button>

        <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="520"
             class="pt-1 text-[12px] text-[#3b6a33]/70 dark:text-[#EAF3EA]/55">
          Tip: si pegas un correo completo, se tomará automáticamente el usuario.
        </div>
      </form>
    </div>

    <!-- Lado visual -->
    <div
      data-aos="fade-left"
      data-aos-duration="900"
      data-aos-delay="180"
      class="relative w-full lg:w-[460px] shrink-0 overflow-hidden
             flex items-center justify-center p-10 lg:p-12
             bg-[radial-gradient(1200px_420px_at_50%_15%,rgba(146,185,93,0.35),transparent_60%),linear-gradient(145deg,#07120A,#123617)]
             dark:bg-[radial-gradient(1200px_420px_at_50%_15%,rgba(146,185,93,0.25),transparent_60%),linear-gradient(145deg,#050D07,#0B1A10)]">

      <!-- textura -->
      <div class="absolute inset-0 opacity-30
                  bg-[radial-gradient(circle_at_20%_20%,rgba(146,185,93,0.55)_0,transparent_40%),radial-gradient(circle_at_80%_30%,rgba(129,165,83,0.45)_0,transparent_42%),radial-gradient(circle_at_50%_90%,rgba(94,141,66,0.45)_0,transparent_45%)]"></div>

      <!-- badge -->
      <div class="absolute top-6 right-6 rounded-full px-3 py-1 text-[12px]
                  border border-white/15 bg-white/10 text-white/80 backdrop-blur">
        La Herradura · Acceso
      </div>

      <div data-aos="zoom-in" data-aos-duration="900" data-aos-delay="320"
           class="relative w-full flex items-center justify-center">
        <img
          src="{{ asset('assets/img/logo_herradura.png') }}"
          alt="La Herradura"
          class="w-full max-w-[320px] lg:max-w-[390px] h-auto
                 drop-shadow-[0px_22px_55px_rgba(0,0,0,0.60)]"
          loading="eager"
          decoding="async"
        >
      </div>

      <div class="pointer-events-none absolute inset-0
                  shadow-[inset_0px_0px_0px_1px_rgba(146,185,93,0.22)]"></div>
    </div>
  </main>
</div>


  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const eyeOpen = document.getElementById('eyeOpen');
      const eyeClosed = document.getElementById('eyeClosed');

      if (!input || !eyeOpen || !eyeClosed) return;

      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      eyeOpen.classList.toggle('hidden', isHidden);
      eyeClosed.classList.toggle('hidden', !isHidden);
    }

    (function initCorporateEmail() {
      const usernameInput = document.getElementById('username');
      const emailHidden = document.getElementById('email');
      const form = document.getElementById('loginForm');
      const DOMAIN = '@dispensariolaherradura.cl';

      if (!usernameInput || !emailHidden) return;

      const normalize = () => {
        let v = (usernameInput.value || '').trim();

        // Si pegan un email, dejamos solo el user
        if (v.includes('@')) v = v.split('@')[0];

        // Normaliza
        v = v.toLowerCase().replace(/\s+/g, '');

        // Permite letras/números/punto/guion/underscore
        v = v.replace(/[^a-z0-9._-]/g, '');

        usernameInput.value = v;
        emailHidden.value = v ? (v + DOMAIN) : '';
      };

      // Bloquear ingreso de '@' siempre
      usernameInput.addEventListener('keydown', (e) => {
        if (e.key === '@') e.preventDefault();
      });

      usernameInput.addEventListener('input', normalize);
      usernameInput.addEventListener('blur', normalize);
      usernameInput.addEventListener('paste', () => setTimeout(normalize, 0));

      if (form) form.addEventListener('submit', normalize);

      // init
      normalize();
    })();
  </script>

  <!-- AOS Animations -->
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 900,
      easing: 'ease-out-cubic',
      once: true
    });
  </script>

</body>
</html>
