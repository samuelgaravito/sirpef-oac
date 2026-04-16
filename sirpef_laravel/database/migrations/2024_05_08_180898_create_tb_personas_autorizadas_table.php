<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbPersonasAutorizadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_personas_autorizadas', function (Blueprint $table) {
            $table->id(); 
            $table->string('nombre_completo');
            $table->string('cedula')->unique(); 
            $table->string('telefono'); 
            $table->enum('estatus', ['interno', 'externo', 'otro']);
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_personas_autorizadas');
    }
}