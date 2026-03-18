@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-[#123617] dark:text-[#92b95d]">
                Chat · Conversación #{{ $thread->id }}
            </h1>
            <p class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Estado: <span class="font-semibold">{{ strtoupper($thread->status) }}</span>
            </p>
        </div>

        <a href="{{ route('chat.home') }}"
           class="rounded-xl px-4 py-2 text-sm font-semibold
                  border border-[#DDEEDD] dark:border-[#16351F]
                  text-[#123617] dark:text-[#EAF3EA] hover:bg-[#F6FBF4] dark:hover:bg-[#07120A]/40">
            Volver
        </a>
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

        <div class="p-5 border-b border-[#DDEEDD] dark:border-[#16351F] flex items-center justify-between">
            <div class="text-sm text-[#123617] dark:text-[#EAF3EA] font-semibold">
                Mensajes
            </div>

            <div class="text-xs text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                Último: {{ optional($thread->last_message_at)->format('d-m H:i') ?? '—' }}
            </div>
        </div>

        <div id="chatMessages" class="p-5 space-y-3 max-h-[60vh] overflow-auto">
            @forelse($messages as $m)
                @php $mine = (int)$m->sender_id === (int)auth()->id(); @endphp

                <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] rounded-2xl px-4 py-3 text-sm
                                {{ $mine
                                    ? 'bg-[#123617] text-white'
                                    : 'bg-[#F6FBF4] dark:bg-[#07120A]/40 text-[#123617] dark:text-[#EAF3EA] border border-[#DDEEDD] dark:border-[#16351F]'
                                }}">
                        <div class="text-xs opacity-70 mb-1">
                            {{ $m->sender->name ?? '—' }} · {{ $m->created_at->format('d-m H:i') }}
                        </div>
                        <div class="whitespace-pre-line">{{ $m->body }}</div>
                    </div>
                </div>
            @empty
                <div class="text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
                    No hay mensajes aún.
                </div>
            @endforelse
        </div>

        @if($thread->status === 'abierto')
            <form id="chatForm" method="POST" action="{{ route('chat.send', $thread) }}"
                  class="p-5 border-t border-[#DDEEDD] dark:border-[#16351F] flex gap-3">
                @csrf

                <textarea id="chatInput" name="body" rows="2" required
                          class="flex-1 rounded-xl px-3 py-2 text-sm
                                 border border-[#DDEEDD] dark:border-[#16351F]
                                 bg-white/90 dark:bg-[#07120A]
                                 text-[#123617] dark:text-[#EAF3EA]"
                          placeholder="Escribe tu mensaje..."></textarea>

                <button type="submit"
                        class="rounded-xl px-4 py-2 text-sm font-semibold bg-[#123617] text-white hover:opacity-90">
                    Enviar
                </button>
            </form>
        @else
            <div class="p-5 text-sm text-[#3b6a33]/70 dark:text-[#EAF3EA]/60">
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

    /* ===============================
       ENTER PARA ENVIAR (Shift+Enter = salto)
    =============================== */
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');

    if (form && input) {
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();

                // no enviar vacío
                if (!input.value.trim()) return;

                // evitar doble submit
                if (form.dataset.submitting === '1') return;
                form.dataset.submitting = '1';

                form.requestSubmit();

                // anti doble envío por "tecla pegada"
                setTimeout(() => {
                    form.dataset.submitting = '0';
                }, 600);
            }
        });
    }

    /* ===============================
       REALTIME ECHO
    =============================== */
    if (!window.Echo || !box) return;

    const scrollBottom = () => { box.scrollTop = box.scrollHeight; };

    window.Echo.channel(`chat.thread.${threadId}`)
        .listen('.message.sent', (e) => {
            if (parseInt(e.thread_id) !== parseInt(threadId)) return;
            if (parseInt(e.sender_id) === parseInt(me)) return;

            const wrap = document.createElement('div');
            wrap.className = 'flex justify-start';

            const bubble = document.createElement('div');
            bubble.className =
                "max-w-[80%] rounded-2xl px-4 py-3 text-sm " +
                "bg-[#F6FBF4] dark:bg-[#07120A]/40 text-[#123617] dark:text-[#EAF3EA] " +
                "border border-[#DDEEDD] dark:border-[#16351F]";

            bubble.innerHTML = `
                <div class="text-xs opacity-70 mb-1">
                    ${e.sender_name ?? '—'} · ${e.created_at ?? ''}
                </div>
                <div class="whitespace-pre-line"></div>
            `;

            bubble.querySelector('.whitespace-pre-line').textContent = e.body ?? '';

            wrap.appendChild(bubble);
            box.appendChild(wrap);
            scrollBottom();
        });

    scrollBottom();
});
</script>
@endsection