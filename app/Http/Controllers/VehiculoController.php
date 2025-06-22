<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\User;
use App\Models\TipoVehiculo;
use App\Models\Marca; // Necesitas importar Marca
use App\Models\Modelo; // Necesitas importar Modelo
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth; // Para acceder a Auth::id()

class VehiculoController extends Controller
{
    /**
     * Constructor para aplicar middlewares (opcional, pero buena práctica)
     */
    // protected $middleware = ['auth']; // Puedes ponerlo así directamente si no necesitas lógica compleja

    /**
     * Display a listing of the resource (Vehiculos/Index).
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Asegúrate de cargar la relación 'modelo' y 'marca' si vas a mostrarla en la tabla
        $vehiculos = Vehiculo::with(['user', 'tipoVehiculo', 'modelo.marca'])->latest()->paginate(10);

        return Inertia::render('Vehiculos/Index', [
            'vehiculos' => $vehiculos->through(fn ($vehiculo) => [
                'id' => $vehiculo->id,
                'marca_nombre' => $vehiculo->modelo->marca->nombre ?? 'N/A', // Obtén la marca del modelo
                'modelo_nombre' => $vehiculo->modelo->nombre ?? 'N/A',       // Obtén el modelo
                'patente' => $vehiculo->patente,
                // 'color' => $vehiculo->color, // <-- ¡Eliminada!
                'anio' => $vehiculo->anio,
                'tipo_nombre' => $vehiculo->tipoVehiculo->nombre ?? 'N/A',
                'tipo_precio' => $vehiculo->tipoVehiculo->precio ?? 0,
                'cliente_nombre' => $vehiculo->user->name ?? 'N/A',
                'cliente_id' => $vehiculo->user->id,
            ]),
            'user' => $user,
        ]);
    }

    /**
     * Show the form for creating a new resource (Vehiculos/Create).
     *
     * @return \Inertia\Response
     */
    public function create(Request $request)
    {
        $user = $request->user();

        // No pasamos clientes, ya que el usuario autenticado será el dueño
        $tiposVehiculo = TipoVehiculo::orderBy('nombre')->get(['id', 'nombre', 'precio']);
        $marcas = Marca::orderBy('nombre')->get(['id', 'nombre']); // <-- Pasa las marcas

        return Inertia::render('Vehiculos/Create', [
            'user' => $user,
            'tiposVehiculo' => $tiposVehiculo,
            'marcas' => $marcas, // <-- Pasa las marcas al frontend
            // No pasamos 'clientes'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            // 'marca' y 'modelo' ya no son directos, ahora usamos 'modelo_id'
            'modelo_id' => ['required', 'exists:modelos,id'], // Validar que el modelo exista
            'anio' => ['required', 'integer', 'min:1900', 'max:' . date('Y')], // Año hasta el actual
            'patente' => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('vehiculos', 'patente')],
            // 'color' ya no se valida
            'tipo_vehiculo_id' => ['required', 'exists:tipos_vehiculos,id'],
            // 'user_id' ya no se valida aquí, se asigna automáticamente
        ]);

        Vehiculo::create([
            'user_id' => Auth::id(), // <-- Asignar automáticamente el ID del usuario logueado
            'modelo_id' => $request->modelo_id,
            'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
            'patente' => $request->patente,
            // 'color' ya no se guarda
            'anio' => $request->anio,
        ]);

        return redirect()->route('vehiculos.index')
                         ->with('success', 'Vehículo registrado exitosamente.'); // Cambiado a "Registrado"
    }

    /**
     * Endpoint para obtener modelos basados en una marca seleccionada
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModelosByMarca(Request $request)
    {
        $request->validate([
            'marca_id' => ['required', 'exists:marcas,id'],
        ]);

        $modelos = Modelo::where('marca_id', $request->marca_id)
                          ->orderBy('nombre')
                          ->with('tipoVehiculo') // Carga la relación
                          ->get(['id', 'nombre', 'anio_inicio', 'anio_fin', 'tipo_vehiculo_id']);

        // Transformar la colección para asegurar que anio_fin siempre sea un año concreto
        $currentYear = date('Y');
        $modelos->each(function ($modelo) use ($currentYear) {
            if (is_null($modelo->anio_fin)) {
                $modelo->anio_fin = $currentYear;
            }
        });

        return response()->json($modelos);
    }

    /**
     * Display the specified resource (Vehiculos/Show).
     *
     * @param  \App\Models\Vehiculo  $vehiculo
     * @return \Inertia\Response
     */
    public function show(Request $request, Vehiculo $vehiculo)
    {
        $user = $request->user();
        $vehiculo->load(['user', 'tipoVehiculo', 'modelo.marca']); // Cargar relaciones necesarias

        return Inertia::render('Vehiculos/Show', [
            'vehiculo' => [
                'id' => $vehiculo->id,
                'marca_nombre' => $vehiculo->modelo->marca->nombre ?? 'N/A',
                'modelo_nombre' => $vehiculo->modelo->nombre ?? 'N/A',
                'anio' => $vehiculo->anio,
                'patente' => $vehiculo->patente,
                // 'color' => $vehiculo->color, // <-- ¡Eliminada!
                'tipo_nombre' => $vehiculo->tipoVehiculo->nombre ?? 'N/A',
                'tipo_precio' => $vehiculo->tipoVehiculo->precio ?? 0,
                'cliente_id' => $vehiculo->user->id,
                'created_at' => $vehiculo->created_at->format('d/m/Y H:i'),
                'updated_at' => $vehiculo->updated_at->format('d/m/Y H:i'),
            ],
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource (Vehiculos/Edit).
     *
     * @param  \App\Models\Vehiculo  $vehiculo
     * @return \Inertia\Response
     */
    public function edit(Request $request, Vehiculo $vehiculo)
    {
        $user = $request->user();
        $tiposVehiculo = TipoVehiculo::orderBy('nombre')->get(['id', 'nombre', 'precio']);
        $marcas = Marca::orderBy('nombre')->get(['id', 'nombre']);
        $vehiculo->load('modelo');
        $modelosMarcaActual = [];
        if ($vehiculo->modelo) {
            $modelosMarcaActual = Modelo::where('marca_id', $vehiculo->modelo->marca_id)
                                    ->orderBy('nombre')
                                    ->get(['id', 'nombre', 'anio_inicio', 'anio_fin']);
        }

        return Inertia::render('Vehiculos/Edit', [
            'vehiculo' => [
                'id' => $vehiculo->id,
                'modelo_id' => $vehiculo->modelo_id,
                'anio' => $vehiculo->anio,
                'patente' => $vehiculo->patente,
                'tipo_vehiculo_id' => $vehiculo->tipo_vehiculo_id,
                'user_id' => $vehiculo->user_id,
            ],
            'tiposVehiculo' => $tiposVehiculo,
            'marcas' => $marcas,
            'modelosMarcaActual' => $modelosMarcaActual,
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vehiculo  $vehiculo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        $request->validate([
            'modelo_id' => ['required', 'exists:modelos,id'],
            'anio' => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
            'patente' => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('vehiculos', 'patente')->ignore($vehiculo->id)],
            // 'color' ya no se valida
            'tipo_vehiculo_id' => ['required', 'exists:tipos_vehiculos,id'],
            // 'user_id' no debería ser editable por el cliente
        ]);

        $vehiculo->update([
            'modelo_id' => $request->modelo_id,
            'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
            'patente' => $request->patente,
            // 'color' ya no se guarda
            'anio' => $request->anio,
        ]);

        return redirect()->route('vehiculos.index')
                         ->with('success', 'Vehículo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vehiculo  $vehiculo
     * @return \Illuminate\Http\RedirectResponse
    */
    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculo->delete();
        return redirect()->route('vehiculos.index');
    }
}