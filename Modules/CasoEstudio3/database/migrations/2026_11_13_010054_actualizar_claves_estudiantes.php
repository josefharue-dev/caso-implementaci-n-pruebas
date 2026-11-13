<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->renameColumn(
                'codEmpleadoConsejero',
                'consejero_id'
            );
        });

        Schema::table('estudiantes_matriculados', function (Blueprint $table) {
            $table->renameColumn(
                'estudiante_carne',
                'estudiante_id'
            );
        });

        Schema::table('estudiantes_matriculados', function (Blueprint $table) {
            $table->unique(
                ['grupo_id', 'estudiante_id'],
                'grupo_estudiante_unico'
            );
        });
    }

    public function down(): void
    {
        Schema::table('estudiantes_matriculados', function (Blueprint $table) {
            $table->dropUnique('grupo_estudiante_unico');
        });

        Schema::table('estudiantes_matriculados', function (Blueprint $table) {
            $table->renameColumn(
                'estudiante_id',
                'estudiante_carne'
            );
        });

        Schema::table('estudiantes', function (Blueprint $table) {
            $table->renameColumn(
                'consejero_id',
                'codEmpleadoConsejero'
            );
        });
    }
};

