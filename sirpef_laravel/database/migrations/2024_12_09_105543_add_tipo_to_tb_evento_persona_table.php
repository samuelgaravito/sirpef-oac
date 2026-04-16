<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoToTbEventoPersonaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_evento_persona', function (Blueprint $table) {
            $table->string('tipo')->nullable(); // Agregar el nuevo campo 'tipo'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_evento_persona', function (Blueprint $table) {
            $table->dropColumn('tipo'); // Eliminar el campo 'tipo' si se revierte la migración
        });
    }
}