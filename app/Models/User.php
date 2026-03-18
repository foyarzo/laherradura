<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'rut',
        'phone',
        'is_active',
        'created_by',
        'password',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'is_active'         => 'boolean',
            'password'          => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $user) {
            if (array_key_exists('rut', $user->getAttributes())) {
                $rut = trim((string) ($user->rut ?? ''));
                $user->rut = $rut !== '' ? $rut : null;
            }

            if (array_key_exists('phone', $user->getAttributes())) {
                $phone = trim((string) ($user->phone ?? ''));
                $user->phone = $phone !== '' ? $phone : null;
            }

            $user->is_active = (bool) ($user->is_active ?? false);
        });
    }

    public function sendPasswordResetNotification($token): void
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        $logoPath = public_path('assets/img/logo_herradura.png');
        $logoDataUri = null;

        if (is_file($logoPath) && is_readable($logoPath)) {
            $mime = mime_content_type($logoPath) ?: 'image/png';
            $base64 = base64_encode(file_get_contents($logoPath));
            $logoDataUri = "data:{$mime};base64,{$base64}";
        }

        $this->notify(new class($url, $logoDataUri) extends \Illuminate\Notifications\Notification {
            public function __construct(
                public string $url,
                public ?string $logoDataUri
            ) {}

            public function via($notifiable): array
            {
                return ['mail'];
            }

            public function toMail($notifiable)
            {
                return (new \Illuminate\Notifications\Messages\MailMessage)
                    ->subject('Recuperación de contraseña · La Herradura')
                    ->view('emails.reset-password', [
                        'name'        => $notifiable->name,
                        'url'         => $this->url,
                        'logoDataUri' => $this->logoDataUri,
                    ]);
            }
        });
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function hasAnyRole(array $slugs): bool
    {
        return $this->roles()->whereIn('slug', $slugs)->exists();
    }

    public function assignRole($role): void
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole($role): void
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->first();
        }

        if ($role) {
            $this->roles()->detach($role->id);
        }
    }
}