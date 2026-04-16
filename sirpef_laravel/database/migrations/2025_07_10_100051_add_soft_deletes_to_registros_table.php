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
        Schema::table('tbl_registros', function (Blueprint $table) {
            // Adds the 'deleted_at' column for soft deletes
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_registros', function (Blueprint $table) {
            // Removes the 'deleted_at' column if rolling back
            $table->dropSoftDeletes();
        });
    }
};