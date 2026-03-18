<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Crear roles base del sistema
     */
    public static function createDefaultRoles(): void
    {
        self::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrador',
                'description' => 'Acceso total al sistema'
            ]
        );

        self::firstOrCreate(
            ['slug' => 'operador'],
            [
                'name' => 'Operador',
                'description' => 'Acceso operativo limitado'
            ]
        );
    }
}
