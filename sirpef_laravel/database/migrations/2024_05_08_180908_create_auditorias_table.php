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
        Schema::create('tbl_auditorias', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion');
            $table->unsignedBigInteger('evento_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('persona_id')->nullable();

            $table->timestamps();
        
            // Definir la llave foránea que referencia a la tabla tb_evento
            $table->foreign('evento_id')->references('id')->on('tb_evento')->onDelete('set null');
        
            // Definir la llave foránea que referencia a la tabla users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Definir la llave foránea que referencia a la tabla persona
            $table->foreign('persona_id')->references('id')->on('tbl_personas')->onDelete('set null');
        
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
