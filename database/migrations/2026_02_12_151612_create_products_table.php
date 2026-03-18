<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name', 160);
            $table->string('slug', 180)->unique();

            $table->text('description')->nullable();

            // precio en CLP (entero)
            $table->unsignedInteger('price')->default(0);

            // stock simple
            $table->unsignedInteger('stock')->default(0);

            // SKU opcional
            $table->string('sku', 80)->nullable()->unique();

            // activo / inactivo
            $table->boolean('is_active')->default(true);

            // imagen simple (path)
            $table->string('image')->nullable();

            $table->timestamps();

            $table->index(['is_active']);
            $table->index(['slug']);
            $table->index(['name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};