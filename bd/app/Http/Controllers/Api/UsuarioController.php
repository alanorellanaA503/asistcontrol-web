<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UsuarioRequest;
use App\Http\Resources\UsuarioResource;
use App\Models\Administrador;
use App\Models\Estudiante;
use App\Models\Profesor;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    private const CONTRASENA_TEMPORAL = '123456789';

    public function index()
    {
        return UsuarioResource::collection(
            Usuario::with(['administrador', 'profesor', 'estudiante'])->orderBy('id_usuario')->paginate(15)
        );
    }

    public function store(UsuarioRequest $request): JsonResponse
    {
        $data = $request->validated();
        $contrasenaTemporal = null;

        if ($data['rol'] !== 'ADMINISTRADOR') {
            $contrasenaTemporal = self::CONTRASENA_TEMPORAL;
            $data['password'] = self::CONTRASENA_TEMPORAL;
        }

        $usuario = DB::transaction(function () use ($data) {
            $usuario = Usuario::create([
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'correo' => $data['correo'],
                'password_hash' => Hash::make($data['password']),
                'rol' => $data['rol'],
                'activo' => $data['activo'] ?? true,
                'debe_cambiar_password' => $data['rol'] !== 'ADMINISTRADOR',
            ]);

            $this->saveProfile($usuario, $data['rol'], $data['profile'] ?? []);

            return $usuario->refresh()->load(['administrador', 'profesor', 'estudiante']);
        });

        $response = (new UsuarioResource($usuario))->response()->setStatusCode(201);

        if ($contrasenaTemporal !== null) {
            $response->setData([
                'data' => (new UsuarioResource($usuario))->resolve($request),
                'contrasena_temporal' => $contrasenaTemporal,
            ]);
        }

        return $response;
    }

    public function show(Usuario $usuario): UsuarioResource
    {
        return new UsuarioResource($usuario->load(['administrador', 'profesor', 'estudiante']));
    }

    public function update(UsuarioRequest $request, Usuario $usuario): JsonResponse
    {
        $data = $request->validated();

        $contrasenaTemporal = null;
        $usuario = DB::transaction(function () use ($data, $usuario, &$contrasenaTemporal) {
            $newRole = $data['rol'] ?? $usuario->rol;
            $roleChanged = $newRole !== $usuario->rol;

            if ($roleChanged) {
                $this->assertRoleCanChange($usuario);
                if ($newRole === 'ESTUDIANTE' && empty($data['profile']['carnet'])) {
                    throw ValidationException::withMessages([
                        'profile.carnet' => ['El carnet es obligatorio al asignar el rol ESTUDIANTE.'],
                    ]);
                }
                $this->deleteProfile($usuario);
            }

            $changes = array_intersect_key($data, array_flip(['nombre', 'apellido', 'correo', 'activo']));
            $resetPassword = $newRole !== 'ADMINISTRADOR' && ($roleChanged || isset($data['password']));
            if ($resetPassword) {
                $contrasenaTemporal = self::CONTRASENA_TEMPORAL;
                $changes['password_hash'] = Hash::make(self::CONTRASENA_TEMPORAL);
                $changes['debe_cambiar_password'] = true;
                $usuario->tokens()->delete();
            } elseif (isset($data['password'])) {
                $changes['password_hash'] = Hash::make($data['password']);
                $usuario->tokens()->delete();
            }
            if ($roleChanged) {
                $changes['rol'] = $newRole;
            }
            if ($newRole === 'ADMINISTRADOR') {
                $changes['debe_cambiar_password'] = false;
            }
            $usuario->update($changes);

            if ($roleChanged) {
                $this->saveProfile($usuario, $newRole, $data['profile'] ?? []);
            } elseif (isset($data['profile'])) {
                $this->updateProfile($usuario, $data['profile']);
            }

            return $usuario->fresh()->load(['administrador', 'profesor', 'estudiante']);
        });

        $response = (new UsuarioResource($usuario))->response();

        if ($contrasenaTemporal !== null) {
            $response->setData([
                'data' => (new UsuarioResource($usuario))->resolve($request),
                'contrasena_temporal' => $contrasenaTemporal,
            ]);
        }

        return $response;
    }

    public function destroy(Usuario $usuario): JsonResponse
    {
        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente.']);
    }

    private function saveProfile(Usuario $usuario, string $role, array $profile): void
    {
        match ($role) {
            'ADMINISTRADOR' => Administrador::create([
                'id_usuario' => $usuario->id_usuario,
                'cargo' => $profile['cargo'] ?? null,
            ]),
            'PROFESOR' => Profesor::create([
                'id_usuario' => $usuario->id_usuario,
                'especialidad' => $profile['especialidad'] ?? null,
                'telefono' => $profile['telefono'] ?? null,
            ]),
            'ESTUDIANTE' => Estudiante::create([
                'id_usuario' => $usuario->id_usuario,
                'carnet' => $profile['carnet'],
                'grado' => $profile['grado'] ?? null,
                'fecha_nacimiento' => $profile['fecha_nacimiento'] ?? null,
            ]),
        };
    }

    private function updateProfile(Usuario $usuario, array $profile): void
    {
        $model = match ($usuario->rol) {
            'ADMINISTRADOR' => $usuario->administrador,
            'PROFESOR' => $usuario->profesor,
            'ESTUDIANTE' => $usuario->estudiante,
        };

        if ($model) {
            $model->update($profile);
        } else {
            $this->saveProfile($usuario, $usuario->rol, $profile);
        }
    }

    private function deleteProfile(Usuario $usuario): void
    {
        match ($usuario->rol) {
            'ADMINISTRADOR' => $usuario->administrador?->delete(),
            'PROFESOR' => $usuario->profesor?->delete(),
            'ESTUDIANTE' => $usuario->estudiante?->delete(),
        };
    }

    private function assertRoleCanChange(Usuario $usuario): void
    {
        $hasLinkedData = match ($usuario->rol) {
            'ADMINISTRADOR' => $usuario->administrador?->justificacionesRevisadas()->exists() ?? false,
            'PROFESOR' => ($usuario->profesor?->cursos()->exists() ?? false)
                || ($usuario->profesor?->asistencias()->exists() ?? false),
            'ESTUDIANTE' => $usuario->estudiante?->matriculas()->exists() ?? false,
        };

        if ($hasLinkedData) {
            throw ValidationException::withMessages([
                'rol' => ['No se puede cambiar el rol porque el perfil tiene datos académicos asociados.'],
            ]);
        }
    }
}
