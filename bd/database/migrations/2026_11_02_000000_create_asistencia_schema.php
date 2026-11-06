<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->bigIncrements('id_usuario');
            $table->string('nombre');
            $table->string('apellido');
            $table->string('correo')->unique();
            $table->string('password_hash');
            $table->enum('rol', ['ADMINISTRADOR', 'PROFESOR', 'ESTUDIANTE']);
            $table->boolean('activo')->default(true);
            $table->timestamp('creado_en')->useCurrent();
        });

        Schema::create('administrador', function (Blueprint $table) {
            $table->bigIncrements('id_admin');
            $table->unsignedBigInteger('id_usuario')->unique();
            $table->string('cargo')->nullable();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->cascadeOnDelete();
        });

        Schema::create('profesor', function (Blueprint $table) {
            $table->bigIncrements('id_profesor');
            $table->unsignedBigInteger('id_usuario')->unique();
            $table->string('especialidad')->nullable();
            $table->string('telefono')->nullable();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->cascadeOnDelete();
        });

        Schema::create('estudiante', function (Blueprint $table) {
            $table->bigIncrements('id_estudiante');
            $table->unsignedBigInteger('id_usuario')->unique();
            $table->string('carnet')->unique();
            $table->string('grado')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->cascadeOnDelete();
        });

        Schema::create('periodo', function (Blueprint $table) {
            $table->bigIncrements('id_periodo');
            $table->string('nombre');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
        });

        Schema::create('materia', function (Blueprint $table) {
            $table->bigIncrements('id_materia');
            $table->string('codigo')->unique();
            $table->string('nombre');
        });

        Schema::create('curso', function (Blueprint $table) {
            $table->bigIncrements('id_curso');
            $table->unsignedBigInteger('id_materia');
            $table->unsignedBigInteger('id_profesor');
            $table->unsignedBigInteger('id_periodo');
            $table->string('grupo');
            $table->string('aula')->nullable();
            $table->unique(['id_materia', 'id_periodo', 'grupo']);
            $table->foreign('id_materia')->references('id_materia')->on('materia')->cascadeOnDelete();
            $table->foreign('id_profesor')->references('id_profesor')->on('profesor')->cascadeOnDelete();
            $table->foreign('id_periodo')->references('id_periodo')->on('periodo')->cascadeOnDelete();
        });

        Schema::create('horario', function (Blueprint $table) {
            $table->bigIncrements('id_horario');
            $table->unsignedBigInteger('id_curso');
            $table->enum('dia_semana', ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO']);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->foreign('id_curso')->references('id_curso')->on('curso')->cascadeOnDelete();
        });

        Schema::create('matricula', function (Blueprint $table) {
            $table->bigIncrements('id_matricula');
            $table->unsignedBigInteger('id_estudiante');
            $table->unsignedBigInteger('id_curso');
            $table->enum('estado', ['ACTIVA', 'RETIRADA'])->default('ACTIVA');
            $table->unique(['id_estudiante', 'id_curso']);
            $table->foreign('id_estudiante')->references('id_estudiante')->on('estudiante')->cascadeOnDelete();
            $table->foreign('id_curso')->references('id_curso')->on('curso')->cascadeOnDelete();
        });

        Schema::create('sesion_clase', function (Blueprint $table) {
            $table->bigIncrements('id_sesion');
            $table->unsignedBigInteger('id_curso');
            $table->date('fecha');
            $table->string('tema')->nullable();
            $table->unique(['id_curso', 'fecha']);
            $table->foreign('id_curso')->references('id_curso')->on('curso')->cascadeOnDelete();
        });

        Schema::create('asistencia', function (Blueprint $table) {
            $table->bigIncrements('id_asistencia');
            $table->unsignedBigInteger('id_sesion');
            $table->unsignedBigInteger('id_matricula');
            $table->unsignedBigInteger('id_profesor');
            $table->enum('estado', ['PRESENTE', 'AUSENTE', 'TARDE', 'JUSTIFICADO']);
            $table->time('hora_registro')->nullable();
            $table->text('observacion')->nullable();
            $table->unique(['id_sesion', 'id_matricula']);
            $table->foreign('id_sesion')->references('id_sesion')->on('sesion_clase')->cascadeOnDelete();
            $table->foreign('id_matricula')->references('id_matricula')->on('matricula')->cascadeOnDelete();
            $table->foreign('id_profesor')->references('id_profesor')->on('profesor')->cascadeOnDelete();
        });

        Schema::create('justificacion', function (Blueprint $table) {
            $table->bigIncrements('id_justificacion');
            $table->unsignedBigInteger('id_asistencia')->unique();
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->text('motivo');
            $table->enum('estado', ['PENDIENTE', 'APROBADA', 'RECHAZADA'])->default('PENDIENTE');
            $table->timestamp('creada_en')->useCurrent();
            $table->foreign('id_asistencia')->references('id_asistencia')->on('asistencia')->cascadeOnDelete();
            $table->foreign('id_admin')->references('id_admin')->on('administrador')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('justificacion');
        Schema::dropIfExists('asistencia');
        Schema::dropIfExists('sesion_clase');
        Schema::dropIfExists('matricula');
        Schema::dropIfExists('horario');
        Schema::dropIfExists('curso');
        Schema::dropIfExists('materia');
        Schema::dropIfExists('periodo');
        Schema::dropIfExists('estudiante');
        Schema::dropIfExists('profesor');
        Schema::dropIfExists('administrador');
        Schema::dropIfExists('usuario');
    }
};
