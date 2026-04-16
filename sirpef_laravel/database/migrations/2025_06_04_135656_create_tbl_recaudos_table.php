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
        Schema::create('tbl_recaudos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->string('path', 255); // Ruta del archivo
            $table->unsignedBigInteger('registro_id');
            $table->timestamps();

            // Llave foránea
            $table->foreign('registro_id')
                  ->references('id')
                  ->on('tbl_registros')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_recaudos');
    }
};