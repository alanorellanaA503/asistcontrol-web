<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Validation\Rule;

class UsuarioRequest extends ApiRequest
{
    public function rules(): array
    {
        $usuario = $this->route('usuario');
        $usuarioId = $usuario instanceof Usuario ? $usuario->getKey() : null;
        $estudianteId = $usuario instanceof Usuario ? $usuario->estudiante?->id_estudiante : null;
        $creating = $this->isMethod('post');

        return [
            'nombre' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'apellido' => [$creating ? 'required' : 'sometimes', 'string', 'max:255'],
            'correo' => [
                'bail',
                $creating ? 'required' : 'sometimes',
                'email',
                'max:255',
                Rule::unique('usuario', 'correo')->ignore($usuarioId, 'id_usuario'),
            ],
            'password' => [
                $creating && $this->input('rol') === 'ADMINISTRADOR' ? 'required' : 'sometimes',
                'string',
                'min:8',
            ],
            'rol' => [$creating ? 'required' : 'sometimes', Rule::in(['ADMINISTRADOR', 'PROFESOR', 'ESTUDIANTE'])],
            'activo' => ['sometimes', 'boolean'],
            'profile' => ['sometimes', 'array'],
            'profile.cargo' => ['nullable', 'string', 'max:255'],
            'profile.especialidad' => ['nullable', 'string', 'max:255'],
            'profile.telefono' => ['nullable', 'string', 'max:50'],
            'profile.carnet' => [
                'bail',
                Rule::requiredIf($creating && $this->input('rol') === 'ESTUDIANTE'),
                'nullable',
                'string',
                'max:255',
                Rule::unique('estudiante', 'carnet')->ignore($estudianteId, 'id_estudiante'),
            ],
            'profile.grado' => ['nullable', 'string', 'max:255'],
            'profile.fecha_nacimiento' => ['nullable', 'date'],
        ];
    }
}
