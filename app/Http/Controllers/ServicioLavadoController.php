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
use Illuminate\Support\Facades\Auth;
class ServicioLavadoController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tabla = $user->getTable();
        
        $isAdmin = $tabla !== 'users';
        $query = ServicioLavado::query();
        $query->with([
            'vehiculo.user',
            'vehiculo.tipoVehiculo',
            'vehiculo.modelo.marca',
            'tipoLavado',
            'administrador'
        ]);

        if (!$isAdmin) {
            if ($user) {
                $query->whereHas('vehiculo', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } else {
                return redirect()->route('login')->with('error', 'Debes iniciar sesión para ver tus servicios.');
            }
        }
        $query->whereIn('estado_servicio', ['finalizado', 'cancelado']);
        $servicios = $query->orderBy('fecha_hora_inicio', 'desc')->paginate(10);
        return Inertia::render('Servicios/Index', [
            'user' => $user,
            'servicios' => $servicios->through(fn ($servicio) => [
                'id' => $servicio->id,
                'fecha_inicio' => $servicio->fecha_hora_inicio->format('d/m/Y H:i'),
                'fecha_fin' => $servicio->fecha_hora_fin ? $servicio->fecha_hora_fin->format('d/m/Y H:i') : 'N/A',
                'precio_total' => $servicio->precio_total_servicio,
                'estado' => $servicio->estado_servicio,
                'vehiculo' => [
                    'patente' => $servicio->vehiculo->patente,
                    'marca' => $servicio->vehiculo->modelo->marca->nombre ?? $servicio->vehiculo->marca,
                    'modelo' => $servicio->vehiculo->modelo->nombre ?? $servicio->vehiculo->modelo,
                    'anio' => $servicio->vehiculo->anio,
                    'tipo_vehiculo_nombre' => $servicio->vehiculo->tipoVehiculo->nombre ?? 'N/A',
                ],
                'tipo_lavado' => [
                    'nombre' => $servicio->tipoLavado->nombre_lavado,
                ],
                'cliente' => $isAdmin && $servicio->vehiculo->user ? [
                    'id' => $servicio->vehiculo->user->id,
                    'name' => $servicio->vehiculo->user->name,
                    'email' => $servicio->vehiculo->user->email,
                ] : null,
                'administrador_nombre' => $isAdmin && $servicio->administrador ? $servicio->administrador->name : null,
            ]),
            'isAdmin' => $isAdmin,
        ]);
    }
    /**
     * Iniciar un nuevo servicio de lavado.
     */
    public function store(Request $request)
    {
        Log::info('Intentando iniciar servicio - Datos recibidos:', $request->all());

        $rules = [
            'box_id' => ['required', 'exists:boxes,id'],
            'tipo_lavado_id' => ['required', 'exists:tipo_lavados,id'],
            'administrador_id' => ['required', 'exists:administrators,id'],

            'user_id' => ['nullable', 'exists:users,id'],
            'vehiculo_id' => ['nullable', 'exists:vehiculos,id'],

            'vehiculo_patente_nuevo' => [
                Rule::requiredIf(function () use ($request) { return empty($request->vehiculo_id); }),
                'nullable', 'string', 'max:20', 'unique:vehiculos,patente',
            ],
            'vehiculo_marca_nuevo' => [
                Rule::requiredIf(function () use ($request) { return empty($request->vehiculo_id); }),
                'nullable', 'string', 'max:255',
            ],
            'vehiculo_modelo_nuevo' => [
                Rule::requiredIf(function () use ($request) { return empty($request->vehiculo_id); }),
                'nullable', 'string', 'max:255',
            ],
            'vehiculo_anio_nuevo' => [
                Rule::requiredIf(function () use ($request) { return empty($request->vehiculo_id); }),
                'nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1),
            ],
            'vehiculo_tipo_vehiculo_id_nuevo' => [
                Rule::requiredIf(function () use ($request) { return empty($request->vehiculo_id); }),
                'nullable', 'exists:tipos_vehiculos,id',
            ],
        ];

        $validatedData = $request->validate($rules);

        DB::beginTransaction();
        try {
            $box = Box::find($validatedData['box_id']);
            if ($box->estado === 'ocupado') {
                DB::rollBack();
                return redirect()->back()->with('error', 'El box ya está ocupado.');
            }
            $vehiculo = null;
            $tipoVehiculoPrecio = 0;
            $tipoLavadoPrecio = 0;
            if (!empty($validatedData['vehiculo_id'])) {
                $vehiculo = Vehiculo::with('tipoVehiculo')->find($validatedData['vehiculo_id']);
                if (!$vehiculo) {
                    throw new \Exception("Vehículo existente no encontrado.");
                }
                $tipoVehiculoPrecio = $vehiculo->tipoVehiculo->precio ?? 0;
            } else {
                $userIdForNewVehicle = $request->input('user_id');
                if (empty($userIdForNewVehicle)) {
                    throw new \Exception("No se proporcionó un cliente para el nuevo vehículo.");
                }

                $vehiculo = Vehiculo::create([
                    'user_id' => $userIdForNewVehicle,
                    'tipo_vehiculo_id' => $validatedData['vehiculo_tipo_vehiculo_id_nuevo'],
                    'patente' => $validatedData['vehiculo_patente_nuevo'],
                    'marca' => $validatedData['vehiculo_marca_nuevo'],
                    'modelo' => $validatedData['vehiculo_modelo_nuevo'],
                    'anio' => $validatedData['vehiculo_anio_nuevo'],
                ]);
                $tipoVehiculo = TipoVehiculo::find($validatedData['vehiculo_tipo_vehiculo_id_nuevo']);
                $tipoVehiculoPrecio = $tipoVehiculo->precio ?? 0;
            }
            $existingActiveServiceForVehicle = ServicioLavado::where('vehiculo_id', $vehiculo->id)
                                                             ->where('estado_servicio', 'en_curso')
                                                             ->first();

            if ($existingActiveServiceForVehicle) {
                DB::rollBack();
                return redirect()->back()->with('error', 'El vehículo seleccionado ya se encuentra en un box ocupado');
            }
            $tipoLavado = TipoLavado::find($validatedData['tipo_lavado_id']);
            if (!$tipoLavado) {
                throw new \Exception("Tipo de lavado no encontrado.");
            }
            $tipoLavadoPrecio = $tipoLavado->precio ?? 0;
            $precioTotalServicio = $tipoLavadoPrecio + $tipoVehiculoPrecio;
            $box->update(['estado' => 'ocupado']);

            ServicioLavado::create([
                'box_id' => $validatedData['box_id'],
                'vehiculo_id' => $vehiculo->id,
                'tipo_lavado_id' => $validatedData['tipo_lavado_id'],
                'administrador_id' => $validatedData['administrador_id'],
                'fecha_hora_inicio' => now(),
                'estado_servicio' => 'en_curso',
                'precio_total_servicio' => $precioTotalServicio,
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
            $box = $servicioLavado->box;
            $servicioLavado->update([
                'fecha_hora_fin' => now(),
                'estado_servicio' => 'cancelado',
                'precio_total_servicio' => 0,
            ]);
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
        $validated = $request->validate([
            'precio_total_servicio' => 'nullable|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000',
        ]);
        DB::beginTransaction();
        try {
            if ($servicioLavado->estado_servicio !== 'en_curso') {
                DB::rollBack();
                return redirect()->back()
                                    ->with('error', 'El servicio no se puede finalizar porque no está en curso.');
            }
            if (!isset($validated['precio_total_servicio'])) {
                $tipoLavado = $servicioLavado->tipoLavado;
                $validated['precio_total_servicio'] = $tipoLavado->precio;
            }
            $servicioLavado->update([
                'fecha_hora_fin' => now(),
                'precio_total_servicio' => $validated['precio_total_servicio'],
                'estado_servicio' => 'finalizado',
            ]);
            $box = $servicioLavado->box; 
            $box->update(['estado' => 'activo']);
            DB::commit();
            return redirect()->route('boxes.show', $box->id)
                                ->with('success', 'Servicio finalizado exitosamente. Box ahora activo.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al finalizar servicio de lavado: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Hubo un error al finalizar el servicio: ' . $e->getMessage());
        }
    }
    public function cancel(ServicioLavado $servicioLavado)
    {
        DB::beginTransaction();
        try {
            if ($servicioLavado->estado_servicio !== 'en_curso') {
                DB::rollBack();
                return redirect()->back()->with('error', 'Solo se pueden cancelar servicios en curso.');
            }

            $servicioLavado->update(['estado_servicio' => 'cancelado', 'fecha_hora_fin' => now()]);
            $servicioLavado->box->update(['estado' => 'activo']);

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