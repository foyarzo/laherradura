<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ChatMessage $message)
    {
        $this->message->load('sender');
    }

    public function broadcastConnection(): string
    {
        return config('broadcasting.default'); // debería ser "reverb"
    }

    public function broadcastOn(): Channel
    {
        return new Channel('chat.thread.' . $this->message->thread_id);
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'thread_id' => $this->message->thread_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name ?? '—',
            'body' => $this->message->body,
            'created_at' => $this->message->created_at->format('d-m H:i'),
        ];
    }
}