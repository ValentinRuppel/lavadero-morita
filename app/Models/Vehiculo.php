<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipo_vehiculo_id',
        'modelo_id',
        'marca',
        'modelo',
        'patente',
        'anio',
    ];

    /**
     * Get the user that owns the Vehiculo.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tipoVehiculo that owns the Vehiculo.
     */
    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class);
    }

    /**
     * Get the modelo that owns the Vehiculo.  <-- ¡Añade esta relación!
     */
    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }

    // Si también quieres acceder directamente a la marca desde el vehículo
    // public function marca()
    // {
    //     return $this->hasOneThrough(Marca::class, Modelo::class);
    // }
}