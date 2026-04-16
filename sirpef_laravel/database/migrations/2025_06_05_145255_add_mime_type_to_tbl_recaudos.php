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
        Schema::table('tbl_recaudos', function (Blueprint $table) {
            // Agregar columna para tipo MIME (100 caracteres suficientes para cualquier tipo)
            $table->string('mime_type', 100)
                  ->nullable() // Opcional: puede ser null si lo prefieres
                  ->after('path'); // Colocación después del campo path
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_recaudos', function (Blueprint $table) {
            $table->dropColumn('mime_type');
        });
    }
};