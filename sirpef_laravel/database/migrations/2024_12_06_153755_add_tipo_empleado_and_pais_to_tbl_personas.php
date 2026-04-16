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
        Schema::table('tbl_personas', function (Blueprint $table) {

            // Agregar las nuevas columnas
            $table->unsignedBigInteger('tipo_empleado_id')->nullable();
            $table->unsignedBigInteger('pais_id')->nullable();

            // Definir las llaves foráneas
            $table->foreign('tipo_empleado_id')->references('id')->on('tb_tipo_empleados')->onDelete('set null');
            $table->foreign('pais_id')->references('id')->on('tb_paises')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_personas', function (Blueprint $table) {
            //
        });
    }
};
