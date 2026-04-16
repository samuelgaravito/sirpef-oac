<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AddPadreIdToTblPersonas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::table('tbl_personas', function (Blueprint $table) {
            $table->unsignedBigInteger('autorizado_id')->nullable();
            $table->foreign('autorizado_id')->references('id')->on('tbl_personas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_personas', function (Blueprint $table) {
            $table->dropForeign(['autorizado_id']);
            $table->dropColumn('autorizado_id');
        });
    }
}