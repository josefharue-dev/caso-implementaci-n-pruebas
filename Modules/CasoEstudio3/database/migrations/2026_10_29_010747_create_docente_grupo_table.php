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
    Schema::create('docente_grupo', function (Blueprint $table) {
        $table->foreignId('grupo_id')
              ->constrained('grupos')
              ->cascadeOnDelete();

        $table->integer('docente_codEmpleado');

        $table->foreign('docente_codEmpleado')
              ->references('codEmpleado')
              ->on('docentes')
              ->cascadeOnDelete();

        $table->primary(['grupo_id', 'docente_codEmpleado']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docente_grupo');
    }
};
