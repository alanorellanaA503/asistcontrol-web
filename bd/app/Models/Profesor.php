<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profesor extends Model
{
    protected $table = 'profesor';
    protected $primaryKey = 'id_profesor';
    public $timestamps = false;
    protected $fillable = ['id_usuario', 'especialidad', 'telefono'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'id_profesor', 'id_profesor');
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'id_profesor', 'id_profesor');
    }
}
