<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Justificacion extends Model
{
    protected $table = 'justificacion';
    protected $primaryKey = 'id_justificacion';
    public $timestamps = false;
    protected $fillable = ['id_asistencia', 'id_admin', 'motivo', 'estado', 'creada_en'];

    protected function casts(): array
    {
        return ['creada_en' => 'datetime'];
    }

    public function asistencia(): BelongsTo
    {
        return $this->belongsTo(Asistencia::class, 'id_asistencia', 'id_asistencia');
    }

    public function administrador(): BelongsTo
    {
        return $this->belongsTo(Administrador::class, 'id_admin', 'id_admin');
    }
}
