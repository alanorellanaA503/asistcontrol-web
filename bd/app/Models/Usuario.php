<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'usuario';

    protected $primaryKey = 'id_usuario';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellido',
        'correo',
        'password_hash',
        'rol',
        'activo',
        'debe_cambiar_password',
        'creado_en',
    ];

    protected $hidden = ['password_hash'];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'debe_cambiar_password' => 'boolean',
            'creado_en' => 'datetime',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function administrador(): HasOne
    {
        return $this->hasOne(Administrador::class, 'id_usuario', 'id_usuario');
    }

    public function profesor(): HasOne
    {
        return $this->hasOne(Profesor::class, 'id_usuario', 'id_usuario');
    }

    public function estudiante(): HasOne
    {
        return $this->hasOne(Estudiante::class, 'id_usuario', 'id_usuario');
    }
}
