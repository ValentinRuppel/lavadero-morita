<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\User;
use App\Models\TipoVehiculo;
use App\Models\Marca; 
use App\Models\Modelo; 
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class VehiculoController extends Controller
{
    /**
     * Constructor para aplicar middlewares (opcional, pero buena práctica)
     */

    /**
     * Display a listing of the resource (Vehiculos/Index).
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $vehiculos = Vehiculo::with(['user', 'tipoVehiculo', 'modelo.marca'])
        ->where('user_id', $user->id)
        ->latest()
        ->paginate(10);

        return Inertia::render('Vehiculos/Index', [
            'vehiculos' => $vehiculos->through(fn ($vehiculo) => [
                'id' => $vehiculo->id,
                'marca_nombre' => $vehiculo->modelo->marca->nombre ?? 'N/A',
                'modelo_nombre' => $vehiculo->modelo->nombre ?? 'N/A',
                'patente' => $vehiculo->patente,
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
        $tiposVehiculo = TipoVehiculo::orderBy('nombre')->get(['id', 'nombre', 'precio']);
        $marcas = Marca::orderBy('nombre')->get(['id', 'nombre']);

        return Inertia::render('Vehiculos/Create', [
            'user' => $user,
            'tiposVehiculo' => $tiposVehiculo,
            'marcas' => $marcas,
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
            'modelo_id' => ['required', 'exists:modelos,id'],
            'anio' => ['required', 'integer', 'min:1900', 'max:' . date('Y')],
            'patente' => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('vehiculos', 'patente')],
            'tipo_vehiculo_id' => ['required', 'exists:tipos_vehiculos,id'],
        ]);

        Vehiculo::create([
            'user_id' => Auth::id(),
            'modelo_id' => $request->modelo_id,
            'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
            'patente' => $request->patente,
            'anio' => $request->anio,
        ]);

        return redirect()->route('vehiculos.index')
                         ->with('success', 'Vehículo registrado exitosamente.');
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
                          ->with('tipoVehiculo') 
                          ->get(['id', 'nombre', 'anio_inicio', 'anio_fin', 'tipo_vehiculo_id']);
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
        if ($vehiculo->user_id !== $user->id) {
            abort(403, 'No tienes permiso para ver este vehículo.');
        }

        $vehiculo->load(['user', 'tipoVehiculo', 'modelo.marca']);

        return Inertia::render('Vehiculos/Show', [
            'vehiculo' => [
                'id' => $vehiculo->id,
                'marca_nombre' => $vehiculo->modelo->marca->nombre ?? 'N/A',
                'modelo_nombre' => $vehiculo->modelo->nombre ?? 'N/A',
                'anio' => $vehiculo->anio,
                'patente' => $vehiculo->patente,
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
            'tipo_vehiculo_id' => ['required', 'exists:tipos_vehiculos,id'],
        ]);

        $vehiculo->update([
            'modelo_id' => $request->modelo_id,
            'tipo_vehiculo_id' => $request->tipo_vehiculo_id,
            'patente' => $request->patente,
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