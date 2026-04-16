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
        Schema::create('tbl_punto_cuenta', function (Blueprint $table) {
            $table->id();
            $table->string('presentado_a');
            $table->string('presentado_por');
            $table->date('fecha');
            $table->string('numero_punto', 50);
            $table->text('asunto');
            $table->text('exposicion_motivos');
            $table->text('propuesta');
            $table->enum('decision', [
                'APROBADO', 
                'NEGADO', 
                'VISTO', 
                'DIFERIDO', 
                'OTRO'
            ])->default('VISTO');
            $table->text('otras_instrucciones')->nullable();
            $table->boolean('anexos')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_punto_cuenta');
    }
};