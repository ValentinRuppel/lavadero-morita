<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicioLavado extends Model
{
    use HasFactory;

    protected $fillable = [
        'box_id',
        'vehiculo_id',
        'tipo_lavado_id',
        'administrador_id',
        'fecha_hora_inicio',
        'fecha_hora_fin',
        'precio_total_servicio',
        'estado_servicio',
    ];

    protected $casts = [
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_fin' => 'datetime',
    ];

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function tipoLavado(): BelongsTo
    {
        return $this->belongsTo(TipoLavado::class);
    }

    /**
     * Un ServicioLavado es asignado por un Administrador.
     * Ahora apuntamos al modelo Administrator.
     */
    public function administrador(): BelongsTo
    {
        return $this->belongsTo(Administrator::class, 'administrador_id'); // ¡CAMBIO AQUÍ!
    }
}