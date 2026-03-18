@extends('layouts.app')

@section('content')
@php
  $emailDomain = '@dispensariolaherradura.cl';
@endphp

<div class="space-y-8">

  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-[#123617] dark:text-[#92b95d]">
        Gestión de Usuarios
      </h1>
      <p class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
        Administrar usuarios del sistema
      </p>
    </div>

    <button onclick="openCreateModal()"
            class="px-4 py-2 rounded-lg text-sm font-medium
                   bg-[#1e4e25] hover:bg-[#123617] text-white
                   dark:bg-[#92b95d] dark:hover:bg-[#81a553]
                   dark:text-[#07120A]
                   transition-all">
      + Crear Usuario
    </button>
  </div>

  @if (session('ok'))
    <div class="rounded-2xl border border-[#CFE6C9] dark:border-[#1B3B22]
                bg-[#EAF6E7] dark:bg-[#0A2012]
                px-4 py-3 text-sm text-[#123617] dark:text-[#EAF3EA]">
      <div class="font-semibold">{{ session('ok') }}</div>
    </div>
  @endif

  @if ($errors->any())
    <div class="rounded-2xl border border-red-200 dark:border-red-900/40
                bg-red-50 dark:bg-[#1d0002]
                px-4 py-3 text-sm text-red-900 dark:text-red-100">
      <div class="font-semibold mb-1">No se pudo guardar:</div>
      <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="rounded-2xl overflow-hidden
              bg-white/90 dark:bg-[#0B1A10]/80
              border border-[#DDEEDD] dark:border-[#16351F]">

    <div class="p-5 border-b border-[#DDEEDD] dark:border-[#16351F]
                flex items-start justify-between gap-4">
      <div>
        <h2 class="text-lg font-semibold text-[#123617] dark:text-[#92b95d]">
          Advertencias / Mensajes
        </h2>
        <p class="mt-1 text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
          Crea mensajes globales o dirigidos a un usuario.
        </p>
      </div>
    </div>

    <div class="p-5">
      <form method="POST" action="{{ route('admin.advertencias.store') }}"
            class="grid grid-cols-1 md:grid-cols-12 gap-3">
        @csrf

        <div class="md:col-span-4">
          <label class="block text-xs font-semibold text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mb-1">
            Título
          </label>
          <input name="titulo" type="text" required
                 class="w-full rounded-xl px-3 py-2 text-sm
                        border border-[#CFE6C9] dark:border-[#1B3B22]
                        bg-white dark:bg-[#07120A]
                        text-[#123617] dark:text-[#EAF3EA]"
                 placeholder="Ej: Verificación pendiente">
        </div>

        <div class="md:col-span-4">
          <label class="block text-xs font-semibold text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mb-1">
            Usuario (opcional)
          </label>
          <select name="user_id"
                  class="w-full rounded-xl px-3 py-2 text-sm
                         border border-[#CFE6C9] dark:border-[#1B3B22]
                         bg-white dark:bg-[#07120A]
                         text-[#123617] dark:text-[#EAF3EA]">
            <option value="">— Global (todos) —</option>
            @foreach($users as $uOpt)
              <option value="{{ $uOpt->id }}">{{ $uOpt->name }} · {{ $uOpt->email }}</option>
            @endforeach
          </select>
          <p class="mt-1 text-[11px] text-[#3b6a33]/65 dark:text-[#EAF3EA]/55">
            Si dejas vacío, lo verán todos.
          </p>
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mb-1">
            Nivel
          </label>
          <select name="nivel" required
                  class="w-full rounded-xl px-3 py-2 text-sm
                         border border-[#CFE6C9] dark:border-[#1B3B22]
                         bg-white dark:bg-[#07120A]
                         text-[#123617] dark:text-[#EAF3EA]">
            <option value="info">Informacion</option>
            <option value="success">Agradecimiento</option>
            <option value="warning">Advertencia</option>
            <option value="danger">Peligro</option>
          </select>
        </div>

        <div class="md:col-span-2 flex items-end">
          <button type="submit"
                  class="w-full rounded-xl px-4 py-2 text-sm font-semibold
                         bg-[#1e4e25] hover:bg-[#123617] text-white
                         dark:bg-[#92b95d] dark:hover:bg-[#81a553]
                         dark:text-[#07120A]
                         transition-all">
            Publicar
          </button>
        </div>

        <div class="md:col-span-12">
          <label class="block text-xs font-semibold text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mb-1">
            Mensaje
          </label>
          <textarea name="mensaje" rows="3" required
                    class="w-full rounded-xl px-3 py-2 text-sm
                           border border-[#CFE6C9] dark:border-[#1B3B22]
                           bg-white dark:bg-[#07120A]
                           text-[#123617] dark:text-[#EAF3EA]"
                    placeholder="Escribe el mensaje para el usuario..."></textarea>
        </div>

        <div class="md:col-span-6">
          <label class="block text-xs font-semibold text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mb-1">
            Visible desde (opcional)
          </label>
          <input name="starts_at" type="datetime-local"
                 class="w-full rounded-xl px-3 py-2 text-sm
                        border border-[#CFE6C9] dark:border-[#1B3B22]
                        bg-white dark:bg-[#07120A]
                        text-[#123617] dark:text-[#EAF3EA]">
        </div>

        <div class="md:col-span-6">
          <label class="block text-xs font-semibold text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mb-1">
            Visible hasta (opcional)
          </label>
          <input name="ends_at" type="datetime-local"
                 class="w-full rounded-xl px-3 py-2 text-sm
                        border border-[#CFE6C9] dark:border-[#1B3B22]
                        bg-white dark:bg-[#07120A]
                        text-[#123617] dark:text-[#EAF3EA]">
        </div>
      </form>
    </div>

    <div class="border-t border-[#DDEEDD] dark:border-[#16351F]"></div>

    <div class="p-5">
      @php
        $advertencias = $advertencias ?? collect();

        $badge = [
          'info' => 'bg-[#EAF6E7] dark:bg-[#0A2012] border-[#81a553]/25 dark:border-[#1B3B22] text-[#123617] dark:text-[#EAF3EA]',
          'success' => 'bg-green-50 dark:bg-[#0A2012] border-green-200/60 dark:border-green-900/40 text-green-900 dark:text-green-100',
          'warning' => 'bg-yellow-50 dark:bg-[#1a1407] border-yellow-200/60 dark:border-yellow-900/40 text-yellow-900 dark:text-yellow-100',
          'danger' => 'bg-red-50 dark:bg-[#1d0002] border-red-200/60 dark:border-red-900/40 text-red-900 dark:text-red-100',
        ];
      @endphp

      <div class="flex items-center justify-between mb-3">
        <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
          Últimas advertencias
        </div>
        <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
          {{ $advertencias->count() }} registros
        </div>
      </div>

      <div class="space-y-3">
        @forelse($advertencias as $a)
          @php
            $nivel = strtolower(trim((string)($a->nivel ?? 'info')));
            if (!in_array($nivel, ['info','success','warning','danger'], true)) $nivel = 'info';

            $toUser = $a->user ? ($a->user->name . ' · ' . $a->user->email) : 'Global (todos)';
            $creator = $a->creator ? $a->creator->name : '—';
            $when = $a->created_at?->format('d-m-Y H:i') ?? '—';
          @endphp

          <div class="rounded-xl p-4 border border-[#DDEEDD] dark:border-[#16351F]
                      bg-white/70 dark:bg-[#0D1E12]/35">
            <div class="flex items-start justify-between gap-4">
              <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border {{ $badge[$nivel] ?? $badge['info'] }}">
                    {{ strtoupper($nivel) }}
                  </span>

                  <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                    {{ $a->titulo }}
                  </div>
                </div>

                <div class="mt-2 text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                  <span class="font-semibold">Para:</span> {{ $toUser }}
                  <span class="mx-2 opacity-50">·</span>
                  <span class="font-semibold">Por:</span> {{ $creator }}
                  <span class="mx-2 opacity-50">·</span>
                  <span class="font-semibold">Fecha:</span> {{ $when }}
                </div>

                <div class="mt-2 text-sm text-[#123617] dark:text-[#EAF3EA]/90 whitespace-pre-line">
                  {{ $a->mensaje }}
                </div>

                <div class="mt-2 text-[11px] text-[#3b6a33]/65 dark:text-[#EAF3EA]/55">
                  Vigencia:
                  <span class="font-semibold">{{ $a->starts_at?->format('d-m-Y H:i') ?? '—' }}</span>
                  <span class="opacity-60">→</span>
                  <span class="font-semibold">{{ $a->ends_at?->format('d-m-Y H:i') ?? '—' }}</span>
                  <span class="mx-2 opacity-50">·</span>
                  <span class="font-semibold">Activa:</span> {{ $a->activa ? 'Sí' : 'No' }}
                </div>
              </div>

              <div class="shrink-0 text-right">
                <form method="POST" action="{{ route('admin.advertencias.destroy', $a) }}"
                      onsubmit="return confirm('¿Eliminar esta advertencia?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                          class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-lg
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
        @empty
          <div class="rounded-xl p-4 border border-[#DDEEDD] dark:border-[#16351F]
                      bg-white/70 dark:bg-[#0D1E12]/35
                      text-sm text-[#3b6a33]/75 dark:text-[#EAF3EA]/60">
            No hay advertencias aún.
          </div>
        @endforelse
      </div>
    </div>
  </div>

  <div class="rounded-2xl overflow-hidden
              bg-white/90 dark:bg-[#0B1A10]/80
              border border-[#DDEEDD] dark:border-[#16351F]">

    <div class="p-5 border-b border-[#DDEEDD] dark:border-[#16351F]
                flex items-center justify-between gap-4">
      <div>
        <h2 class="text-lg font-semibold text-[#123617] dark:text-[#92b95d]">
          Usuarios
        </h2>
        <p class="mt-1 text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
          Lista de usuarios registrados en el sistema.
        </p>
      </div>

      <div class="text-xs font-semibold text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
        {{ $users->total() ?? $users->count() }} registros
      </div>
    </div>

    <div class="p-5">
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($users as $user)
          @php
            $currentRole = $user->roles->first();
            $currentRoleId = $currentRole?->id;

            $roleLabel = $currentRole?->name ?? '—';
            $roleKey = strtolower(trim((string)$roleLabel));

            $email = (string)($user->email ?? '');
            $domainLower = strtolower($emailDomain);
            $isInternal = $email && str_ends_with(strtolower($email), $domainLower);
            $username = $isInternal ? substr($email, 0, -strlen($domainLower)) : $email;

            $roleTone = 'text-[#3b6a33]/75 dark:text-[#EAF3EA]/60';
            if (str_contains($roleKey, 'admin')) $roleTone = 'text-[#a77b1f] dark:text-[#FFD37A]';
            if (str_contains($roleKey, 'oper'))  $roleTone = 'text-[#1f6b5e] dark:text-[#77e7d4]';

            $chip = 'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold
                     bg-[#EAF6E7] text-[#123617]
                     dark:bg-[#07120A] dark:text-[#EAF3EA]
                     border border-[#CFE6C9] dark:border-[#1B3B22]';

            $statusPill = $user->is_active
              ? 'bg-[#EAF6E7] dark:bg-[#0A2012] border-[#81a553]/30 text-[#123617] dark:text-[#EAF3EA]'
              : 'bg-[#FFECEC] dark:bg-[#1D0002] border-[#F53003]/20 text-[#B10E00] dark:text-[#FF4433]';
          @endphp

          <div class="rounded-2xl
                      bg-white/70 dark:bg-[#0D1E12]/35
                      border border-[#DDEEDD] dark:border-[#16351F]
                      hover:border-[#CFE6C9] dark:hover:border-[#1B3B22]
                      transition-all">

            <div class="p-4 flex items-start justify-between gap-4">
              <div class="min-w-0">
                <div class="flex items-center gap-3">
                  <div class="h-10 w-10 rounded-xl
                              bg-[#EAF6E7] dark:bg-[#07120A]
                              border border-[#CFE6C9] dark:border-[#1B3B22]
                              flex items-center justify-center
                              text-[#123617] dark:text-[#EAF3EA]
                              font-bold select-none">
                    {{ mb_strtoupper(mb_substr((string)$user->name, 0, 1)) }}
                  </div>

                  <div class="min-w-0">
                    <div class="flex items-center gap-2 min-w-0">
                      <div class="font-semibold text-[#123617] dark:text-[#EAF3EA] truncate">
                        {{ $user->name }}
                      </div>
                      <span class="text-[12px] font-semibold {{ $roleTone }}">
                        {{ $roleLabel }}
                      </span>
                    </div>

                    <div class="mt-0.5 text-[12px] text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 truncate">
                      {{ $user->email }}
                    </div>
                  </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                  <span class="{{ $chip }}">ID: #{{ $user->id }}</span>
                  <span class="{{ $chip }}">RUT: {{ $user->rut }}</span>
                  @if(!empty($user->phone))
                    <span class="{{ $chip }}">Tel: {{ $user->phone }}</span>
                  @endif
                  @if($isInternal)
                    <span class="{{ $chip }}">{{ $username }}</span>
                  @endif
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold border {{ $statusPill }}">
                    {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                  </span>
                </div>
              </div>

              <div class="shrink-0 flex flex-col items-end gap-2">
                <button
                  type="button"
                  onclick="openEditModal(
                    {{ $user->id }},
                    @js($user->name),
                    @js($user->email),
                    @js($currentRoleId),
                    @js($user->rut),
                    @js($user->phone),
                    {{ $user->is_active ? 'true' : 'false' }}
                  )"
                  class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-xl
                         bg-[#1e4e25] hover:bg-[#123617] text-white
                         dark:bg-[#92b95d] dark:hover:bg-[#81a553] dark:text-[#07120A]
                         transition-all">
                  Editar
                </button>

                <form method="POST" action="{{ route('admin.usuarios.destroy', $user) }}" class="inline w-full"
                      onsubmit="return confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                          class="w-full inline-flex items-center justify-center px-3 py-2 text-xs font-semibold rounded-xl
                                 bg-[#FFECEC] hover:bg-[#ffdede]
                                 dark:bg-[#1D0002] dark:hover:bg-[#2a0003]
                                 border border-[#F53003]/25
                                 text-[#B10E00] dark:text-[#FF4433]
                                 transition-all">
                    Eliminar
                  </button>
                </form>
              </div>
            </div>

          </div>
        @empty
          <div class="col-span-full rounded-xl p-6 border border-[#DDEEDD] dark:border-[#16351F]
                      bg-white/70 dark:bg-[#0D1E12]/35
                      text-sm text-[#3b6a33]/75 dark:text-[#EAF3EA]/60 text-center">
            No hay usuarios registrados.
          </div>
        @endforelse
      </div>
    </div>
  </div>

  <div>
    {{ $users->links() }}
  </div>

</div>

<div id="userModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-3 sm:p-6">
<div class="bg-white dark:bg-[#0B1A10]
            w-full max-w-[380px]
            rounded-2xl p-4 space-y-4
            border border-[#DDEEDD] dark:border-[#16351F]
            overflow-y-auto"
     style="max-height:500px;">

    <div class="flex items-start justify-between gap-3">
      <div>
        <h2 id="modalTitle" class="text-base sm:text-lg font-semibold">Crear Usuario</h2>
        <p id="modalHint" class="text-[11px] sm:text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mt-1">
          Completa los datos del usuario.
        </p>
      </div>
      <button type="button" onclick="closeModal()"
              class="px-2.5 py-1.5 rounded-lg text-[11px] sm:text-xs font-semibold
                     bg-white/80 dark:bg-[#0D1E12]/70 backdrop-blur
                     border border-[#CFE6C9] dark:border-[#1B3B22]
                     hover:border-[#81a553] dark:hover:border-[#3b6a33]
                     transition-all">
        Cerrar
      </button>
    </div>

    <form id="userForm" method="POST" action="{{ route('admin.usuarios.store') }}">
      @csrf
      <input type="hidden" id="formMethod" name="_method" value="POST">
      <input type="hidden" id="editingId" value="">

      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">

        <div>
          <label class="block text-[11px] sm:text-sm mb-1">Nombre</label>
          <input id="userName" name="name" type="text" required
                 class="w-full px-3 py-2 sm:py-2 rounded-md border text-sm
                        border-[#CFE6C9] dark:border-[#1B3B22]
                        bg-white dark:bg-[#07120A]
                        text-[#123617] dark:text-[#EAF3EA]">
        </div>

        <div>
          <label class="block text-[11px] sm:text-sm mb-1">RUT</label>
          <input id="userRut" name="rut" type="text" required
                 placeholder="12.345.678-9"
                 class="w-full px-3 py-2 sm:py-2 rounded-md border text-sm
                        border-[#CFE6C9] dark:border-[#1B3B22]
                        bg-white dark:bg-[#07120A]
                        text-[#123617] dark:text-[#EAF3EA]">
        </div>

        <div>
          <label class="block text-[11px] sm:text-sm mb-1">Teléfono</label>
          <input id="userPhone" name="phone" type="text"
                 placeholder="+56 9 1234 5678"
                 class="w-full px-3 py-2 sm:py-2 rounded-md border text-sm
                        border-[#CFE6C9] dark:border-[#1B3B22]
                        bg-white dark:bg-[#07120A]
                        text-[#123617] dark:text-[#EAF3EA]">
        </div>

        <div class="sm:col-span-2 xl:col-span-2">
          <label class="block text-[11px] sm:text-sm mb-1">Email</label>

          <div class="flex rounded-md overflow-hidden border border-[#CFE6C9] dark:border-[#1B3B22]
                      bg-white dark:bg-[#07120A]">
            <input id="emailUser"
                   type="text"
                   placeholder="usuario"
                   class="w-full px-3 py-2 text-sm bg-transparent outline-none
                          text-[#123617] dark:text-[#EAF3EA]">
            <span class="px-3 py-2 text-sm font-semibold
                         text-[#3b6a33]/80 dark:text-[#EAF3EA]/70
                         border-l border-[#CFE6C9] dark:border-[#1B3B22]">
              {{ $emailDomain }}
            </span>
          </div>

          <input id="userEmail" name="email" type="hidden" required>

          <p class="mt-1 text-[11px] sm:text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
            Se guardará como: <span class="font-semibold" id="emailPreview">—</span>
          </p>
        </div>

        <div>
          <label class="block text-[11px] sm:text-sm mb-1">Rol</label>
          <select id="userRole" name="role_id" required
                  class="w-full px-3 py-2 sm:py-2 rounded-md border text-sm
                         border-[#CFE6C9] dark:border-[#1B3B22]
                         bg-white dark:bg-[#07120A]
                         text-[#123617] dark:text-[#EAF3EA]">
            <option value="">— Seleccionar rol —</option>
            @foreach($roles as $role)
              <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="sm:col-span-2 xl:col-span-3 flex items-center justify-between gap-3 rounded-xl p-2.5 sm:p-3
                    border border-[#CFE6C9] dark:border-[#1B3B22]
                    bg-white/60 dark:bg-[#07120A]/60">

          <div>
            <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">Usuario activo</div>
            <div class="text-[11px] sm:text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
              Permite acceso al sistema
            </div>
          </div>

          <label class="inline-flex items-center cursor-pointer select-none">
            <input id="userIsActive" name="is_active" type="checkbox" value="1" class="sr-only">
            <span class="relative inline-flex h-6 w-11 items-center rounded-full
                         bg-[#CFE6C9] dark:bg-[#1B3B22] transition-colors">
              <span id="userIsActiveDot"
                    class="inline-block h-5 w-5 transform rounded-full bg-white
                           translate-x-1 transition-transform"></span>
            </span>
          </label>
        </div>

        <div id="passwordWrap" class="sm:col-span-2 xl:col-span-3">
          <label class="block text-[11px] sm:text-sm mb-1">Password</label>
          <input id="userPassword" name="password" type="password"
                 class="w-full px-3 py-2 sm:py-2 rounded-md border text-sm
                        border-[#CFE6C9] dark:border-[#1B3B22]
                        bg-white dark:bg-[#07120A]
                        text-[#123617] dark:text-[#EAF3EA]"
                 placeholder="Solo requerido al crear">
          <p class="mt-1 text-[11px] sm:text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
            En edición, si lo dejas vacío no se cambia.
          </p>
        </div>

      </div>

      <div class="flex justify-end gap-2 sm:gap-3 pt-4 sm:pt-5">
        <button type="button" onclick="closeModal()"
                class="px-3 sm:px-4 py-2 text-sm rounded-md
                       bg-white/80 dark:bg-[#0D1E12]/70 backdrop-blur
                       border border-[#CFE6C9] dark:border-[#1B3B22]
                       hover:border-[#81a553] dark:hover:border-[#3b6a33]
                       transition-all">
          Cancelar
        </button>

        <button type="submit"
                class="px-3 sm:px-4 py-2 text-sm rounded-md font-semibold
                       bg-[#1e4e25] hover:bg-[#123617] text-white
                       dark:bg-[#92b95d] dark:hover:bg-[#81a553]
                       dark:text-[#07120A]">
          Guardar
        </button>
      </div>

    </form>
  </div>
</div>

<script>
const modal = document.getElementById('userModal');
const form = document.getElementById('userForm');
const formMethod = document.getElementById('formMethod');
const editingId = document.getElementById('editingId');

const EMAIL_DOMAIN = @js($emailDomain);

const elEmailUser = document.getElementById('emailUser');
const elUserEmail = document.getElementById('userEmail');
const elEmailPreview = document.getElementById('emailPreview');

const elRut = document.getElementById('userRut');
const elPhone = document.getElementById('userPhone');

const elIsActive = document.getElementById('userIsActive');
const elIsActiveDot = document.getElementById('userIsActiveDot');

function normalizeUsername(v) {
  return (v || '')
    .trim()
    .toLowerCase()
    .replace(/\s+/g, '')
    .replace(/[^a-z0-9._-]/g, '');
}

function setEmailFromUsername(usernameRaw) {
  const username = normalizeUsername(usernameRaw);
  if (elEmailUser && elEmailUser.value !== username) elEmailUser.value = username;

  const full = username ? (username + EMAIL_DOMAIN) : '';
  if (elUserEmail) elUserEmail.value = full;
  if (elEmailPreview) elEmailPreview.textContent = full || '—';
}

function setActiveUI(isOn) {
  if (!elIsActive || !elIsActiveDot) return;

  elIsActive.checked = !!isOn;

  const track = elIsActiveDot.parentElement;
  if (!track) return;

  if (isOn) {
    track.classList.remove('bg-[#CFE6C9]', 'dark:bg-[#1B3B22]');
    track.classList.add('bg-[#1e4e25]', 'dark:bg-[#92b95d]');
    elIsActiveDot.classList.remove('translate-x-1');
    elIsActiveDot.classList.add('translate-x-5');
  } else {
    track.classList.remove('bg-[#1e4e25]', 'dark:bg-[#92b95d]');
    track.classList.add('bg-[#CFE6C9]', 'dark:bg-[#1B3B22]');
    elIsActiveDot.classList.remove('translate-x-5');
    elIsActiveDot.classList.add('translate-x-1');
  }
}

elIsActive?.addEventListener('change', () => setActiveUI(elIsActive.checked));
elEmailUser?.addEventListener('input', (e) => setEmailFromUsername(e.target.value));
elEmailUser?.addEventListener('blur', (e) => setEmailFromUsername(e.target.value));

function openCreateModal() {
  document.getElementById('modalTitle').innerText = 'Crear Usuario';
  document.getElementById('modalHint').innerText = 'Completa los datos del usuario.';

  form.action = @js(route('admin.usuarios.store'));
  formMethod.value = 'POST';
  if (editingId) editingId.value = '';

  document.getElementById('userName').value = '';
  document.getElementById('userRole').value = '';
  document.getElementById('userPassword').value = '';

  if (elRut) elRut.value = '';
  if (elPhone) elPhone.value = '';

  setActiveUI(true);
  document.getElementById('isActiveHidden')?.remove();

  setEmailFromUsername('');
  if (elEmailUser) {
    elEmailUser.disabled = false;
    elEmailUser.placeholder = 'usuario';
    elEmailUser.focus();
  }

  if (elRut) elRut.disabled = false;

  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function openEditModal(id, name, email, roleId, rut, phone, isActive) {
  document.getElementById('modalTitle').innerText = 'Editar Usuario';
  document.getElementById('modalHint').innerText = 'Actualiza los datos y el rol del usuario.';

  form.action = @js(url('/admin/usuarios')) + '/' + id;
  formMethod.value = 'PUT';
  if (editingId) editingId.value = String(id);

  document.getElementById('userName').value = name || '';
  document.getElementById('userRole').value = roleId ? String(roleId) : '';
  document.getElementById('userPassword').value = '';

  if (elRut) elRut.value = rut || '';
  if (elPhone) elPhone.value = phone || '';

  const active = (isActive === true || isActive === 'true' || isActive === 1 || isActive === '1');
  setActiveUI(active);

  if (active) {
    document.getElementById('isActiveHidden')?.remove();
  } else {
    if (!document.getElementById('isActiveHidden')) {
      const inp = document.createElement('input');
      inp.type = 'hidden';
      inp.name = 'is_active';
      inp.value = '0';
      inp.id = 'isActiveHidden';
      form.appendChild(inp);
    }
  }

  const e = String(email || '').trim();
  const domainLower = EMAIL_DOMAIN.toLowerCase();
  const hasDomain = e.toLowerCase().endsWith(domainLower);
  const userPart = hasDomain ? e.slice(0, -domainLower.length) : e;

  if (elEmailUser) {
    elEmailUser.value = userPart;
    elEmailUser.disabled = true;
    elEmailUser.placeholder = '—';
  }

  if (elUserEmail) elUserEmail.value = e;
  if (elEmailPreview) elEmailPreview.textContent = e || '—';

  if (elRut) elRut.disabled = false;

  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

function closeModal() {
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  if (elEmailUser) elEmailUser.disabled = false;
}

form?.addEventListener('submit', (e) => {
  const method = (formMethod?.value || 'POST').toUpperCase();

  const isEdit = method === 'PUT' || method === 'PATCH';

  if (!isEdit) {
    setEmailFromUsername(elEmailUser?.value || '');
    if (!elUserEmail?.value) {
      e.preventDefault();
      alert('Debes ingresar el nombre de usuario para el correo.');
      elEmailUser?.focus();
      return;
    }
  }

  if (!elIsActive?.checked) {
    const hasHidden = document.getElementById('isActiveHidden');
    if (!hasHidden) {
      const inp = document.createElement('input');
      inp.type = 'hidden';
      inp.name = 'is_active';
      inp.value = '0';
      inp.id = 'isActiveHidden';
      form.appendChild(inp);
    }
  } else {
    document.getElementById('isActiveHidden')?.remove();
  }
});

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
});

modal?.addEventListener('click', (e) => {
  if (e.target === modal) closeModal();
});

@if ($errors->any())
  openCreateModal();
@endif
</script>
@endsection