<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTelefono2AndCausaPensionToTblPersonas extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_personas', function (Blueprint $table) {
            $table->bigInteger('telefono_2')->nullable(); // Agregar el campo telefono_2
            $table->string('causa_pension')->nullable(); // Agregar el campo causa_pension
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_personas', function (Blueprint $table) {
            $table->dropColumn('telefono_2'); // Eliminar el campo telefono_2
            $table->dropColumn('causa_pension'); // Eliminar el campo causa_pension
        });
    }
}