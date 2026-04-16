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
        Schema::table('tbl_personas', function (Blueprint $table) {
            // Cambiar el tipo de la columna telefono a string
            $table->string('telefono')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_personas', function (Blueprint $table) {
            // Revertir el cambio, si es necesario
            $table->bigInteger('telefono')->nullable()->change();
        });
    }
};