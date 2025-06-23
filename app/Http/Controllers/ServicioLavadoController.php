<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\ServicioLavado;
use App\Models\Vehiculo;
use App\Models\TipoVehiculo;
use App\Models\TipoLavado;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth; // Asegúrate de importar Auth
class ServicioLavadoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        // 1. Determinar si el usuario logueado es un administrador
        $isAdmin = Auth::guard('admin')->check();
        $loggedInUser = Auth::user(); // Esto obtendrá el usuario logueado por el guard 'web' (cliente)

        $query = ServicioLavado::query();

        // 2. Cargar ansiosamente todas las relaciones necesarias para mostrar la información
        $query->with([
            'vehiculo.user',          // Para obtener el cliente asociado al vehículo
            'vehiculo.tipoVehiculo',  // Para el nombre del tipo de vehículo
            'vehiculo.modelo.marca',  // Para la marca y modelo detallados del vehículo
            'tipoLavado',             // Para el nombre del tipo de lavado
            'administrador'           // Para el nombre del administrador que realizó el servicio (si es visible para admin)
        ]);

        // 3. Filtrar servicios según el rol del usuario
        if (!$isAdmin) {
            // Si es un cliente regular, solo mostrar sus propios servicios
            // Se filtra por el user_id del vehículo asociado al servicio.
            if ($loggedInUser) { // Asegurarse de que haya un usuario cliente logueado
                $query->whereHas('vehiculo', function ($q) use ($loggedInUser) {
                    $q->where('user_id', $loggedInUser->id);
                });
            } else {
                // Si no hay un cliente logueado, redirigir o mostrar error
                return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus servicios.');
            }
        }

        // 4. Filtrar por servicios "finalizados" para el historial
        $query->whereIn('estado_servicio', ['finalizado', 'cancelado']); // Puedes ajustar esto si quieres incluir otros estados

        // 5. Ordenar por fecha de inicio (más recientes primero) y paginar
        $servicios = $query->orderBy('fecha_hora_inicio', 'desc')->paginate(10); // Ajusta la cantidad por página si lo deseas

        // 6. Mapear los datos para enviarlos a Inertia, controlando la visibilidad
        return Inertia::render('Servicios/Index', [
            'user' => $user,
            'servicios' => $servicios->through(fn ($servicio) => [
                'id' => $servicio->id,
                'fecha_inicio' => $servicio->fecha_hora_inicio->format('d/m/Y H:i'),
                'fecha_fin' => $servicio->fecha_hora_fin ? $servicio->fecha_hora_fin->format('d/m/Y H:i') : 'N/A', // Usar N/A si aún no finaliza
                'precio_total' => $servicio->precio_total_servicio,
                'estado' => $servicio->estado_servicio,

                // Información del vehículo (visible para ambos roles)
                'vehiculo' => [
                    'patente' => $servicio->vehiculo->patente,
                    'marca' => $servicio->vehiculo->modelo->marca->nombre ?? $servicio->vehiculo->marca, // Prioriza marca del modelo, sino la directa
                    'modelo' => $servicio->vehiculo->modelo->nombre ?? $servicio->vehiculo->modelo, // Prioriza modelo del modelo, sino el directo
                    'anio' => $servicio->vehiculo->anio,
                    'tipo_vehiculo_nombre' => $servicio->vehiculo->tipoVehiculo->nombre ?? 'N/A',
                ],
                // Información del tipo de lavado (visible para ambos roles)
                'tipo_lavado' => [
                    'nombre' => $servicio->tipoLavado->nombre_lavado,
                ],

                // Información específica para el ADMINISTRADOR
                'cliente' => $isAdmin && $servicio->vehiculo->user ? [ // Solo si es admin y hay un cliente asociado
                    'id' => $servicio->vehiculo->user->id,
                    'name' => $servicio->vehiculo->user->name,
                    'email' => $servicio->vehiculo->user->email,
                ] : null,
                'administrador_nombre' => $isAdmin && $servicio->administrador ? $servicio->administrador->name : null, // Solo si es admin y hay admin
            ]),
            'isAdmin' => $isAdmin, // Pasar esta variable al frontend para control de visibilidad
        ]);
    }
    /**
     * Iniciar un nuevo servicio de lavado.
     */
    public function store(Request $request)
    {
        Log::info('Intentando iniciar servicio - Datos recibidos:', $request->all());

        // 1. Definir las reglas de validación
        $rules = [
            'box_id' => ['required', 'exists:boxes,id'],
            'tipo_lavado_id' => ['required', 'exists:tipo_lavados,id'],
            'administrador_id' => ['required', 'exists:administrators,id'],

            // Reglas para cliente y vehículo existente/nuevo
            'user_id' => ['nullable', 'exists:users,id'], // Ahora user_id puede ser nulo si el vehículo es nuevo y no se seleccionó cliente
            'vehiculo_id' => ['nullable', 'exists:vehiculos,id'], // ID del vehículo existente

            // Reglas para un VEHÍCULO NUEVO (si vehiculo_id es nulo)
            'vehiculo_patente_nuevo' => [
                Rule::requiredIf(function () use ($request) {
                    return empty($request->vehiculo_id); // Requerido si no se seleccionó un vehículo existente
                }),
                'nullable', 'string', 'max:20', 'unique:vehiculos,patente',
            ],
            'vehiculo_marca_nuevo' => [
                Rule::requiredIf(function () use ($request) {
                    return empty($request->vehiculo_id);
                }),
                'nullable', 'string', 'max:255',
            ],
            'vehiculo_modelo_nuevo' => [
                Rule::requiredIf(function () use ($request) {
                    return empty($request->vehiculo_id);
                }),
                'nullable', 'string', 'max:255',
            ],
            'vehiculo_anio_nuevo' => [
                Rule::requiredIf(function () use ($request) {
                    return empty($request->vehiculo_id);
                }),
                'nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1),
            ],
            'vehiculo_tipo_vehiculo_id_nuevo' => [ // <-- ¡Nueva regla para el tipo de vehículo del coche nuevo!
                Rule::requiredIf(function () use ($request) {
                    return empty($request->vehiculo_id);
                }),
                'nullable', 'exists:tipos_vehiculos,id',
            ],
        ];

        // 2. Validar los datos
        $validatedData = $request->validate($rules);

        DB::beginTransaction();
        try {
            $box = Box::find($validatedData['box_id']);

            // Verificar si el box ya está ocupado
            if ($box->estado === 'ocupado') {
                DB::rollBack();
                return redirect()->back()->with('error', 'El box ya está ocupado.');
            }

            $vehiculo = null;
            $tipoVehiculoPrecio = 0;
            $tipoLavadoPrecio = 0;

            // 3. Obtener o crear el vehículo y su precio de tipo
            if (!empty($validatedData['vehiculo_id'])) {
                // Vehículo existente
                $vehiculo = Vehiculo::with('tipoVehiculo')->find($validatedData['vehiculo_id']);
                if (!$vehiculo) {
                    throw new \Exception("Vehículo existente no encontrado.");
                }
                $tipoVehiculoPrecio = $vehiculo->tipoVehiculo->precio ?? 0;
            } else {
                // Nuevo vehículo: Primero validar que se proporcionó user_id si no se seleccionó un existente
                // Este `user_id` vendría de la selección del cliente para el nuevo vehículo
                $userIdForNewVehicle = $request->input('user_id'); // Usamos input porque la validación de user_id es nullable
                if (empty($userIdForNewVehicle)) {
                    throw new \Exception("No se proporcionó un cliente para el nuevo vehículo.");
                }

                $vehiculo = Vehiculo::create([
                    'user_id' => $userIdForNewVehicle, // Usar el user_id del request
                    'tipo_vehiculo_id' => $validatedData['vehiculo_tipo_vehiculo_id_nuevo'], // ID del tipo de vehículo para el nuevo coche
                    'patente' => $validatedData['vehiculo_patente_nuevo'],
                    'marca' => $validatedData['vehiculo_marca_nuevo'],
                    'modelo' => $validatedData['vehiculo_modelo_nuevo'],
                    'anio' => $validatedData['vehiculo_anio_nuevo'],
                ]);
                // Obtener el precio del tipo de vehículo recién creado
                $tipoVehiculo = TipoVehiculo::find($validatedData['vehiculo_tipo_vehiculo_id_nuevo']);
                $tipoVehiculoPrecio = $tipoVehiculo->precio ?? 0;
            }

            // 4. Obtener el precio del tipo de lavado
            $tipoLavado = TipoLavado::find($validatedData['tipo_lavado_id']);
            if (!$tipoLavado) {
                throw new \Exception("Tipo de lavado no encontrado.");
            }
            $tipoLavadoPrecio = $tipoLavado->precio ?? 0;

            // 5. Calcular el precio total del servicio
            $precioTotalServicio = $tipoLavadoPrecio + $tipoVehiculoPrecio;

            // 6. Actualizar estado del box y crear el servicio
            $box->update(['estado' => 'ocupado']);

            ServicioLavado::create([
                'box_id' => $validatedData['box_id'],
                'vehiculo_id' => $vehiculo->id,
                'tipo_lavado_id' => $validatedData['tipo_lavado_id'],
                'administrador_id' => $validatedData['administrador_id'],
                'fecha_hora_inicio' => now(),
                'estado_servicio' => 'en_curso',
                'precio_total_servicio' => $precioTotalServicio, // <-- ¡Aquí se guarda el precio calculado!
            ]);

            DB::commit();

            return redirect()->route('boxes.show', $box->id)
                ->with('success', 'Servicio iniciado exitosamente. Box ahora ocupado.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al iniciar servicio de lavado: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Hubo un error al iniciar el servicio: ' . $e->getMessage());
        }
    }


    /**
     * Finaliza un servicio de lavado.
     */
    public function finalizar(Request $request, ServicioLavado $servicioLavado)
    {
        DB::beginTransaction();
        try {
            if ($servicioLavado->estado_servicio !== 'en_curso') {
                DB::rollBack();
                Log::warning('Intento de finalizar servicio no \'en_curso\'. ID: ' . $servicioLavado->id . ', estado: ' . $servicioLavado->estado_servicio);
                return redirect()->back()->with('error', 'Solo se pueden finalizar servicios en curso.');
            }

            $servicioLavado->update(['estado_servicio' => 'finalizado', 'fecha_hora_fin' => now()]);
            $servicioLavado->box->update(['estado' => 'activo']);

            DB::commit();
            Log::info('Transacción de finalización de servicio ' . $servicioLavado->id . ' exitosa.');
            Log::info('Estado del box después del commit: ' . $servicioLavado->box->estado);
            Log::info('Estado del servicio después del commit: ' . $servicioLavado->estado_servicio);

            // *** CAMBIO CRÍTICO AQUÍ ***
            // Usa Inertia::location para una redirección del lado del cliente
            return Inertia::location(route('boxes.show', $servicioLavado->box_id));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al finalizar servicio de lavado: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Hubo un error al finalizar el servicio: ' . $e->getMessage());
        }
    }

    /**
     * Cancela un servicio de lavado.
     */
    public function cancelar(ServicioLavado $servicioLavado)
    {
        DB::beginTransaction();
        try {
            // Cargar el box para actualizar su estado
            $box = $servicioLavado->box;

            // Actualizar el servicio a 'cancelado'
            $servicioLavado->update([
                'fecha_hora_fin' => now(),
                'estado_servicio' => 'cancelado',
                // Podrías poner precio_total_servicio a 0 o un valor de multa si aplica.
                'precio_total_servicio' => 0, // O dejar el valor que tenía si no quieres descontar
            ]);

            // Actualizar el estado del box a 'activo'
            if ($box) {
                $box->update(['estado' => 'activo']);
            }

            DB::commit();

            return redirect()->route('boxes.show', $servicioLavado->box_id)->with('success', 'Servicio cancelado correctamente. El box está nuevamente activo.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al cancelar servicio: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cancelar el servicio: ' . $e->getMessage());
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
            return redirect()->back()->with('error', 'Hubo un error al finalizar el servicio: ' . $e->getMessage());
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