// database/migrations/YYYY_MM_DD_HHMMSS_add_specific_fields_to_punto_cuenta_table.php

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
        Schema::table('tbl_punto_cuenta', function (Blueprint $table) {
            // Existing fields
            $table->string('cargo_a')->nullable()->after('otras_instrucciones');
            $table->string('cargo_por')->nullable()->after('cargo_a');
            $table->string('resolucion_1')->nullable()->after('cargo_por');
            $table->string('resolucion_2')->nullable()->after('resolucion_1');
            $table->boolean('estatus_pc')->after('resolucion_2');
            $table->unsignedInteger('cantidad_editada')->default(0)->after('estatus_pc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_punto_cuenta', function (Blueprint $table) {
            // Se eliminan las columnas en el orden inverso a su creación
            $table->dropColumn([
                'cantidad_editada',
                'estatus_pc',
                'resolucion_2',
                'resolucion_1',
                'cargo_por',
                'cargo_a'
            ]);
        });
    }
};