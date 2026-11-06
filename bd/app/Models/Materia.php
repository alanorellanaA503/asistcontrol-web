<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    protected $table = 'materia';
    protected $primaryKey = 'id_materia';
    public $timestamps = false;
    protected $fillable = ['codigo', 'nombre'];

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'id_materia', 'id_materia');
    }
}
