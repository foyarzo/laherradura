<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('estado', 40)
                  ->default('pendiente_aprobacion')
                  ->index();

            $table->unsignedBigInteger('total')
                  ->default(0);

            // Cliente propone
            $table->string('punto_encuentro')->nullable();
            $table->dateTime('hora_estimada_cliente')->nullable();
            $table->text('mensaje_cliente')->nullable();

            // Admin confirma / modifica
            $table->string('punto_encuentro_confirmado')->nullable();
            $table->dateTime('hora_estimada_confirmada')->nullable();
            $table->text('mensaje_admin')->nullable();

            $table->foreignId('aprobado_por')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->dateTime('aprobado_en')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};