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
        Schema::create('tbl_seguimiento', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('registro_id');
            $table->foreign('registro_id')->references('id')->on('tbl_registros')->onDelete('cascade');
            $table->unsignedBigInteger('estatus_tramite_id');
            $table->foreign('estatus_tramite_id')->references('id')->on('tbl_estatus_tramite')->onDelete('restrict'); 
            $table->text('observacion')->nullable();
 
            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_seguimiento');
    }
};