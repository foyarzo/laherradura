<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Crear roles base
        |--------------------------------------------------------------------------
        */
        Role::createDefaultRoles();

        $adminRole = Role::where('slug', 'admin')->first();
        $operadorRole = Role::where('slug', 'operador')->first();

        /*
        |--------------------------------------------------------------------------
        | Usuario Admin
        |--------------------------------------------------------------------------
        */
        $admin = User::updateOrCreate(
            ['email' => 'admin@laherradura.cl'],
            [
                'name' => 'Administrador',
                'password' => 'password',
            ]
        );

        $admin->roles()->sync([$adminRole->id]);

        /*
        |--------------------------------------------------------------------------
        | Usuario Operador
        |--------------------------------------------------------------------------
        */
        $operador = User::updateOrCreate(
            ['email' => 'operador@laherradura.cl'],
            [
                'name' => 'Operador',
                'password' => 'password',
            ]
        );

        $operador->roles()->sync([$operadorRole->id]);

        /*
        |--------------------------------------------------------------------------
        | Productos
        |--------------------------------------------------------------------------
        */
        $this->call(ProductSeeder::class);
    }
}