<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'marca',
        'modelo',
        'anio',
        'patente',
        'color',
        'tipos_vehiculo_id',
        'user_id',
    ];

    /**
     * Get the tipoVehiculo that owns the Vehiculo.
     */
    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class);
    }

    /**
     * Get the user (client) that owns the Vehiculo.
     */
    public function user() // Cambiado de 'cliente' a 'user'
    {
        return $this->belongsTo(User::class);
    }
}