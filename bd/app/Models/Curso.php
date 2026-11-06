<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    protected $table = 'curso';
    protected $primaryKey = 'id_curso';
    public $timestamps = false;
    protected $fillable = ['id_materia', 'id_profesor', 'id_periodo', 'grupo', 'aula'];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'id_profesor', 'id_profesor');
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class, 'id_periodo', 'id_periodo');
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'id_curso', 'id_curso');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class, 'id_curso', 'id_curso');
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(SesionClase::class, 'id_curso', 'id_curso');
    }
}
