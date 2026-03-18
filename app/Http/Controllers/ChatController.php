<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ChatThread;
use App\Models\ChatMessage;
use App\Events\ChatMessageSent;
use App\Events\ChatInboxUpdated;

class ChatController extends Controller
{
    private function isAdmin($user): bool
    {
        return ($user->role === 'admin') || ($user->roles?->contains('slug', 'admin'));
    }

    private function unreadCountForAdmin(ChatThread $thread): int
    {
        // "No leído" = mensajes del cliente (dueño del thread) sin read_at
        return (int) $thread->messages()
            ->where('sender_id', (int) $thread->user_id)
            ->whereNull('read_at')
            ->count();
    }

    public function home(Request $request)
    {
        $user = $request->user();
        $isAdmin = $this->isAdmin($user);

        if ($isAdmin) {
            $threads = ChatThread::with(['user'])
                ->withCount([
                    'messages as unread_count' => function ($q) {
                        $q->whereNull('read_at')
                          // mensajes enviados por el cliente (owner del thread)
                          ->whereColumn('sender_id', 'chat_threads.user_id');
                    }
                ])
                ->orderByDesc('last_message_at')
                ->paginate(20);

            return view('chat.admin_home', compact('threads'));
        }

        $thread = ChatThread::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'abierto'],
            ['last_message_at' => now()]
        );

        $messages = $thread->messages()
            ->with('sender')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();

        return view('chat.home', compact('thread', 'messages'));
    }

    public function show(Request $request, ChatThread $thread)
    {
        $user = $request->user();
        $isAdmin = $this->isAdmin($user);

        if (!$isAdmin && (int)$thread->user_id !== (int)$user->id) {
            abort(403);
        }

        // ✅ Si es admin: marcar como leídos los mensajes del cliente al abrir
        if ($isAdmin) {
            DB::transaction(function () use ($thread) {
                $thread->messages()
                    ->where('sender_id', (int) $thread->user_id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            });

            // Emitir update a la bandeja (para que se quite badge/dot)
            $unread = $this->unreadCountForAdmin($thread); // debería quedar 0
            broadcast(new ChatInboxUpdated(thread: $thread, unreadCount: $unread))->toOthers();
        }

        $messages = $thread->messages()
            ->with('sender')
            ->latest()
            ->take(80)
            ->get()
            ->reverse()
            ->values();

        return view($isAdmin ? 'chat.thread_admin' : 'chat.thread_user', compact('thread', 'messages'));
    }

    public function send(Request $request, ChatThread $thread)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $user = $request->user();
        $isAdmin = $this->isAdmin($user);

        if (!$isAdmin && (int)$thread->user_id !== (int)$user->id) {
            abort(403);
        }

        $message = null;

        DB::transaction(function () use ($thread, $user, $data, $isAdmin, &$message) {
            // ✅ read_at:
            // - si escribe el cliente -> NULL (no leído por admin)
            // - si escribe admin -> now() (no cuenta como no leído)
            $message = ChatMessage::create([
                'thread_id' => $thread->id,
                'sender_id' => $user->id,
                'body' => $data['body'],
                'read_at' => $isAdmin ? now() : null,
            ]);

            if ($isAdmin && !$thread->assigned_admin_id) {
                $thread->assigned_admin_id = $user->id;
            }

            $thread->last_message_at = now();
            $thread->save();
        });

        if ($message) {
            // Realtime del thread (para el otro lado)
            broadcast(new ChatMessageSent($message))->toOthers();

            // ✅ Update bandeja admin con unread_count
            $unread = $this->unreadCountForAdmin($thread);

            // Importante: aquí mandamos message + unreadCount para body_preview / sender_name
            broadcast(new ChatInboxUpdated(message: $message, unreadCount: $unread))->toOthers();
        }

        return back()->with('success', 'Mensaje enviado.');
    }

    public function close(Request $request, ChatThread $thread)
    {
        $user = $request->user();
        $isAdmin = $this->isAdmin($user);

        if (!$isAdmin && (int)$thread->user_id !== (int)$user->id) {
            abort(403);
        }

        $thread->update(['status' => 'cerrado']);

        // ✅ Update bandeja (status + unread)
        $unread = $this->unreadCountForAdmin($thread);
        broadcast(new ChatInboxUpdated(thread: $thread, unreadCount: $unread))->toOthers();

        return back()->with('success', 'Conversación cerrada.');
    }
}