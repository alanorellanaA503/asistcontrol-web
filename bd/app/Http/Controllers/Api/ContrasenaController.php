<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CambiarPasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ContrasenaController extends Controller
{
    public function update(CambiarPasswordRequest $request): JsonResponse
    {
        $usuario = $request->user();
        $tokenActual = $usuario->currentAccessToken();

        DB::transaction(function () use ($usuario, $request, $tokenActual): void {
            $usuario->update([
                'password_hash' => Hash::make($request->validated('password')),
                'debe_cambiar_password' => false,
            ]);

            if ($tokenActual && method_exists($tokenActual, 'getKey')) {
                $usuario->tokens()->where('id', '!=', $tokenActual->getKey())->delete();
            }
        });

        return response()->json(['message' => 'La contraseña se cambió correctamente.']);
    }
}
