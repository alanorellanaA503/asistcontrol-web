<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AutenticacionController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $usuario = Usuario::where('correo', $request->validated('correo'))->first();

        if (! $usuario || ! $usuario->activo || ! Hash::check($request->validated('password'), $usuario->password_hash)) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        $token = $usuario->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'usuario' => new UsuarioResource($usuario->load(['administrador', 'profesor', 'estudiante'])),
        ]);
    }

    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    public function me(): UsuarioResource
    {
        return new UsuarioResource(request()->user()->load(['administrador', 'profesor', 'estudiante']));
    }
}
