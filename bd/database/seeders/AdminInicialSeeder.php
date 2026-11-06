<?php

namespace Database\Seeders;

use App\Models\Administrador;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminInicialSeeder extends Seeder
{
    private const CORREO_ADMIN = 'administrador.inicial@asistcontrol.com';

    public function run(): void
    {
        $password = env('ADMIN_INITIAL_PASSWORD');

        if (! is_string($password) || strlen($password) < 12) {
            throw new RuntimeException('Define ADMIN_INITIAL_PASSWORD con al menos 12 caracteres antes de sembrar.');
        }

        $usuarios = Usuario::whereIn('correo', [self::CORREO_ADMIN, 'admin@asistcontrol.test'])->get();

        if ($usuarios->count() > 1) {
            throw new RuntimeException('Hay más de una cuenta para el administrador inicial.');
        }

        $usuario = $usuarios->first() ?? Usuario::create([
            'correo' => self::CORREO_ADMIN,
            'nombre' => 'Administrador',
            'apellido' => 'Inicial',
            'password_hash' => Hash::make($password),
            'rol' => 'ADMINISTRADOR',
            'activo' => true,
            'debe_cambiar_password' => false,
        ]);

        if ($usuario->rol !== 'ADMINISTRADOR') {
            throw new RuntimeException(self::CORREO_ADMIN.' ya existe con un rol diferente.');
        }

        if ($usuario->correo !== self::CORREO_ADMIN) {
            $usuario->update(['correo' => self::CORREO_ADMIN, 'debe_cambiar_password' => false]);
        } elseif ($usuario->debe_cambiar_password) {
            $usuario->update(['debe_cambiar_password' => false]);
        }

        Administrador::firstOrCreate(['id_usuario' => $usuario->id_usuario]);
    }
}
