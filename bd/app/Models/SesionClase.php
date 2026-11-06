<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesionClase extends Model
{
    protected $table = 'sesion_clase';
    protected $primaryKey = 'id_sesion';
    public $timestamps = false;
    protected $fillable = ['id_curso', 'fecha', 'tema'];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'id_sesion', 'id_sesion');
    }
}
