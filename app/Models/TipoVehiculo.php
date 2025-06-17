<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    use HasFactory;

    protected $table = 'tipos_vehiculos';

    protected $fillable = [
        'nombre',
        'precio',
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'tipos_vehiculo_id');
    }
}