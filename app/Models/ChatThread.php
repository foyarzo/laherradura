<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ChatThread extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'assigned_admin_id',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'thread_id')
                    ->orderBy('created_at');
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    // Mensajes no leídos para admin (mensajes del cliente sin read_at)
    public function unreadCountForAdmin(): int
    {
        return (int) $this->messages()
            ->where('sender_id', $this->user_id)
            ->whereNull('read_at')
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAbiertos(Builder $query): Builder
    {
        return $query->where('status', 'abierto');
    }

    public function scopeOrdenados(Builder $query): Builder
    {
        return $query->orderByDesc('last_message_at');
    }
}