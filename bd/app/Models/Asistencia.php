<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asistencia extends Model
{
    protected $table = 'asistencia';
    protected $primaryKey = 'id_asistencia';
    public $timestamps = false;
    protected $fillable = ['id_sesion', 'id_matricula', 'id_profesor', 'estado', 'hora_registro', 'observacion'];

    public function sesion(): BelongsTo
    {
        return $this->belongsTo(SesionClase::class, 'id_sesion', 'id_sesion');
    }

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class, 'id_matricula', 'id_matricula');
    }

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'id_profesor', 'id_profesor');
    }

    public function justificacion(): HasOne
    {
        return $this->hasOne(Justificacion::class, 'id_asistencia', 'id_asistencia');
    }
}
