<?php

namespace Database\Seeders;

use App\Models\Administrador;
use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Horario;
use App\Models\Justificacion;
use App\Models\Matricula;
use App\Models\Materia;
use App\Models\Periodo;
use App\Models\Profesor;
use App\Models\SesionClase;
use App\Models\Usuario;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class DatosDemostracionSeeder extends Seeder
{
    private const CONTRASENA_TEMPORAL = '123456789';

    public function run(): void
    {
        DB::transaction(function (): void {
            $admin = Usuario::where('rol', 'ADMINISTRADOR')
                ->whereIn('correo', ['administrador.inicial@asistcontrol.com', 'admin@asistcontrol.test'])
                ->first()?->administrador;

            if (! $admin instanceof Administrador) {
                throw new RuntimeException('Debe existir el administrador inicial con su perfil.');
            }

            $profesorNombre = 'Profesor';
            $profesorApellido = 'Demostracion';
            $profesorUsuario = $this->updateDemoUsuario(
                'demo.profesor@asistcontrol.test',
                $this->correoPara($profesorNombre, $profesorApellido),
                [
                    'nombre' => $profesorNombre,
                    'apellido' => $profesorApellido,
                    'password_hash' => Hash::make(self::CONTRASENA_TEMPORAL),
                    'rol' => 'PROFESOR',
                    'activo' => true,
                    'debe_cambiar_password' => true,
                ]
            );
            $profesor = Profesor::updateOrCreate(
                ['id_usuario' => $profesorUsuario->id_usuario],
                ['especialidad' => 'Docencia general', 'telefono' => '5550000000']
            );

            $year = now()->year;
            $inicioPeriodo = CarbonImmutable::create($year, 1, 1)->toDateString();
            $finPeriodo = CarbonImmutable::create($year, 12, 31)->toDateString();
            $dias = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO'];

            for ($index = 1; $index <= 10; $index++) {
                $numero = str_pad((string) $index, 2, '0', STR_PAD_LEFT);
                $estudianteNombre = "Estudiante {$numero}";
                $estudianteApellido = 'Demostracion';
                $estudianteUsuario = $this->updateDemoUsuario(
                    "demo.estudiante{$numero}@asistcontrol.test",
                    $this->correoPara($estudianteNombre, $estudianteApellido),
                    [
                        'nombre' => $estudianteNombre,
                        'apellido' => $estudianteApellido,
                        'password_hash' => Hash::make(self::CONTRASENA_TEMPORAL),
                        'rol' => 'ESTUDIANTE',
                        'activo' => true,
                        'debe_cambiar_password' => true,
                    ]
                );
                $estudiante = Estudiante::updateOrCreate(
                    ['id_usuario' => $estudianteUsuario->id_usuario],
                    [
                        'carnet' => "DEMO-{$year}-{$numero}",
                        'grado' => 'Nivel de prueba',
                        'fecha_nacimiento' => CarbonImmutable::create($year - 20, $index, 1)->toDateString(),
                    ]
                );

                $periodo = Periodo::updateOrCreate(
                    ['nombre' => "Ciclo demostracion {$year}-{$numero}"],
                    ['fecha_inicio' => $inicioPeriodo, 'fecha_fin' => $finPeriodo]
                );
                $materia = Materia::updateOrCreate(
                    ['codigo' => "DEMO{$year}{$numero}"],
                    ['nombre' => "Materia de demostracion {$numero}"]
                );
                $curso = Curso::updateOrCreate(
                    [
                        'id_materia' => $materia->id_materia,
                        'id_periodo' => $periodo->id_periodo,
                        'grupo' => 'DEMO',
                    ],
                    [
                        'id_profesor' => $profesor->id_profesor,
                        'aula' => 'Aula de prueba',
                    ]
                );

                Horario::updateOrCreate(
                    ['id_curso' => $curso->id_curso, 'dia_semana' => $dias[($index - 1) % count($dias)]],
                    [
                        'hora_inicio' => sprintf('%02d:00:00', 8 + (($index - 1) % 8)),
                        'hora_fin' => sprintf('%02d:00:00', 9 + (($index - 1) % 8)),
                    ]
                );

                $matricula = Matricula::updateOrCreate(
                    ['id_estudiante' => $estudiante->id_estudiante, 'id_curso' => $curso->id_curso],
                    ['estado' => 'ACTIVA']
                );
                $fechaSesion = CarbonImmutable::create($year, 7, $index)->toDateString();
                $sesion = SesionClase::updateOrCreate(
                    ['id_curso' => $curso->id_curso, 'fecha' => $fechaSesion],
                    ['tema' => "Clase de demostracion {$numero}"]
                );

                $estadoJustificacion = $index <= 2
                    ? 'APROBADA'
                    : ($index <= 6 ? 'PENDIENTE' : 'RECHAZADA');
                $estadoAsistencia = $estadoJustificacion === 'APROBADA' ? 'JUSTIFICADO' : 'AUSENTE';
                $asistencia = Asistencia::updateOrCreate(
                    ['id_sesion' => $sesion->id_sesion, 'id_matricula' => $matricula->id_matricula],
                    [
                        'id_profesor' => $profesor->id_profesor,
                        'estado' => $estadoAsistencia,
                        'hora_registro' => '08:05:00',
                        'observacion' => 'Registro de demostracion',
                    ]
                );

                Justificacion::updateOrCreate(
                    ['id_asistencia' => $asistencia->id_asistencia],
                    [
                        'id_admin' => $estadoJustificacion === 'PENDIENTE' ? null : $admin->id_admin,
                        'motivo' => "Motivo de prueba {$numero}",
                        'estado' => $estadoJustificacion,
                    ]
                );
            }
        });
    }

    private function updateDemoUsuario(string $correoAnterior, string $correo, array $attributes): Usuario
    {
        $usuario = Usuario::whereIn('correo', [$correo, $correoAnterior])->first();

        if ($usuario && $usuario->rol !== $attributes['rol']) {
            throw new RuntimeException("{$correo} ya existe con un rol diferente.");
        }

        $usuario ??= new Usuario();
        $usuario->fill($attributes + ['correo' => $correo]);
        $usuario->save();

        return $usuario;
    }

    private function correoPara(string $nombre, string $apellido): string
    {
        $localPart = collect([$nombre, $apellido])
            ->map(fn (string $parte): string => Str::ascii(Str::lower(trim($parte))))
            ->map(fn (string $parte): string => trim((string) preg_replace('/[^a-z0-9]+/', '.', $parte), '.'))
            ->implode('.');

        return $localPart.'@asistcontrol.com';
    }
}
