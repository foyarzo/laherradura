@extends('layouts.app')

@section('content')
<div class="space-y-6">

    {{-- Header responsive: 2 filas en mobile --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div class="min-w-0">
            <h1 class="text-xl sm:text-2xl font-semibold text-[#123617] dark:text-[#92b95d] truncate">
                Chat Admin · Conversación #{{ $thread->id }}
            </h1>
            <p class="text-xs sm:text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60 mt-1">
                Cliente: <span class="font-semibold">{{ $thread->user->name ?? '—' }}</span>
                <span class="mx-1">·</span>
                Estado: <span class="font-semibold">{{ strtoupper($thread->status) }}</span>
            </p>
        </div>

        <div class="flex flex-col xs:flex-row sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('chat.home') }}"
               class="w-full sm:w-auto text-center rounded-xl px-4 py-2 text-sm font-semibold
                      border border-[#DDEEDD] dark:border-[#16351F]
                      text-[#123617] dark:text-[#EAF3EA]
                      hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40">
                Volver
            </a>

            @if($thread->status === 'abierto')
                <form method="POST" action="{{ route('chat.close', $thread) }}" class="w-full sm:w-auto">
                    @csrf
                    <button class="w-full sm:w-auto rounded-xl px-4 py-2 text-sm font-semibold bg-red-600 text-white hover:opacity-90">
                        Cerrar
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl px-5 py-3 border border-green-200 bg-green-50 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl px-5 py-3 border border-red-200 bg-red-50 text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden
                bg-white/90 dark:bg-[#0B1A10]/80
                border border-[#DDEEDD] dark:border-[#16351F]
                shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_18px_50px_-20px_rgba(18,54,23,0.45)]">

        {{-- Top bar responsive --}}
        <div class="p-4 sm:p-5 border-b border-[#DDEEDD] dark:border-[#16351F]
                    flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div class="text-sm text-[#123617] dark:text-[#EAF3EA] font-semibold">
                Mensajes
            </div>

            <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Último: {{ optional($thread->last_message_at)->format('d-m H:i') ?? '—' }}
            </div>
        </div>

        @php
            // ✅ Orden cronológico (antiguo -> nuevo) para evitar “aparecen arriba”
            $messagesSorted = collect($messages ?? [])->sortBy(fn($m) => $m->created_at ?? now())->values();
        @endphp

        {{-- Chat box --}}
        <div id="chatMessages"
             class="p-3 sm:p-5 space-y-3 max-h-[60vh] sm:max-h-[62vh] overflow-auto overscroll-contain">

            @forelse($messagesSorted as $m)
                @php
                    $mine = (int)$m->sender_id === (int)auth()->id();
                    $time = optional($m->created_at)->format('d-m H:i') ?? '—';
                    $name = $m->sender->name ?? '—';
                @endphp

                <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                    <div class="w-full max-w-[92%] sm:max-w-[78%] md:max-w-[70%]">
                        <div class="rounded-2xl px-4 py-3 text-sm break-words
                                    {{ $mine
                                        ? 'bg-[#123617] text-white'
                                        : 'bg-[#F6FBF4] dark:bg-[#07120A]/40 text-[#123617] dark:text-[#EAF3EA] border border-[#DDEEDD] dark:border-[#16351F]'
                                    }}">
                            <div class="flex items-center justify-between gap-3 text-[11px] opacity-80 mb-1">
                                <span class="truncate">{{ $name }}</span>
                                <span class="shrink-0">{{ $time }}</span>
                            </div>
                            <div class="whitespace-pre-line leading-relaxed">{{ $m->body }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div id="noMessagesYet" class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                    No hay mensajes aún.
                </div>
            @endforelse
        </div>

        {{-- Composer sticky en mobile --}}
        @if($thread->status === 'abierto')
            <div class="border-t border-[#DDEEDD] dark:border-[#16351F]">
                <form id="chatForm" method="POST" action="{{ route('chat.send', $thread) }}"
                      class="p-3 sm:p-5 flex flex-col sm:flex-row gap-2 sm:gap-3
                             bg-white/80 dark:bg-[#0B1A10]/70
                             sticky bottom-0">
                    @csrf

                    <textarea id="chatInput" name="body" rows="2" required
                              class="w-full flex-1 rounded-xl px-3 py-2 text-sm
                                     border border-[#DDEEDD] dark:border-[#16351F]
                                     bg-white/90 dark:bg-[#07120A]
                                     text-[#123617] dark:text-[#EAF3EA]
                                     focus:outline-none focus:ring-2 focus:ring-[#92b95d]/40"
                              placeholder="Responder al cliente..."></textarea>

                    <button type="submit"
                            class="w-full sm:w-auto rounded-xl px-4 py-2 text-sm font-semibold
                                   bg-[#123617] text-white hover:opacity-90">
                        Enviar
                    </button>
                </form>
            </div>
        @else
            <div class="p-4 sm:p-5 text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Conversación cerrada.
            </div>
        @endif

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const threadId = @json($thread->id);
    const me = @json(auth()->id());
    const box = document.getElementById('chatMessages');

    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');

    const scrollBottom = () => {
        if (!box) return;
        box.scrollTop = box.scrollHeight;
    };

    // scroll al final al cargar
    scrollBottom();
    setTimeout(scrollBottom, 0);

    /* ENTER = ENVIAR | SHIFT+ENTER = salto */
    if (form && input) {
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!input.value.trim()) return;

                if (form.dataset.submitting === '1') return;
                form.dataset.submitting = '1';

                form.requestSubmit();

                setTimeout(() => { form.dataset.submitting = '0'; }, 600);
            }
        });
    }

    /* REALTIME ECHO */
    if (!window.Echo || !box) return;

    const appendMessage = ({ mine, sender_name, created_at, body }) => {
        document.getElementById('noMessagesYet')?.remove();

        const wrap = document.createElement('div');
        wrap.className = 'flex ' + (mine ? 'justify-end' : 'justify-start');

        const col = document.createElement('div');
        col.className = 'w-full max-w-[92%] sm:max-w-[78%] md:max-w-[70%]';

        const bubble = document.createElement('div');
        bubble.className = 'rounded-2xl px-4 py-3 text-sm break-words ' + (
            mine
                ? 'bg-[#123617] text-white'
                : 'bg-[#F6FBF4] dark:bg-[#07120A]/40 text-[#123617] dark:text-[#EAF3EA] border border-[#DDEEDD] dark:border-[#16351F]'
        );

        const meta = document.createElement('div');
        meta.className = 'flex items-center justify-between gap-3 text-[11px] opacity-80 mb-1';
        meta.innerHTML = `
            <span class="truncate">${(sender_name ?? '—')}</span>
            <span class="shrink-0">${(created_at ?? '')}</span>
        `;

        const text = document.createElement('div');
        text.className = 'whitespace-pre-line leading-relaxed';
        text.textContent = body ?? '';

        bubble.appendChild(meta);
        bubble.appendChild(text);
        col.appendChild(bubble);
        wrap.appendChild(col);

        box.appendChild(wrap);
        scrollBottom();
    };

    window.Echo.channel(`chat.thread.${threadId}`)
        .listen('.message.sent', (e) => {
            if (parseInt(e.thread_id) !== parseInt(threadId)) return;

            // Si no quieres duplicados mientras tu submit recarga, ignora tus mensajes
            if (parseInt(e.sender_id) === parseInt(me)) return;

            appendMessage({
                mine: false,
                sender_name: e.sender_name,
                created_at: e.created_at,
                body: e.body
            });
        });

});
</script>
@endsection
