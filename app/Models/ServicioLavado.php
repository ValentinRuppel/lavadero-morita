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

    /**
     * Un ServicioLavado pertenece a un Box.
     */
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    /**
     * Un ServicioLavado pertenece a un Vehiculo.
     * Asegúrate de que el modelo Vehiculo exista.
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class); // Asegúrate de tener este modelo
    }

    /**
     * Un ServicioLavado pertenece a un TipoLavado.
     */
    public function tipoLavado(): BelongsTo
    {
        return $this->belongsTo(TipoLavado::class);
    }

    /**
     * Un ServicioLavado es asignado por un Administrador (User).
     * Asegúrate de que el modelo User exista y represente a tus administradores.
     */
    public function administrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administrador_id'); // Usa 'User' si tus admins están ahí, o el modelo que uses
    }
}