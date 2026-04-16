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
        Schema::create('tbl_registros', function (Blueprint $table) {
            $table->id();
            $table->boolean('voto');
            $table->string('descripcion')->nullable();
            $table->time('hora_voto')->nullable();
            $table->unsignedBigInteger('evento_persona_id');
            $table->timestamps();
        
            // Definir la llave foránea que referencia a la tabla tb_evento_persona
            $table->foreign('evento_persona_id')->references('id')->on('tb_evento_persona')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros');
    }
};
