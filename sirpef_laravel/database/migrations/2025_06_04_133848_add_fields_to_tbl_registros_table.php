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
            // Agregar columna para observación (texto largo, nullable)
            $table->string('observacion')->nullable();
            
            // Agregar columna para referencia (string, nullable)
            $table->string('referencia')->nullable();
            
            // Agregar columna para el tipo de caso (foreign key)
            $table->unsignedBigInteger('id_tipo_caso')->nullable();
            
            // Crear la relación con tbl_tipo_caso
            $table->foreign('id_tipo_caso')
                  ->references('id')
                  ->on('tbl_tipo_caso')
                  ->onDelete('set null'); // O usar 'cascade' según tu necesidad

            
            $table->unsignedBigInteger('punto_cuenta_id')->nullable();
            
            // Crear la relación con tbl_punto_cuenta
            $table->foreign('punto_cuenta_id')
                  ->references('id')
                  ->on('tbl_punto_cuenta')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_registros', function (Blueprint $table) {
            // Eliminar la llave foránea primero
            $table->dropForeign(['id_tipo_caso']);
            
            // Eliminar las columnas agregadas
            $table->dropColumn(['observacion', 'referencia', 'id_tipo_caso']);

                // Eliminar la llave foránea primero
            $table->dropForeign(['punto_cuenta_id']);
            
            // Eliminar la columna
            $table->dropColumn('punto_cuenta_id');
        });
    }
};