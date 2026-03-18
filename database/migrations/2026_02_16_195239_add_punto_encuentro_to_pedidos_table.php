<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {

            // Punto elegido por cliente
            $table->foreignId('punto_encuentro_id')
                  ->nullable()
                  ->constrained('puntos_encuentro')
                  ->nullOnDelete();

            // Punto confirmado por admin (puede ser distinto)
            $table->foreignId('punto_encuentro_confirmado_id')
                  ->nullable()
                  ->constrained('puntos_encuentro')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['punto_encuentro_id']);
            $table->dropForeign(['punto_encuentro_confirmado_id']);
            $table->dropColumn([
                'punto_encuentro_id',
                'punto_encuentro_confirmado_id'
            ]);
        });
    }
};
