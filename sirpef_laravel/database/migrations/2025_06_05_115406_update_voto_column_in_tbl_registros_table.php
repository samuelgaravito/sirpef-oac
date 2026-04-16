<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_registros', function (Blueprint $table) {
            // Cambiar el campo voto para permitir valores nulos
            $table->boolean('voto')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_registros', function (Blueprint $table) {
            // Revertir el cambio (hacer el campo no nullable nuevamente)
            // Nota: Esto establecerá valores nulos existentes a false
            $table->boolean('voto')->nullable(false)->default(false)->change();
        });
    }
};