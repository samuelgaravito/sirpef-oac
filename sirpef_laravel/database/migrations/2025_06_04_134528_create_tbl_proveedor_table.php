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
        Schema::create('tbl_proveedor', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('monto'); // 10 dígitos en total, 2 decimales
            $table->unsignedBigInteger('registro_id');
            $table->timestamps();

            // Definir la llave foránea que referencia a tbl_registros
            $table->foreign('registro_id')
                  ->references('id')
                  ->on('tbl_registros')
                  ->onDelete('cascade'); // Elimina el proveedor si se elimina el registro
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_proveedor');
    }
};