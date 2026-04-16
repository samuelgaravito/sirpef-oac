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
            // Añade la columna 'tipo_caso_padre_id'
            // Será una clave foránea que referencia a la misma tabla 'tbl_tipo_caso'
            // y puede ser nula (para los tipos de caso raíz).
            $table->foreignId('tipo_caso_padre_id')
                  ->nullable() // Permite que sea nulo (es decir, es un tipo de caso raíz)
                  ->constrained('tbl_tipo_caso') // La tabla a la que hace referencia (ella misma)
                  ->onDelete('set null'); // Si el padre se elimina, establece este campo a null

            // Opcional: Si quieres que aparezca después de 'id' o 'tipo', puedes usar ->after('id') o ->after('tipo')
            // $table->foreignId('tipo_caso_padre_id')->nullable()->constrained('tbl_tipo_caso')->onDelete('set null')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_tipo_caso', function (Blueprint $table) {
            // Elimina la clave foránea primero
            $table->dropConstrainedForeignId('tipo_caso_padre_id'); // Elimina la clave foránea por convención de nombres de Laravel
            // Luego, elimina la columna
            $table->dropColumn('tipo_caso_padre_id');
        });
    }
};