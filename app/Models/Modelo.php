<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

    protected $fillable = [
        'marca_id',
        'nombre',
        'anio_inicio',
        'anio_fin',
        'tipo_vehiculo_id', // <-- ¡Añade esta línea!
    ];

    /**
     * Get the marca that owns the Modelo.
     */
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    /**
     * Get the vehiculos that belong to the Modelo.
     */
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }

    /**
     * Get the tipoVehiculo that owns the Modelo. <-- ¡Añade esta relación!
     */
    public function tipoVehiculo()
    {
        return $this->belongsTo(TipoVehiculo::class);
    }
}