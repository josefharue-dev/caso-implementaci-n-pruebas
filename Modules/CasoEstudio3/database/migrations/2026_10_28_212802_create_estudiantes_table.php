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
        Schema::create('estudiantes', function (Blueprint $table) {
        $table->string('carne')->primary();
        $table->string('nombre');
        $table->string('apellidos');
        $table->integer('codEmpleadoConsejero');
        $table->foreign('codEmpleadoConsejero')
          ->references('codEmpleado')
          ->on('docentes');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
