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
        Schema::create('tbl_municipio', function (Blueprint $table) {
            $table->id();
            $table->string('municipio');
            $table->decimal('coordenadas_x')->nullable();       
            $table->decimal('coordenadas_y')->nullable();          
            $table->unsignedBigInteger('estado_id');
            $table->timestamps();
        
            // Definir la llave foránea que referencia a la tabla tbl_estados
            $table->foreign('estado_id')->references('id')->on('tbl_estados')->onDelete('set null');
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
