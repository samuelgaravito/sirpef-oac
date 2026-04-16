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
        Schema::create('tb_evento_persona', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evento_id');
            $table->unsignedBigInteger('persona_id');
            $table->integer('cantidad')->nullable();
            $table->timestamps();
            $table->softDeletes();
        
            // Definir la llave foránea que referencia a la tabla tb_evento
            $table->foreign('evento_id')->references('id')->on('tb_evento')->onDelete('set null');
        
            // Definir la llave foránea que referencia a la tabla tbl_personas
            $table->foreign('persona_id')->references('id')->on('tbl_personas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_evento_persona');
    }
};
