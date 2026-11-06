<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    protected $table = 'estudiante';
    protected $primaryKey = 'id_estudiante';
    public $timestamps = false;
    protected $fillable = ['id_usuario', 'carnet', 'grado', 'fecha_nacimiento'];

    protected function casts(): array
    {
        return ['fecha_nacimiento' => 'date'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class, 'id_estudiante', 'id_estudiante');
    }
}
