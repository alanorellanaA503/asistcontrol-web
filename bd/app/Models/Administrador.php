<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Administrador extends Model
{
    protected $table = 'administrador';
    protected $primaryKey = 'id_admin';
    public $timestamps = false;
    protected $fillable = ['id_usuario', 'cargo'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function justificacionesRevisadas(): HasMany
    {
        return $this->hasMany(Justificacion::class, 'id_admin', 'id_admin');
    }
}
