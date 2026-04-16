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
    $table->enum('estatus_caso', [
        'Orientado', 
        'En Tramite', 
        'Resultado Directo', 
        'Remitido a Otro', 
        'Cerrado'
    ])->nullable(); // Primero permite null
    
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_registros', function (Blueprint $table) {
            $table->dropColumn('estatus_caso');
        });
    }
};