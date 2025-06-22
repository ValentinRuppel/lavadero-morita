<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoLavado extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_lavado',
        'descripcion',
        'precio',
        'duracion_estimada',
    ];

    /**
     * Un TipoLavado puede ser usado en muchos ServiciosLavado.
     */
    public function serviciosLavado(): HasMany
    {
        return $this->hasMany(ServicioLavado::class);
    }
}