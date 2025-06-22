<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Box extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_box',
        'descripcion',
        'estado',
    ];

    /**
     * Un Box puede tener muchos ServiciosLavado.
     */
    public function serviciosLavado(): HasMany
    {
        return $this->hasMany(ServicioLavado::class);
    }
}