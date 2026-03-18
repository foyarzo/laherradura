<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('pedidos')
            ->where('estado', 'pendiente_aprobacion')
            ->update(['estado' => 'pendiente']);
    }

    public function down(): void
    {
        DB::table('pedidos')
            ->where('estado', 'pendiente')
            ->update(['estado' => 'pendiente_aprobacion']);
    }
};