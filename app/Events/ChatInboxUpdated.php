<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\ChatThread;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ChatInboxUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ?ChatMessage $message = null;
    public ChatThread $thread;
    public int $unreadCount = 0;

    /**
     * Puedes emitir:
     * - new ChatInboxUpdated(message: $message, unreadCount: X)
     * - new ChatInboxUpdated(thread: $thread, unreadCount: X)
     */
    public function __construct(?ChatMessage $message = null, ?ChatThread $thread = null, int $unreadCount = 0)
    {
        $this->unreadCount = $unreadCount;

        if ($message) {
            $this->message = $message;
            $this->message->load(['thread.user', 'sender']);
            $this->thread = $this->message->thread;
            return;
        }

        if ($thread) {
            $this->thread = $thread->loadMissing(['user']);
            return;
        }

        throw new \InvalidArgumentException('ChatInboxUpdated requiere $message o $thread');
    }

    public function broadcastConnection(): string
    {
        return config('broadcasting.default');
    }

    public function broadcastOn(): Channel
    {
        return new Channel('chat.inbox');
    }

    public function broadcastAs(): string
    {
        return 'inbox.updated';
    }

    public function broadcastWith(): array
    {
        $thread = $this->thread;

        // Si hay mensaje (cuando se envía), usar created_at real del mensaje
        $lastTime = $this->message?->created_at
            ? $this->message->created_at->format('d-m H:i')
            : optional($thread->last_message_at)->format('d-m H:i');

        return [
            'thread_id' => $thread->id,
            'status' => $thread->status,
            'last_message_at' => $lastTime,
            'client_name' => $thread->user->name ?? '—',
            'sender_name' => $this->message?->sender?->name ?? '—',
            'body_preview' => $this->message
                ? mb_strimwidth($this->message->body ?? '', 0, 70, '…')
                : '',
            'unread_count' => (int) $this->unreadCount,
        ];
    }
}