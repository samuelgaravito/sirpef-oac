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
        Schema::table('tbl_tipo_caso', function (Blueprint $table) {
            $table->string('color')->nullable(); // Añade esta línea para el nuevo campo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_tipo_caso', function (Blueprint $table) {
            $table->dropColumn('color'); // Añade esta línea para revertir el cambio
        });
    }
};