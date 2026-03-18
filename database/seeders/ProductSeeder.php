<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Aceite CBD 10%', 'price' => 19990, 'stock' => 30, 'sku' => 'CBD-ACE-10'],
            ['name' => 'Flores 1g (índica)', 'price' => 8990, 'stock' => 50, 'sku' => 'FLOR-IND-1G'],
            ['name' => 'Flores 1g (sativa)', 'price' => 8990, 'stock' => 40, 'sku' => 'FLOR-SAT-1G'],
            ['name' => 'Gomitas CBD', 'price' => 12990, 'stock' => 25, 'sku' => 'CBD-GOM-01'],
            ['name' => 'Crema tópica CBD', 'price' => 15990, 'stock' => 15, 'sku' => 'CBD-CRE-01'],
        ];

        foreach ($items as $i) {
            Product::updateOrCreate(
                ['sku' => $i['sku']],
                [
                    'name' => $i['name'],
                    'slug' => Str::slug($i['name']),
                    'description' => null,
                    'price' => $i['price'],
                    'stock' => $i['stock'],
                    'is_active' => true,
                ]
            );
        }
    }
}