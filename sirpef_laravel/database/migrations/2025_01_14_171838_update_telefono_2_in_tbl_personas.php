<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTelefono2InTblPersonas extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_personas', function (Blueprint $table) {
            // Cambiar el campo telefono_2 para que sea nullable
            $table->string('telefono_2')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_personas', function (Blueprint $table) {
            // Si necesitas revertir el cambio, puedes hacerlo aquí
            $table->string('telefono_2')->nullable(false)->change();
        });
    }
}