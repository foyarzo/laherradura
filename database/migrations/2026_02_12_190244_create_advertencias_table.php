<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertencias', function (Blueprint $table) {
            $table->id();

            // Usuario al que va dirigida (null = global)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Admin que la creó
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('titulo');
            $table->text('mensaje');

            // Nivel visual
            $table->enum('nivel', ['info', 'warning', 'danger', 'success'])
                  ->default('info');

            // Control de vigencia
            $table->datetime('starts_at')->nullable();
            $table->datetime('ends_at')->nullable();

            // Activa o no
            $table->boolean('activa')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertencias');
    }
};
