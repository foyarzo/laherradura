@extends('layouts.app')

@section('content')
@php
  $user = auth()->user();
  $misAdvertencias = collect($advertencias ?? [])->values();
@endphp

<div class="min-h-[calc(100vh-100px)] flex items-start sm:items-center justify-center
            px-3 sm:px-4 py-6 sm:py-10
            bg-[#F6FBF4] dark:bg-[#07120A]">

  <div class="w-full max-w-2xl space-y-4 sm:space-y-6">

    {{-- Bienvenida --}}
    <div class="rounded-2xl p-5 sm:p-8 md:p-12
                bg-white/90 dark:bg-[#0B1A10]/80 backdrop-blur
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.45)]">

      <div class="flex flex-col items-center text-center gap-4 sm:gap-6">

        <img
          src="{{ asset('assets/img/logo_herradura.png') }}"
          alt="Dispensario La Herradura"
          class="h-20 min-[420px]:h-24 sm:h-32 md:h-40 lg:h-52 w-auto object-contain select-none
                 drop-shadow-[0px_18px_45px_rgba(0,0,0,0.35)]"
          loading="eager"
          decoding="async"
        >

        <div class="space-y-3">
          <h1 class="text-2xl min-[420px]:text-3xl sm:text-4xl md:text-5xl font-extrabold
                     text-[#123617] dark:text-[#92b95d] leading-tight">
            Bienvenido, {{ $user->name }}
          </h1>

          {{-- ✅ Mensaje editable (viene desde BD, pasado por controller como $mensajeBienvenida) --}}
          <p class="text-sm sm:text-base text-[#3b6a33]/80 dark:text-[#EAF3EA]/70 max-w-xl mx-auto whitespace-pre-line">
            {{ $mensajeBienvenida ?? '' }}
          </p>

          <p class="text-xs sm:text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 max-w-xl mx-auto">
            Si necesitas apoyo o tienes dudas, escribe por correo a alguno de los administradores que aparecen abajo.
          </p>

          {{-- Correos de administradores --}}
          @if(isset($admins) && $admins->count())
          <div class="mt-4">
            <p class="text-xs sm:text-sm font-semibold text-[#123617] dark:text-[#92b95d] mb-2">
              Correos de administración
            </p>

            <div class="flex flex-wrap justify-center gap-2">
              @foreach($admins as $admin)
                <a href="mailto:{{ $admin->email }}"
                   class="px-3 py-1 text-xs sm:text-sm rounded-full
                          bg-[#EAF6E7] dark:bg-[#0A2012]
                          border border-[#81a553]/40
                          text-[#123617] dark:text-[#92b95d]
                          hover:bg-[#dff0da] dark:hover:bg-[#123617]
                          transition">
                  {{ $admin->email }}
                </a>
              @endforeach
            </div>
          </div>
          @endif

        </div>

      </div>
    </div>

    {{-- Mensajes --}}
    <div class="rounded-2xl p-4 sm:p-6
                bg-white/90 dark:bg-[#0B1A10]/80 backdrop-blur
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03)]">

      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3">
        <h2 class="text-sm font-semibold text-[#123617] dark:text-[#92b95d]">
          Mensajes del Administrador
        </h2>

        <span class="w-fit text-[10px] px-2 py-1 rounded-full
                     bg-[#EAF6E7] dark:bg-[#0A2012]
                     border border-[#81a553]/30
                     text-[#123617] dark:text-[#EAF3EA]">
          {{ $misAdvertencias->count() }} mensaje(s)
        </span>
      </div>

      <div class="mt-4 space-y-3">
        @forelse($misAdvertencias as $adv)
          @php
              $nivel = strtolower(trim((string) data_get($adv, 'nivel', 'info')));
              if (!in_array($nivel, ['info','success','warning','danger'], true)) $nivel = 'info';

              $map = [
                  'info' => ['icon' => 'ℹ️','style' => 'border-blue-400 bg-blue-50 dark:bg-blue-900/20'],
                  'success' => ['icon' => '✅','style' => 'border-green-400 bg-green-50 dark:bg-green-900/20'],
                  'warning' => ['icon' => '⚠️','style' => 'border-yellow-400 bg-yellow-50 dark:bg-yellow-900/20'],
                  'danger' => ['icon' => '🚨','style' => 'border-red-400 bg-red-50 dark:bg-red-900/20'],
              ];

              $cfg = $map[$nivel];
              $isDirect = !is_null(data_get($adv, 'user_id'));
              $titulo  = (string) data_get($adv, 'titulo', '(Sin título)');
              $mensaje = (string) data_get($adv, 'mensaje', '');

              $createdAt = data_get($adv, 'created_at');
              $createdFmt = '—';
              try {
                  if ($createdAt) $createdFmt = \Carbon\Carbon::parse($createdAt)->format('d-m-Y H:i');
              } catch (\Throwable $e) {
                  $createdFmt = '—';
              }
          @endphp

          <div class="rounded-xl p-3 sm:p-4 border {{ $cfg['style'] }}">
            <div class="flex items-start gap-3">
              <div class="text-lg leading-none mt-[1px]">
                {{ $cfg['icon'] }}
              </div>

              <div class="min-w-0 flex-1">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                  <p class="text-[13px] sm:text-sm font-semibold text-[#123617] dark:text-[#EAF3EA] leading-snug">
                    {{ $titulo }}
                  </p>

                  <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[10px] px-2 py-1 rounded-full
                                 bg-white/80 dark:bg-[#0D1E12]/50
                                 border border-[#DDEEDD] dark:border-[#16351F]
                                 text-[#123617] dark:text-[#EAF3EA]/80">
                      {{ strtoupper($nivel) }}
                    </span>

                    <span class="text-[10px] px-2 py-1 rounded-full
                                 bg-white/80 dark:bg-[#0D1E12]/50
                                 border border-[#DDEEDD] dark:border-[#16351F]
                                 text-[#123617] dark:text-[#EAF3EA]/80">
                      {{ $isDirect ? 'Directo' : 'General' }}
                    </span>
                  </div>
                </div>

                <p class="text-sm mt-1 text-[#3b6a33]/80 dark:text-[#EAF3EA]/70 break-words">
                  {{ $mensaje }}
                </p>

                <div class="mt-2 text-xs text-[#3b6a33]/60 dark:text-[#EAF3EA]/50">
                  {{ $createdFmt }}
                </div>
              </div>
            </div>
          </div>

        @empty
          <div class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
            No tienes mensajes pendientes.
          </div>
        @endforelse
      </div>
    </div>

  </div>
</div>
@endsection