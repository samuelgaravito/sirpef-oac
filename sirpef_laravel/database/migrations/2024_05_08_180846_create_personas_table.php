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
        Schema::create('tbl_personas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->integer('cedula');
            $table->string('direccion')->nullable();
            $table->string('cargo')->nullable();//nuevo
            $table->bigInteger('telefono')->nullable();
            $table->date('fecha_nacimiento')->nullable();//nuevo
            $table->string('sexo')->nullable();//nuevo
            $table->string('correo_electronico')->nullable();//nuevo
            $table->enum('estatus', ['activo', 'egresado', 'suspendido'])->default('activo');
            
            $table->unsignedBigInteger('parroquia_id');
            $table->unsignedBigInteger('centro_id')->nullable();;
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('ministerio_id')->nullable(); // Agrega la columna ministerio_id
        
            $table->timestamps();
            $table->softDeletes(); // Add this line to enable soft deletes
        
            // Definir la llave foránea que referencia a la tabla tbl_estados
            $table->foreign('parroquia_id')->references('id')->on('tbl_parroquias')->onDelete('set null');
          
            $table->foreign('centro_id')->references('id')->on('tbl_centros')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('ministerio_id')->references('id')->on('tb_ministerio')->onDelete('set null'); // Agrega la llave foránea
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
