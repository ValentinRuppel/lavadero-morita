<?php

namespace App\Http\Controllers;

use App\Models\ServicioLavado;
use App\Models\Box;
use App\Models\Vehiculo; // Necesario si vamos a crear o buscar vehículos
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB; // Para transacciones de base de datos
use Illuminate\Support\Facades\Log;

class ServicioLavadoController extends Controller
{
    /**
     * Iniciar un nuevo servicio de lavado.
     */
    public function store(Request $request)
    {
        // Validación de los datos recibidos
        $validated = $request->validate([
            'box_id' => 'required|exists:boxes,id',
            'vehiculo_patente' => 'required|string|max:20', // Patente del vehículo
            'vehiculo_marca' => 'required_with:vehiculo_modelo|nullable|string|max:255', // Marca (si se crea nuevo)
            'vehiculo_modelo' => 'required_with:vehiculo_marca|nullable|string|max:255', // Modelo (si se crea nuevo)
            'tipo_lavado_id' => 'required|exists:tipo_lavados,id',
            'administrador_id' => 'required|exists:users,id', // El ID del usuario logueado
        ]);

        // Iniciar una transacción de base de datos
        // Esto asegura que o todo se guarda correctamente, o nada se guarda
        DB::beginTransaction();

        try {
            $box = Box::findOrFail($validated['box_id']);

            // Verificar si el box ya está ocupado
            if ($box->estado === 'ocupado') {
                DB::rollBack();
                return redirect()->back()
                                 ->with('error', 'El box ya está ocupado y no se puede iniciar un nuevo servicio.');
            }

            // Buscar o crear el vehículo
            $vehiculo = Vehiculo::firstOrCreate(
                ['patente' => strtoupper($validated['vehiculo_patente'])], // Convierte a mayúsculas para consistencia
                [
                    'marca' => $validated['vehiculo_marca'] ?? 'Desconocida', // Valor por defecto si no se proporciona
                    'modelo' => $validated['vehiculo_modelo'] ?? 'Desconocido',
                ]
            );

            // Crear el nuevo servicio de lavado
            ServicioLavado::create([
                'box_id' => $box->id,
                'vehiculo_id' => $vehiculo->id,
                'tipo_lavado_id' => $validated['tipo_lavado_id'],
                'administrador_id' => $validated['administrador_id'],
                'fecha_hora_inicio' => now(), // La hora actual del servidor
                'estado_servicio' => 'en_curso', // Estado inicial
            ]);

            // Cambiar el estado del box a 'ocupado'
            $box->update(['estado' => 'ocupado']);

            DB::commit(); // Confirmar la transacción

            return redirect()->route('boxes.show', $box->id)
                             ->with('success', 'Servicio iniciado exitosamente. Box ahora ocupado.');

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción si algo sale mal
            // Logear el error para depuración
            Log::error('Error al iniciar servicio de lavado: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                             ->with('error', 'Hubo un error al iniciar el servicio: ' . $e->getMessage());
        }
    }

    /**
     * Finalizar un servicio de lavado.
     */
    public function update(Request $request, ServicioLavado $servicioLavado)
    {
        // Validación
        $validated = $request->validate([
            'precio_total_servicio' => 'nullable|numeric|min:0', // Precio final del servicio
            'observaciones' => 'nullable|string|max:1000', // Campo opcional para observaciones
        ]);

        // Iniciar una transacción
        DB::beginTransaction();

        try {
            // Solo se puede finalizar un servicio 'en_curso'
            if ($servicioLavado->estado_servicio !== 'en_curso') {
                DB::rollBack();
                return redirect()->back()
                                 ->with('error', 'El servicio no se puede finalizar porque no está en curso.');
            }

            // Si no se proporcionó un precio_total_servicio, lo calculamos del tipo de lavado
            if (!isset($validated['precio_total_servicio'])) {
                // Asegúrate de que TipoLavado esté cargado o cárgalo
                $tipoLavado = $servicioLavado->tipoLavado; // Esto asume que tienes la relación en ServicioLavado Model
                $validated['precio_total_servicio'] = $tipoLavado->precio;
            }

            // Actualizar el servicio
            $servicioLavado->update([
                'fecha_hora_fin' => now(), // La hora actual del servidor
                'precio_total_servicio' => $validated['precio_total_servicio'],
                'estado_servicio' => 'finalizado',
                // 'observaciones' => $validated['observaciones'] ?? null, // Si agregas observaciones a la tabla
            ]);

            // Cambiar el estado del box a 'activo' (o 'limpieza' si lo reintroduces)
            $box = $servicioLavado->box; // Esto asume la relación en ServicioLavado Model
            $box->update(['estado' => 'activo']);

            DB::commit(); // Confirmar la transacción

            return redirect()->route('boxes.show', $box->id)
                             ->with('success', 'Servicio finalizado exitosamente. Box ahora activo.');

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción
            Log::error('Error al finalizar servicio de lavado: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()
                             ->with('error', 'Hubo un error al finalizar el servicio: ' . $e->getMessage());
        }
    }

    // Opcional: Si quieres una forma de cancelar un servicio
    public function cancel(ServicioLavado $servicioLavado)
    {
        DB::beginTransaction();
        try {
            if ($servicioLavado->estado_servicio !== 'en_curso') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Solo se pueden cancelar servicios en curso.');
            }

            $servicioLavado->update(['estado_servicio' => 'cancelado', 'fecha_hora_fin' => now()]);
            $servicioLavado->box->update(['estado' => 'activo']); // El box vuelve a estar activo

            DB::commit();
            return redirect()->route('boxes.show', $servicioLavado->box_id)
                             ->with('success', 'Servicio cancelado. Box ahora activo.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cancelar servicio de lavado: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Hubo un error al cancelar el servicio: ' . $e->getMessage());
        }
    }
}