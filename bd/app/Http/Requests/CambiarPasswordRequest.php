<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Validation\Validator;

class CambiarPasswordRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'password_actual' => ['required', 'string'],
            'password' => ['required', 'string', 'min:12', 'confirmed', 'different:password_actual'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $usuario = $this->user();
            $passwordActual = $this->input('password_actual');

            if (is_string($passwordActual) && ! Hash::check($passwordActual, $usuario->password_hash)) {
                $validator->errors()->add('password_actual', 'La contraseña actual no es correcta.');
            }
        }];
    }
}
