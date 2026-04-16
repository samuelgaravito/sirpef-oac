<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPersonaAutorizadaIdToTbEventoPersonaV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tb_evento_persona', function (Blueprint $table) {
            // Agregar campos para las imágenes
            $table->string('imagen1')->nullable();
            $table->string('imagen2')->nullable();
            $table->string('imagen3')->nullable();
            
            // Agregar el campo estatus como ENUM
            $table->enum('estatus', ['pendiente', 'activo', 'rechazado'])->nullable();
            
            // Agregar el campo para la referencia a la persona autorizada
            $table->unsignedBigInteger('persona_autorizada_id')->nullable();

            // Definir la clave foránea
            $table->foreign('persona_autorizada_id')
                  ->references('id')
                  ->on('tb_personas_autorizadas')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tb_evento_persona', function (Blueprint $table) {
            // Eliminar la clave foránea antes de eliminar la columna
            $table->dropForeign(['persona_autorizada_id']);
            $table->dropColumn('persona_autorizada_id');

            // Eliminar los campos agregados en la migración
            $table->dropColumn(['imagen1', 'imagen2', 'imagen3', 'estatus']);
        });
    }
}