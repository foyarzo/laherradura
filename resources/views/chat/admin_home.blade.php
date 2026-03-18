@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Header responsive (2 filas en mobile) --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 sm:gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#123617] dark:text-[#EAF3EA]">
                Chat - Bandeja Admin
            </h1>
            <p class="text-sm text-[#40624a] dark:text-[#9ec79f] mt-1">
                Cualquier admin puede responder.
            </p>
        </div>
    </div>

    <div class="rounded-2xl overflow-hidden bg-white/90 dark:bg-[#0B1A10]/80 border border-[#DDEEDD] dark:border-[#16351F]">

        {{-- MOBILE: cards --}}
        <div class="block sm:hidden p-3 space-y-3">
            @forelse($threads as $t)
                @php $unread = (int)($t->unread_count ?? 0); @endphp

                <div id="inboxRow-{{ $t->id }}"
                     data-thread-id="{{ $t->id }}"
                     data-unread="{{ $unread }}"
                     class="border rounded-xl p-4
                            border-[#DDEEDD] dark:border-[#16351F]
                            bg-white/80 dark:bg-[#07120A]/60
                            {{ $unread > 0 ? 'ring-1 ring-red-400/30 bg-[#fff8e6] dark:bg-[#1a1408]' : '' }}">

                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                                    Thread #{{ $t->id }}
                                </div>

                                @if($unread > 0)
                                    <span class="inline-flex items-center justify-center
                                                 min-w-[22px] h-[22px] px-2
                                                 text-xs font-bold rounded-full
                                                 bg-red-600 text-white unread-badge">
                                        {{ $unread }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-1 text-sm text-[#40624a] dark:text-[#9ec79f] break-words">
                                {{ $t->user->name ?? '—' }}
                                @if($unread > 0)
                                    <span class="ml-2 inline-block w-2 h-2 rounded-full bg-red-500 animate-pulse unread-dot"></span>
                                @endif
                            </div>
                        </div>

                        <a class="shrink-0 inline-flex items-center justify-center rounded-xl px-4 py-2 text-xs font-semibold
                                  border border-[#DDEEDD] dark:border-[#16351F]
                                  text-[#123617] dark:text-[#EAF3EA]
                                  hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40"
                           href="{{ route('chat.show', $t) }}">
                            Abrir
                        </a>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <div class="uppercase tracking-wide text-[#123617]/60 dark:text-[#EAF3EA]/60">
                                Estado
                            </div>
                            <div class="inbox-status text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                                {{ strtoupper($t->status) }}
                            </div>
                        </div>

                        <div class="text-right">
                            <div class="uppercase tracking-wide text-[#123617]/60 dark:text-[#EAF3EA]/60">
                                Último
                            </div>
                            <div class="inbox-last text-sm font-semibold text-[#123617] dark:text-[#EAF3EA]">
                                {{ $t->last_message_at ? $t->last_message_at->format('d-m H:i') : '—' }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-10 text-center text-sm text-[#40624a] dark:text-[#9ec79f]">
                    Sin conversaciones.
                </div>
            @endforelse
        </div>

        {{-- DESKTOP/TABLET: tabla --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm min-w-[820px]">
                <thead class="bg-[#EAF6E7] dark:bg-[#0A2012]">
                    <tr class="text-left text-xs uppercase tracking-wide text-[#123617]/70 dark:text-[#EAF3EA]/70">
                        <th class="px-4 md:px-6 py-3">Thread</th>
                        <th class="px-4 md:px-6 py-3">Cliente</th>
                        <th class="px-4 md:px-6 py-3">Estado</th>
                        <th class="px-4 md:px-6 py-3">Último</th>
                        <th class="px-4 md:px-6 py-3 text-right">Acción</th>
                    </tr>
                </thead>

                <tbody id="inboxBody" class="divide-y divide-[#DDEEDD] dark:divide-[#16351F]">
                    @forelse($threads as $t)
                        @php $unread = (int)($t->unread_count ?? 0); @endphp

                        <tr id="inboxRow-{{ $t->id }}"
                            data-thread-id="{{ $t->id }}"
                            data-unread="{{ $unread }}"
                            class="hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40 transition-colors
                                   {{ $unread > 0 ? 'bg-[#fff8e6] dark:bg-[#1a1408]' : '' }}">

                            <td class="px-4 md:px-6 py-4 font-semibold">
                                #{{ $t->id }}

                                @if($unread > 0)
                                    <span class="ml-2 inline-flex items-center justify-center
                                                 min-w-[22px] h-[22px] px-2
                                                 text-xs font-bold rounded-full
                                                 bg-red-600 text-white unread-badge">
                                        {{ $unread }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 md:px-6 py-4">
                                {{ $t->user->name ?? '—' }}

                                @if($unread > 0)
                                    <span class="ml-2 inline-block w-2 h-2 rounded-full bg-red-500 animate-pulse unread-dot"></span>
                                @endif
                            </td>

                            <td class="px-4 md:px-6 py-4">
                                <span class="inbox-status">{{ strtoupper($t->status) }}</span>
                            </td>

                            <td class="px-4 md:px-6 py-4">
                                <span class="inbox-last">
                                    {{ $t->last_message_at ? $t->last_message_at->format('d-m H:i') : '—' }}
                                </span>
                            </td>

                            <td class="px-4 md:px-6 py-4 text-right">
                                <a class="inline-flex items-center rounded-xl px-4 py-2 text-xs font-semibold
                                          border border-[#DDEEDD] dark:border-[#16351F]
                                          text-[#123617] dark:text-[#EAF3EA]
                                          hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40"
                                   href="{{ route('chat.show', $t) }}">
                                    Abrir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-[#40624a] dark:text-[#9ec79f]">
                                Sin conversaciones.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[#DDEEDD] dark:border-[#16351F]">
            {{ $threads->links() }}
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Echo) return;

    const setUnreadUI = (row, unreadCount) => {
        unreadCount = parseInt(unreadCount || 0);
        row.dataset.unread = unreadCount;

        // desktop references (table)
        const tds = row.querySelectorAll('td');
        const tdThread = tds?.[0] || null;
        const tdCliente = tds?.[1] || null;

        // mobile references (card)
        const mobileBadgeHost = row.querySelector('.unread-badge')?.parentElement || row;
        const mobileDotHost   = row.querySelector('.unread-dot')?.parentElement || row;

        // helper to remove
        const removeUnread = () => {
            row.classList.remove('bg-[#fff8e6]');
            row.classList.remove('dark:bg-[#1a1408]');
            row.classList.remove('ring-1', 'ring-red-400/30');
            row.querySelector('.unread-badge')?.remove();
            row.querySelector('.unread-dot')?.remove();
        };

        if (!unreadCount || unreadCount <= 0) {
            removeUnread();
            return;
        }

        // add unread styles (works for both)
        row.classList.add('bg-[#fff8e6]');
        row.classList.add('dark:bg-[#1a1408]');
        row.classList.add('ring-1', 'ring-red-400/30');

        let badge = row.querySelector('.unread-badge');
        if (!badge) {
            badge = document.createElement('span');
            badge.className = "ml-2 inline-flex items-center justify-center min-w-[22px] h-[22px] px-2 text-xs font-bold rounded-full bg-red-600 text-white unread-badge";
            // try to append in desktop thread cell first, else in mobile header
            if (tdThread) tdThread.appendChild(badge);
            else mobileBadgeHost.appendChild(badge);
        }
        badge.textContent = unreadCount;

        let dot = row.querySelector('.unread-dot');
        if (!dot) {
            dot = document.createElement('span');
            dot.className = "ml-2 inline-block w-2 h-2 rounded-full bg-red-500 animate-pulse unread-dot";
            if (tdCliente) tdCliente.appendChild(dot);
            else mobileDotHost.appendChild(dot);
        }
    };

    const flashRow = (row) => {
        row.classList.add('ring-2', 'ring-red-400/50');
        setTimeout(() => row.classList.remove('ring-2', 'ring-red-400/50'), 1200);
    };

    window.Echo.channel('chat.inbox')
        .listen('.inbox.updated', (e) => {
            const row = document.getElementById(`inboxRow-${e.thread_id}`);

            if (!row) {
                window.location.reload();
                return;
            }

            const statusEl = row.querySelector('.inbox-status');
            const lastEl = row.querySelector('.inbox-last');

            if (statusEl) statusEl.textContent = (e.status || '').toString().toUpperCase();
            if (lastEl) lastEl.textContent = e.last_message_at || '—';

            setUnreadUI(row, e.unread_count ?? 0);
            flashRow(row);
        });
});
</script>
@endsection
