<?php

use App\Http\Controllers\Api\AsistenciaController;
use App\Http\Controllers\Api\AutenticacionController;
use App\Http\Controllers\Api\CursoController;
use App\Http\Controllers\Api\ContrasenaController;
use App\Http\Controllers\Api\EstudianteController;
use App\Http\Controllers\Api\HorarioController;
use App\Http\Controllers\Api\JustificacionController;
use App\Http\Controllers\Api\MatriculaController;
use App\Http\Controllers\Api\MateriaController;
use App\Http\Controllers\Api\PeriodoController;
use App\Http\Controllers\Api\ProfesorController;
use App\Http\Controllers\Api\SesionClaseController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AutenticacionController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AutenticacionController::class, 'logout']);
    Route::get('/me', [AutenticacionController::class, 'me']);
    Route::put('/password', [ContrasenaController::class, 'update']);

    Route::middleware(['password.changed', 'role:ADMINISTRADOR'])->group(function () {
        Route::apiResource('usuarios', UsuarioController::class);
        Route::apiResource('ciclos', PeriodoController::class)->parameters(['ciclos' => 'periodo']);
        Route::apiResource('periodos', PeriodoController::class);
        Route::apiResource('materias', MateriaController::class);
        Route::apiResource('cursos', CursoController::class);
        Route::apiResource('horarios', HorarioController::class);
        Route::get('/profesores', [ProfesorController::class, 'index']);

        Route::post('/matriculas', [MatriculaController::class, 'store']);
        Route::patch('/matriculas/{matricula}/retirar', [MatriculaController::class, 'retirar']);
        Route::get('/justificaciones', [JustificacionController::class, 'index']);
        Route::patch('/justificaciones/{justificacion}/revisar', [JustificacionController::class, 'revisar']);

        Route::prefix('admin')->group(function () {
            Route::get('/cursos', [ProfesorController::class, 'cursos']);
            Route::get('/cursos/{curso}/estudiantes', [ProfesorController::class, 'estudiantes']);
            Route::post('/cursos/{curso}/sesiones', [SesionClaseController::class, 'store']);
            Route::post('/sesiones/{sesion}/asistencias', [SesionClaseController::class, 'registrarAsistencias']);
            Route::put('/asistencias/{asistencia}', [AsistenciaController::class, 'update']);
        });
    });

    Route::prefix('profesor')->middleware(['password.changed', 'role:PROFESOR'])->group(function () {
        Route::get('/cursos', [ProfesorController::class, 'cursos']);
        Route::get('/cursos/{curso}/estudiantes', [ProfesorController::class, 'estudiantes']);
        Route::post('/cursos/{curso}/sesiones', [SesionClaseController::class, 'store']);
        Route::post('/sesiones/{sesion}/asistencias', [SesionClaseController::class, 'registrarAsistencias']);
        Route::put('/asistencias/{asistencia}', [AsistenciaController::class, 'update']);
    });

    Route::prefix('estudiante')->middleware(['password.changed', 'role:ESTUDIANTE'])->group(function () {
        Route::get('/cursos', [EstudianteController::class, 'cursos']);
        Route::get('/asistencias', [EstudianteController::class, 'asistencias']);
        Route::get('/cursos/{curso}/porcentaje', [EstudianteController::class, 'porcentaje']);
        Route::post('/asistencias/{asistencia}/justificacion', [EstudianteController::class, 'justificar']);
    });
});
