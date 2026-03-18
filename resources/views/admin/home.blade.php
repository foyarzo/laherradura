@extends('layouts.app')

@section('content')
@php
  $authUser = auth()->user();
  $superAdminEmail = 'matias-oyarzo@hotmail.com';
@endphp

<div class="min-h-[calc(100vh-100px)] px-4 sm:px-6 lg:px-8 py-8 sm:py-10 bg-[#F6FBF4] dark:bg-[#07120A]">
  <div class="max-w-6xl mx-auto space-y-5 sm:space-y-6">

    {{-- HERO / BIENVENIDA --}}
    <section class="relative overflow-hidden rounded-3xl border border-[#DDEEDD] dark:border-[#16351F]
                    bg-white/90 dark:bg-[#0B1A10]/80 backdrop-blur
                    shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_22px_65px_-28px_rgba(18,54,23,0.38)]">

      <div class="pointer-events-none absolute inset-0">
        <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full blur-3xl opacity-30
                    bg-[radial-gradient(circle_at_30%_30%,rgba(146,185,93,0.55),transparent_60%)]"></div>
        <div class="absolute -bottom-28 -left-28 h-80 w-80 rounded-full blur-3xl opacity-25
                    bg-[radial-gradient(circle_at_30%_30%,rgba(18,54,23,0.35),transparent_60%)]"></div>
      </div>

      <div class="relative p-5 sm:p-8 lg:p-10">
        <div class="flex flex-col lg:flex-row lg:items-center gap-6 lg:gap-8">

          {{-- izquierda: logo + textos --}}
          <div class="flex flex-col sm:flex-row sm:items-center gap-4 min-w-0">
            <div class="shrink-0 rounded-3xl p-3 sm:p-4
                        bg-[#F1F8EC] dark:bg-[#07120A]/60
                        border border-[#DDEEDD] dark:border-[#16351F]
                        shadow-[0px_12px_40px_-28px_rgba(18,54,23,0.55)]">
              <img
                src="{{ asset('assets/img/logo_herradura.png') }}"
                alt="Dispensario La Herradura"
                class="h-14 sm:h-16 md:h-20 w-auto object-contain select-none"
                loading="eager"
                decoding="async"
              />
            </div>

            <div class="min-w-0">
              <div class="text-xs font-semibold tracking-wide uppercase text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Panel de administración
              </div>
              <h1 class="mt-1 text-xl sm:text-2xl md:text-3xl lg:text-4xl font-extrabold leading-tight
                         text-[#123617] dark:text-[#92b95d] break-words">
                Bienvenido, {{ $authUser->name }}
              </h1>
              <p class="mt-2 text-sm sm:text-base text-[#3b6a33]/80 dark:text-[#EAF3EA]/70 max-w-2xl">
                Mantengamos comunicación clara y coordinación efectiva para asegurar el correcto funcionamiento del sistema.
              </p>
            </div>
          </div>

          {{-- derecha: acciones (en móvil 2 columnas, sin desbordes) --}}
          <div class="lg:ml-auto w-full lg:w-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:flex gap-3 lg:items-center">
              <button type="button"
                      data-open-superadmin-modal
                      class="inline-flex w-full items-center justify-center gap-2 rounded-2xl px-4 sm:px-5 py-3 text-sm font-extrabold
                             bg-[#1e4e25] text-white hover:bg-[#123617]
                             dark:bg-[#92b95d] dark:hover:bg-[#81a553] dark:text-[#07120A]
                             shadow-[0px_12px_35px_-22px_rgba(18,54,23,0.7)]
                             focus:outline-none focus:ring-2 focus:ring-[#92b95d]/45">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8z"/>
                </svg>
                <span class="whitespace-nowrap">Contactar Super-Admin</span>
              </button>

              <a href="#users"
                 class="inline-flex w-full items-center justify-center gap-2 rounded-2xl px-4 sm:px-5 py-3 text-sm font-bold
                        bg-white/80 dark:bg-[#07120A]/55
                        border border-[#DDEEDD] dark:border-[#16351F]
                        text-[#123617] dark:text-[#EAF3EA]
                        hover:bg-white dark:hover:bg-[#0D1E12]
                        transition
                        focus:outline-none focus:ring-2 focus:ring-[#92b95d]/35">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="whitespace-nowrap">Ver usuarios</span>
              </a>
            </div>
          </div>

        </div>
      </div>
    </section>

    {{-- TOOLBAR --}}
    <section class="rounded-3xl p-4 sm:p-6
                    bg-white/90 dark:bg-[#0B1A10]/80 backdrop-blur
                    border border-[#DDEEDD] dark:border-[#16351F]
                    shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03)]">

      <div class="flex flex-col gap-4">

        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
          <div class="flex-1 min-w-0">
            <label for="userSearch" class="block text-xs font-semibold text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mb-2">
              Buscar en historial de ingreso
            </label>

            <div class="relative">
              <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[#3b6a33]/55 dark:text-[#EAF3EA]/45">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                  <circle cx="11" cy="11" r="7"/>
                </svg>
              </span>

              {{-- ✅ input con botón "Limpiar" responsivo: en móvil baja debajo --}}
              <input id="userSearch" type="text"
                     placeholder='Ej: "admin", "gmail", "12-02-2026", "190.22", "operador"...'
                     class="w-full rounded-2xl pl-12 pr-4 sm:pr-28 py-3 text-sm
                            border border-[#DDEEDD] dark:border-[#16351F]
                            bg-white/90 dark:bg-[#07120A]/70
                            text-[#123617] dark:text-[#EAF3EA]
                            shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03)]
                            focus:outline-none focus:ring-2 focus:ring-[#92b95d]/40">

              <button id="clearUserSearch" type="button"
                      class="mt-2 sm:mt-0 sm:absolute sm:right-2 sm:top-1/2 sm:-translate-y-1/2
                             inline-flex w-full sm:w-auto items-center justify-center gap-2
                             rounded-xl px-3 py-2 text-xs font-bold
                             border border-[#DDEEDD] dark:border-[#16351F]
                             bg-white/70 dark:bg-[#07120A]/50
                             text-[#123617] dark:text-[#EAF3EA]
                             hover:bg-white dark:hover:bg-[#0D1E12]
                             transition">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar
              </button>
            </div>

            <div id="searchMeta" class="mt-3 text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60"></div>
          </div>

          {{-- ✅ chips: scroll horizontal en móvil (sin romper) --}}
          <div class="-mx-4 sm:mx-0">
            <div class="px-4 sm:px-0 flex gap-2 items-center overflow-x-auto whitespace-nowrap
                        [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
              <button type="button" data-chip="admin"
                      class="search-chip inline-flex items-center gap-2 rounded-full px-3 py-2 text-xs font-bold
                             border border-[#DDEEDD] dark:border-[#16351F]
                             bg-white/70 dark:bg-[#07120A]/50
                             text-[#123617] dark:text-[#EAF3EA]
                             hover:bg-white dark:hover:bg-[#0D1E12] transition">
                <span class="h-2.5 w-2.5 rounded-full bg-[#1e4e25] dark:bg-[#92b95d]"></span>
                Admin
              </button>

              <button type="button" data-chip="usuario"
                      class="search-chip inline-flex items-center gap-2 rounded-full px-3 py-2 text-xs font-bold
                             border border-[#DDEEDD] dark:border-[#16351F]
                             bg-white/70 dark:bg-[#07120A]/50
                             text-[#123617] dark:text-[#EAF3EA]
                             hover:bg-white dark:hover:bg-[#0D1E12] transition">
                <span class="h-2.5 w-2.5 rounded-full bg-[#81a553]"></span>
                Usuario
              </button>

              <button type="button" data-chip="ip"
                      class="search-chip inline-flex items-center gap-2 rounded-full px-3 py-2 text-xs font-bold
                             border border-[#DDEEDD] dark:border-[#16351F]
                             bg-white/70 dark:bg-[#07120A]/50
                             text-[#123617] dark:text-[#EAF3EA]
                             hover:bg-white dark:hover:bg-[#0D1E12] transition">
                <span class="h-2.5 w-2.5 rounded-full bg-[#3b6a33]"></span>
                IP
              </button>

              <div class="inline-flex items-center gap-2 rounded-full px-3 py-2 text-xs font-bold
                          bg-[#F1F8EC] dark:bg-[#07120A]/60
                          border border-[#DDEEDD] dark:border-[#16351F]
                          text-[#123617] dark:text-[#EAF3EA]">
                <svg class="h-4 w-4 opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7 14l3-3 4 4 6-6"/>
                </svg>
                <span id="countVisible">—</span>
                <span class="opacity-70">visibles</span>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl p-4
                    bg-[#F6FBF4] dark:bg-[#07120A]/55
                    border border-[#DDEEDD] dark:border-[#16351F]">
          <div class="text-xs text-[#3b6a33]/75 dark:text-[#EAF3EA]/60">
            Tip: presiona <span class="font-bold">Esc</span> para limpiar. Puedes buscar por nombre, email, rol, IP o fecha/hora.
          </div>
        </div>

      </div>
    </section>

    {{-- LISTA USUARIOS --}}
    <section id="users"
             class="rounded-3xl p-5 sm:p-7
                    bg-white/90 dark:bg-[#0B1A10]/80 backdrop-blur
                    border border-[#DDEEDD] dark:border-[#16351F]
                    shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_20px_60px_-28px_rgba(18,54,23,0.42)]">

      <header class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 sm:gap-4">
        <div>
          <h2 class="text-lg sm:text-xl font-extrabold text-[#123617] dark:text-[#92b95d]">
            Historial de ingreso de usuarios
          </h2>
          <p class="mt-1 text-sm text-[#3b6a33]/75 dark:text-[#EAF3EA]/60">
            Revisa último ingreso, roles e IP del sistema.
          </p>
        </div>

        <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
          <span class="font-bold">Orden:</span> por tu paginación actual
        </div>
      </header>

      <div id="usersList" class="mt-5 sm:mt-6 grid grid-cols-1 gap-3">
        @forelse($users as $u)
          @php
            $lastLogin = $u->last_login_at?->timezone(config('app.timezone'));
            $rolesText = $u->roles?->pluck('name')->implode(' ') ?? '';
            $lastText = $lastLogin ? $lastLogin->format('d-m-Y H:i') : '';
            $ipText = $u->last_login_ip ?? '';

            $blob = mb_strtolower(trim(
              $u->name . ' ' .
              $u->email . ' ' .
              $rolesText . ' ' .
              $lastText . ' ' .
              $ipText
            ));

            $createdText = $u->created_at?->format('d-m-Y') ?? '—';
          @endphp

          <article class="user-card group rounded-2xl p-4 sm:p-5 border border-[#DDEEDD] dark:border-[#16351F]
                          bg-white/70 dark:bg-[#0D1E12]/35
                          hover:bg-white/90 dark:hover:bg-[#0D1E12]/55
                          transition
                          focus-within:ring-2 focus-within:ring-[#92b95d]/35"
                   data-search="{{ $blob }}">

            {{-- ✅ en móvil se apila; en sm+ vuelve a 2 columnas --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
              <div class="min-w-0">

                <div class="flex items-center gap-3">
                  <div class="inline-flex h-10 w-10 items-center justify-center rounded-2xl
                              bg-[#F1F8EC] dark:bg-[#07120A]/55
                              border border-[#DDEEDD] dark:border-[#16351F]">
                    <svg class="h-5 w-5 text-[#123617] dark:text-[#EAF3EA] opacity-85" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
                    </svg>
                  </div>

                  <div class="min-w-0">
                    <p class="text-sm sm:text-base font-extrabold text-[#123617] dark:text-[#EAF3EA] truncate">
                      {{ $u->name }}
                    </p>
                    <p class="text-xs text-[#3b6a33]/75 dark:text-[#EAF3EA]/60 truncate">
                      {{ $u->email }}
                    </p>
                  </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                  @foreach($u->roles as $role)
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border
                                 bg-[#EAF6E7] dark:bg-[#0A2012]
                                 border-[#81a553]/35
                                 text-[#123617] dark:text-[#EAF3EA]">
                      {{ $role->name }}
                    </span>
                  @endforeach
                  @if($u->roles->isEmpty())
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border
                                 bg-white/70 dark:bg-[#07120A]/40
                                 border-[#DDEEDD] dark:border-[#16351F]
                                 text-[#123617] dark:text-[#EAF3EA]">
                      sin rol
                    </span>
                  @endif
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                  <div class="rounded-2xl p-3 border border-[#DDEEDD] dark:border-[#16351F]
                              bg-[#F6FBF4] dark:bg-[#07120A]/55">
                    <div class="text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Último ingreso</div>
                    <div class="mt-1 font-extrabold text-[#123617] dark:text-[#EAF3EA]">
                      {{ $lastLogin ? $lastLogin->format('d-m-Y H:i') : '—' }}
                    </div>
                  </div>

                  <div class="rounded-2xl p-3 border border-[#DDEEDD] dark:border-[#16351F]
                              bg-[#F6FBF4] dark:bg-[#07120A]/55">
                    <div class="text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">IP</div>
                    <div class="mt-1 font-extrabold text-[#123617] dark:text-[#EAF3EA] truncate">
                      {{ $u->last_login_ip ?? '—' }}
                    </div>
                  </div>

                  <div class="rounded-2xl p-3 border border-[#DDEEDD] dark:border-[#16351F]
                              bg-[#F6FBF4] dark:bg-[#07120A]/55">
                    <div class="text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Creado</div>
                    <div class="mt-1 font-extrabold text-[#123617] dark:text-[#EAF3EA]">
                      {{ $createdText }}
                    </div>
                  </div>
                </div>

              </div>

              {{-- ✅ acciones: visibles en móvil (sin depender de hover) --}}
              <div class="flex flex-row sm:flex-col gap-2 sm:items-end shrink-0">
                <button type="button"
                        class="copy-email inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold
                               bg-white/80 dark:bg-[#07120A]/55
                               border border-[#DDEEDD] dark:border-[#16351F]
                               text-[#123617] dark:text-[#EAF3EA]
                               hover:bg-white dark:hover:bg-[#0D1E12]
                               transition sm:opacity-0 sm:group-hover:opacity-100 sm:focus:opacity-100"
                        data-copy="{{ $u->email }}"
                        aria-label="Copiar email">
                  <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2"/>
                    <rect x="8" y="8" width="14" height="14" rx="2"/>
                  </svg>
                  Copiar email
                </button>

                <a href="mailto:{{ $u->email }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-2 text-xs font-extrabold
                          bg-[#1e4e25] text-white hover:bg-[#123617]
                          dark:bg-[#92b95d] dark:hover:bg-[#81a553] dark:text-[#07120A]
                          transition sm:opacity-0 sm:group-hover:opacity-100 sm:focus:opacity-100"
                   aria-label="Enviar correo">
                  <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="m22 6-10 7L2 6"/>
                  </svg>
                  Mail
                </a>
              </div>
            </div>
          </article>
        @empty
          <p class="text-sm text-[#3b6a33]/75 dark:text-[#EAF3EA]/60">
            No hay usuarios registrados.
          </p>
        @endforelse
      </div>

      <div id="noResults"
           class="hidden mt-6 rounded-2xl p-6 text-center text-sm
                  text-[#3b6a33]/75 dark:text-[#EAF3EA]/60
                  border border-[#DDEEDD] dark:border-[#16351F]
                  bg-white/70 dark:bg-[#0D1E12]/35">
        No hay usuarios que coincidan con la búsqueda.
      </div>

      @if(method_exists($users, 'links'))
        <div class="mt-6">
          {{ $users->links() }}
        </div>
      @endif

    </section>

  </div>
</div>

{{-- MODAL: SUPER-ADMIN --}}
<div id="superAdminModal" class="fixed inset-0 z-[80] hidden" aria-hidden="true">
  <div data-close-superadmin-modal class="absolute inset-0 bg-black/45 backdrop-blur-sm"></div>

  <div class="relative min-h-screen flex items-center justify-center p-4">
    <div role="dialog" aria-modal="true" aria-labelledby="superAdminTitle"
         class="w-full max-w-xl rounded-3xl overflow-hidden
                bg-white dark:bg-[#0B1A10]
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_30px_90px_-35px_rgba(0,0,0,0.65)]">

      <div class="px-5 sm:px-6 py-4 border-b border-[#DDEEDD] dark:border-[#16351F]
                  bg-[#F6FBF4] dark:bg-[#07120A]/55">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <h3 id="superAdminTitle" class="text-sm font-extrabold tracking-wide text-[#123617] dark:text-[#EAF3EA]">
              Contactar Super-Admin
            </h3>
            <p class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mt-1">
              Incidencias técnicas o solicitudes de ajuste del sistema.
            </p>
          </div>

          <button type="button"
                  data-close-superadmin-modal
                  class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-xs font-bold shrink-0
                         border border-[#DDEEDD] dark:border-[#16351F]
                         text-[#123617] dark:text-[#EAF3EA] hover:bg-white/60 dark:hover:bg-[#0D1E12]/50
                         focus:outline-none focus:ring-2 focus:ring-[#92b95d]/35">
            Cerrar
          </button>
        </div>
      </div>

      <div class="p-5 sm:p-6 space-y-5">

        <div class="rounded-3xl p-4
                    bg-[#F6FBF4] dark:bg-[#07120A]/60
                    border border-[#DDEEDD] dark:border-[#16351F]">
          <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Correo de contacto</div>

          <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex-1 text-sm font-extrabold text-[#123617] dark:text-[#EAF3EA] break-all" id="superAdminEmail">
              {{ $superAdminEmail }}
            </div>

            <div class="grid grid-cols-1 xs:grid-cols-2 sm:flex gap-2">
              <button type="button" id="copySuperAdminEmail"
                      class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-xs font-bold
                             bg-[#1e4e25] text-white hover:bg-[#123617]
                             dark:bg-[#92b95d] dark:hover:bg-[#81a553] dark:text-[#07120A]
                             focus:outline-none focus:ring-2 focus:ring-[#92b95d]/35">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2"/>
                  <rect x="8" y="8" width="14" height="14" rx="2"/>
                </svg>
                Copiar
              </button>

              <a href="mailto:{{ $superAdminEmail }}"
                 class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 text-xs font-bold
                        border border-[#DDEEDD] dark:border-[#16351F]
                        text-[#123617] dark:text-[#EAF3EA] hover:bg-white/60 dark:hover:bg-[#0D1E12]/50
                        focus:outline-none focus:ring-2 focus:ring-[#92b95d]/35">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="m22 6-10 7L2 6"/>
                </svg>
                Abrir correo
              </a>
            </div>
          </div>

          <div id="copyToast" class="hidden mt-3 text-xs font-semibold text-[#123617] dark:text-[#EAF3EA]">
            ✅ Copiado al portapapeles
          </div>
        </div>

        <div class="space-y-3">
          <h4 class="text-sm font-extrabold text-[#123617] dark:text-[#92b95d]">
            Requisitos para reportar un problema
          </h4>

          <div class="rounded-3xl p-4 border border-[#DDEEDD] dark:border-[#16351F]
                      bg-white/70 dark:bg-[#0D1E12]/35 space-y-2">
            <div class="text-sm text-[#3b6a33]/85 dark:text-[#EAF3EA]/75">
              Adjunta evidencia clara y describe el caso con detalle:
            </div>

            <ul class="text-xs text-[#3b6a33]/75 dark:text-[#EAF3EA]/65 space-y-1 list-disc pl-5">
              <li>Capturas de pantalla o imágenes del error.</li>
              <li>Qué estabas haciendo (paso a paso) antes de que ocurriera.</li>
              <li>Qué esperabas que pasara vs. qué pasó realmente.</li>
              <li>Fecha/hora aproximada y usuario afectado (si aplica).</li>
            </ul>
          </div>
        </div>

      </div>

      <div class="px-5 sm:px-6 py-4 border-t border-[#DDEEDD] dark:border-[#16351F]
                  bg-[#F6FBF4] dark:bg-[#07120A]/55 flex items-center justify-end">
        <button type="button"
                data-close-superadmin-modal
                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs font-bold
                       bg-[#1e4e25] text-white hover:bg-[#123617]
                       dark:bg-[#92b95d] dark:hover:bg-[#81a553] dark:text-[#07120A]
                       focus:outline-none focus:ring-2 focus:ring-[#92b95d]/35">
          Entendido
        </button>
      </div>

    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // ---------- helpers ----------
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  // ---------- buscador usuarios (con contador + chips) ----------
  const input = $('#userSearch');
  const clearBtn = $('#clearUserSearch');
  const meta = $('#searchMeta');
  const noResults = $('#noResults');
  const cards = $$('.user-card');
  const countVisible = $('#countVisible');

  const updateCounts = (visible, total) => {
    if (countVisible) countVisible.textContent = `${visible}/${total}`;
  };

  const apply = () => {
    const q = (input?.value || '').trim().toLowerCase();
    let visible = 0;

    cards.forEach(card => {
      const blob = (card.dataset.search || '');
      const show = !q ? true : blob.includes(q);
      card.classList.toggle('hidden', !show);
      if (show) visible++;
    });

    noResults?.classList.toggle('hidden', visible !== 0);
    updateCounts(visible, cards.length);

    if (meta) {
      if (!q) {
        meta.textContent = `Tip: busca por nombre, email, rol, IP o fecha/hora (ej: "12-02-2026").`;
      } else {
        meta.textContent = `Búsqueda: "${q}" · Resultados: ${visible}`;
      }
    }
  };

  input?.addEventListener('input', apply);
  input?.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      input.value = '';
      apply();
      input.blur();
    }
  });

  clearBtn?.addEventListener('click', () => {
    if (input) input.value = '';
    apply();
    input?.focus();
  });

  // chips
  $$('.search-chip').forEach(btn => {
    btn.addEventListener('click', () => {
      const term = (btn.getAttribute('data-chip') || '').trim();
      if (!term || !input) return;
      const current = (input.value || '').trim();
      input.value = current ? `${current} ${term}` : term;
      input.focus();
      apply();
    });
  });

  apply();

  // ---------- modal super-admin (focus initial + cerrar consistente) ----------
  const modal = $('#superAdminModal');
  const openBtns = $$('[data-open-superadmin-modal]');
  const closeBtns = modal ? $$('[data-close-superadmin-modal]', modal) : [];

  const firstFocusable = () => {
    if (!modal) return null;
    return modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
  };

  const openModal = () => {
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
    setTimeout(() => firstFocusable()?.focus(), 0);
  };

  const closeModal = () => {
    if (!modal) return;
    modal.classList.add('hidden');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
  };

  openBtns.forEach(btn => btn.addEventListener('click', openModal));
  closeBtns.forEach(btn => btn.addEventListener('click', closeModal));

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
  });

  // copiar correo (superadmin)
  const copyBtn = $('#copySuperAdminEmail');
  const emailEl = $('#superAdminEmail');
  const toast = $('#copyToast');

  const showToast = () => {
    if (!toast) return;
    toast.classList.remove('hidden');
    window.clearTimeout(showToast._t);
    showToast._t = window.setTimeout(() => toast.classList.add('hidden'), 1400);
  };

  copyBtn?.addEventListener('click', async () => {
    const email = (emailEl?.textContent || '').trim();
    if (!email) return;

    try {
      await navigator.clipboard.writeText(email);
      showToast();
    } catch (e) {
      const ta = document.createElement('textarea');
      ta.value = email;
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
      showToast();
    }
  });

  // copiar email usuario (acciones rápidas)
  $$('.copy-email').forEach(btn => {
    btn.addEventListener('click', async () => {
      const email = (btn.getAttribute('data-copy') || '').trim();
      if (!email) return;

      try {
        await navigator.clipboard.writeText(email);
        // microfeedback inline
        const prev = btn.innerHTML;
        btn.innerHTML = `<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M20 6 9 17l-5-5"/>
                        </svg> Copiado`;
        btn.disabled = true;
        setTimeout(() => { btn.innerHTML = prev; btn.disabled = false; }, 900);
      } catch (e) {
        // fallback simple
        const ta = document.createElement('textarea');
        ta.value = email;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
      }
    });
  });
});
</script>
@endsection