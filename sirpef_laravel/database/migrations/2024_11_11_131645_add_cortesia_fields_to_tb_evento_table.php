<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCortesiaFieldsToTbEventoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_evento', function (Blueprint $table) {
            $table->integer('cortesia')->default(0); // Agregar el campo cortesia
            $table->integer('cortesia_entregada')->default(0); // Agregar el campo cortesia_entregada
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_evento', function (Blueprint $table) {
            $table->dropColumn(['cortesia', 'cort esia_entregada']); // Eliminar los campos en caso de rollback
        });
    }
}