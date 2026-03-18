<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'La Herradura') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="min-h-screen font-sans bg-[#F6FBF4] dark:bg-[#07120A] text-[#0f1a12] dark:text-[#EAF3EA]">
@auth
@php
    $user = auth()->user();
    $isAdmin = $user?->hasRole('admin') ?? false;

    $isHome          = request()->routeIs('home');
    $isTiendaSection = request()->is('tienda') || request()->is('tienda/*');

    $isPedidosSection = $isAdmin
        ? (request()->is('admin/pedidos') || request()->is('admin/pedidos/*'))
        : (request()->is('pedidos') || request()->is('pedidos/*'));

    $isAdminUsers    = $isAdmin && request()->is('admin/usuarios*');
    $isAdminProducts = $isAdmin && request()->is('admin/productos*');
    $isAdminPedidos  = $isAdmin && request()->is('admin/pedidos*');
    $isAdminPuntos   = $isAdmin && request()->is('admin/puntos-encuentro*');
    $isAdminMensaje  = $isAdmin && request()->is('admin/mensaje');

    $isPedidosDashboard = $isAdmin && request()->routeIs('pedidos.dashboard');

    $pedidosHref = $isAdmin ? route('admin.pedidos.admin') : route('pedidos.index');

    $navBase = "w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all border";
    $navIdle = "bg-white/70 dark:bg-[#0B1A10]/55 border-[#DDEEDD] dark:border-[#16351F] text-[#123617] dark:text-[#EAF3EA] hover:bg-white dark:hover:bg-[#0D1E12]";
    $navOn   = "bg-[#1e4e25] text-white border-[#1e4e25] dark:bg-[#92b95d] dark:text-[#07120A] dark:border-[#92b95d]";

    $chipBase = "text-[10px] px-2 py-1 rounded-full border";

    $iconWrapBase = "inline-flex h-9 w-9 items-center justify-center rounded-xl border transition";
    $iconWrapIdle = "bg-[#F1F8EC] dark:bg-[#0D1E12] border-[#DDEEDD] dark:border-[#16351F] text-[#123617] dark:text-[#EAF3EA]";
    $iconWrapOn   = "bg-white/15 border-white/25 text-white dark:bg-black/10 dark:border-[#07120A]/25 dark:text-[#07120A]";

    $iconSvg = "w-4 h-4 opacity-90 text-current";
@endphp

<div class="min-h-screen flex">
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 hidden z-[9998] lg:hidden"></div>

    <aside id="sidebar"
           class="fixed lg:sticky top-0 left-0 h-screen w-[280px] shrink-0 z-[9999]
                  -translate-x-full lg:translate-x-0 transition-transform duration-200
                  bg-white/85 dark:bg-[#0B1A10]/75 backdrop-blur
                  border-r border-[#DDEEDD] dark:border-[#16351F]
                  flex flex-col">

        <div class="px-5 py-5 border-b border-[#DDEEDD] dark:border-[#16351F]">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <img
                    src="{{ asset('assets/img/logo_herradura.png') }}"
                    alt="La Herradura"
                    class="h-14 w-auto object-contain select-none drop-shadow-[0px_18px_35px_rgba(0,0,0,0.18)]"
                    loading="eager"
                    decoding="async"
                />
            </a>
            <div class="mt-3 text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Sesión: <span class="font-semibold">{{ $user->name ?? 'Usuario' }}</span>
                <span class="mx-1">·</span>
                <span class="font-semibold">{{ $isAdmin ? 'Admin' : 'Usuario' }}</span>
            </div>
        </div>

        <nav class="px-4 py-4 space-y-2 flex-1 overflow-y-auto min-h-0">

            <a href="{{ route('home') }}"
               class="{{ $navBase }} {{ $isHome ? $navOn : $navIdle }}">
                <span class="{{ $iconWrapBase }} {{ $isHome ? $iconWrapOn : $iconWrapIdle }}">
                    <svg class="{{ $iconSvg }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-10.5z"/>
                    </svg>
                </span>
                <span class="flex-1">Home</span>
                @if($isHome)
                    <span class="{{ $chipBase }} text-white/90 border-white/25 dark:text-[#07120A] dark:border-[#07120A]/25">Actual</span>
                @endif
            </a>

            <a href="{{ route('tienda.home') }}"
               class="{{ $navBase }} {{ $isTiendaSection ? $navOn : $navIdle }}">
                <span class="{{ $iconWrapBase }} {{ $isTiendaSection ? $iconWrapOn : $iconWrapIdle }}">
                    <svg class="{{ $iconSvg }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l2-5h14l2 5M5 9v11h14V9M9 20v-7h6v7"/>
                    </svg>
                </span>
                <span class="flex-1">Tienda</span>
                @if($isTiendaSection)
                    <span class="{{ $chipBase }} text-white/90 border-white/25 dark:text-[#07120A] dark:border-[#07120A]/25">Actual</span>
                @endif
            </a>

            <a href="{{ $pedidosHref }}"
               class="{{ $navBase }} {{ $isPedidosSection ? $navOn : $navIdle }}">
                <span class="{{ $iconWrapBase }} {{ $isPedidosSection ? $iconWrapOn : $iconWrapIdle }}">
                    <svg class="{{ $iconSvg }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5h6M9 3h6a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                    </svg>
                </span>
                <span class="flex-1">Pedidos</span>
                @if($isPedidosSection)
                    <span class="{{ $chipBase }} text-white/90 border-white/25 dark:text-[#07120A] dark:border-[#07120A]/25">Actual</span>
                @endif
            </a>

            @if($isAdmin)
                <a href="{{ route('pedidos.dashboard') }}"
                   class="{{ $navBase }} {{ $isPedidosDashboard ? $navOn : $navIdle }}">
                    <span class="{{ $iconWrapBase }} {{ $isPedidosDashboard ? $iconWrapOn : $iconWrapIdle }}">
                        <svg class="{{ $iconSvg }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 15v3"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11v7"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 7v11"/>
                        </svg>
                    </span>
                    <span class="flex-1">Dashboard</span>
                    @if($isPedidosDashboard)
                        <span class="{{ $chipBase }} text-white/90 border-white/25 dark:text-[#07120A] dark:border-[#07120A]/25">Actual</span>
                    @endif
                </a>

                <div class="pt-3">
                    <div class="px-2 pb-2 text-xs font-semibold uppercase tracking-wider text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                        Admin
                    </div>

                    <div class="space-y-2">

                        <a href="{{ route('admin.usuarios') }}"
                           class="{{ $navBase }} {{ $isAdminUsers ? $navOn : $navIdle }}">
                            <span class="{{ $iconWrapBase }} {{ $isAdminUsers ? $iconWrapOn : $iconWrapIdle }}">
                                <svg class="{{ $iconSvg }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </span>
                            <span class="flex-1">Usuarios</span>
                            @if($isAdminUsers)
                                <span class="{{ $chipBase }} text-white/90 border-white/25 dark:text-[#07120A] dark:border-[#07120A]/25">Actual</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.productos') }}"
                           class="{{ $navBase }} {{ $isAdminProducts ? $navOn : $navIdle }}">
                            <span class="{{ $iconWrapBase }} {{ $isAdminProducts ? $iconWrapOn : $iconWrapIdle }}">
                                <svg class="{{ $iconSvg }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.29 7.3 12 12l8.71-4.7"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 22V12"/>
                                </svg>
                            </span>
                            <span class="flex-1">Productos</span>
                            @if($isAdminProducts)
                                <span class="{{ $chipBase }} text-white/90 border-white/25 dark:text-[#07120A] dark:border-[#07120A]/25">Actual</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.puntos.index') }}"
                           class="{{ $navBase }} {{ $isAdminPuntos ? $navOn : $navIdle }}">
                            <span class="{{ $iconWrapBase }} {{ $isAdminPuntos ? $iconWrapOn : $iconWrapIdle }}">
                                <svg class="{{ $iconSvg }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-4.35 7-10a7 7 0 0 0-14 0c0 5.65 7 10 7 10z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 11a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                                </svg>
                            </span>
                            <span class="flex-1">Puntos de encuentro</span>
                            @if($isAdminPuntos)
                                <span class="{{ $chipBase }} text-white/90 border-white/25 dark:text-[#07120A] dark:border-[#07120A]/25">Actual</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.pedidos.admin') }}"
                           class="{{ $navBase }} {{ $isAdminPedidos ? $navOn : $navIdle }}">
                            <span class="{{ $iconWrapBase }} {{ $isAdminPedidos ? $iconWrapOn : $iconWrapIdle }}">
                                <svg class="{{ $iconSvg }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>
                                </svg>
                            </span>
                            <span class="flex-1">Pedidos</span>
                            @if($isAdminPedidos)
                                <span class="{{ $chipBase }} text-white/90 border-white/25 dark:text-[#07120A] dark:border-[#07120A]/25">Actual</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.mensaje') }}"
                           class="{{ $navBase }} {{ $isAdminMensaje ? $navOn : $navIdle }}">
                            <span class="{{ $iconWrapBase }} {{ $isAdminMensaje ? $iconWrapOn : $iconWrapIdle }}">
                                <svg class="{{ $iconSvg }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v12H5.5L4 17.5V4z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h8"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h6"/>
                                </svg>
                            </span>
                            <span class="flex-1">Mensaje bienvenida</span>
                            @if($isAdminMensaje)
                                <span class="{{ $chipBase }} text-white/90 border-white/25 dark:text-[#07120A] dark:border-[#07120A]/25">Actual</span>
                            @endif
                        </a>

                    </div>
                </div>
            @endif
        </nav>
    </aside>

    <div class="flex-1 min-w-0">
        <header class="sticky top-0 z-[9990]
                       bg-white/80 dark:bg-[#0B1A10]/70 backdrop-blur
                       border-b border-[#DDEEDD] dark:border-[#16351F]">
            <div class="px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" id="sidebarToggle"
                            class="lg:hidden inline-flex items-center justify-center
                                   h-10 w-10 rounded-xl shrink-0
                                   bg-white/70 dark:bg-[#0B1A10]/55
                                   border border-[#DDEEDD] dark:border-[#16351F]
                                   hover:bg-white dark:hover:bg-[#0D1E12]
                                   transition">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="leading-tight min-w-0">
                        <div class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">Panel</div>
                        <div class="text-base font-semibold text-[#123617] dark:text-[#92b95d] truncate">
                            {{ $isAdmin ? 'Administrador' : 'Usuario' }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 whitespace-nowrap">
                    <div class="text-xs sm:text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                        {{ now()->format('d/m/Y H:i') }}
                    </div>

                    @if($isAdmin)
                        <a href="{{ route('admin.mensaje') }}"
                           class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold
                                  bg-white/70 dark:bg-[#0B1A10]/55
                                  border border-[#DDEEDD] dark:border-[#16351F]
                                  text-[#123617] dark:text-[#EAF3EA]
                                  hover:bg-white dark:hover:bg-[#0D1E12]
                                  transition">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v12H5.5L4 17.5V4z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 8h8"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h6"/>
                            </svg>
                            <span class="hidden sm:inline">Mensaje</span>
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-xs sm:text-sm font-semibold
                                       bg-white/70 dark:bg-[#0B1A10]/55
                                       border border-[#DDEEDD] dark:border-[#16351F]
                                       text-[#123617] dark:text-[#EAF3EA]
                                       hover:bg-white dark:hover:bg-[#0D1E12]
                                       transition">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3"/>
                            </svg>
                            <span class="hidden sm:inline">Cerrar sesión</span>
                            <span class="sm:hidden">Salir</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="px-4 sm:px-6 py-8">
            @yield('content')
        </main>
    </div>
</div>

<script>
(() => {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const toggle  = document.getElementById('sidebarToggle');

  if (!sidebar || !overlay || !toggle) return;

  const open = () => {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
    document.documentElement.classList.add('overflow-hidden');
  };

  const close = () => {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
    document.documentElement.classList.remove('overflow-hidden');
  };

  toggle.addEventListener('click', () => {
    const isClosed = sidebar.classList.contains('-translate-x-full');
    isClosed ? open() : close();
  });

  overlay.addEventListener('click', close);

  sidebar.querySelectorAll('a').forEach(a => {
    a.addEventListener('click', () => {
      if (window.innerWidth < 1024) close();
    }, { passive: true });
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024) {
      overlay.classList.add('hidden');
      sidebar.classList.remove('-translate-x-full');
      document.documentElement.classList.remove('overflow-hidden');
    } else {
      close();
    }
  }, { passive: true });
})();
</script>

@else
    <main class="max-w-5xl mx-auto px-6 py-10">
        @yield('content')
    </main>
@endauth
</body>
</html>