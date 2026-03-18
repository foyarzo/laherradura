<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PuntoEncuentro;

class PuntoEncuentroSeeder extends Seeder
{
    public function run(): void
    {
        $puntos = [
            ['name' => 'Plaza Central - Frente a la fuente', 'is_active' => true],
            ['name' => 'Mall Portal Temuco - Entrada principal', 'is_active' => true],
            ['name' => 'Copec Av. Alemania - Sector minimarket', 'is_active' => true],
            ['name' => 'Lider Prieto Norte - Estacionamiento sector A', 'is_active' => true],
            ['name' => 'Terminal Rodoviario - Hall principal', 'is_active' => true],
            ['name' => 'Hospital Regional - Acceso visitas', 'is_active' => true],
            ['name' => 'UFRO Campus Andrés Bello - Portería', 'is_active' => true],
            ['name' => 'Costanera Río Cautín - Mirador', 'is_active' => true],
        ];

        foreach ($puntos as $punto) {
            PuntoEncuentro::updateOrCreate(
                ['name' => $punto['name']],
                $punto
            );
        }
    }
}
