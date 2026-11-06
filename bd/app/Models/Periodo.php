<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periodo extends Model
{
    protected $table = 'periodo';
    protected $primaryKey = 'id_periodo';
    public $timestamps = false;
    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin'];

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'id_periodo', 'id_periodo');
    }
}
