<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\User;
use App\Models\TipoVehiculo;
use Illuminate\Http\Request;
use Inertia\Inertia;


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

        $vehiculos = Vehiculo::with(['user', 'tipoVehiculo'])->latest()->paginate(10);

        return Inertia::render('Vehiculos/Index', [
            'vehiculos' => $vehiculos->through(fn ($vehiculo) => [
                'id' => $vehiculo->id,
                'marca' => $vehiculo->marca,
                'modelo' => $vehiculo->modelo,
                'patente' => $vehiculo->patente,
                'color' => $vehiculo->color,
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
    public function create(Request $request) // <-- ¡¡ESTE ES EL MÉTODO QUE FALTABA!!
    {
        $user = $request->user(); // Obtén el usuario autenticado para pasarlo al layout
        $tiposVehiculo = TipoVehiculo::orderBy('nombre')->get(['id', 'nombre', 'precio']);

        return Inertia::render('Vehiculos/Create', [
            'user' => $user, // Pasa el usuario al layout
            'tiposVehiculo' => $tiposVehiculo, // Pasa los tipos de vehículo si es necesario
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
            'marca' => ['required', 'string', 'max:255'],
            'modelo' => ['required', 'string', 'max:255'],
            'anio' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'patente' => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('vehiculos', 'patente')],
            'color' => ['nullable', 'string', 'max:50'],
            'tipo_vehiculo_id' => ['required', 'exists:tipos_vehiculos,id'],
            'user_id' => ['required', 'exists:users,id', \Illuminate\Validation\Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'cliente');
            })],
        ]);

        Vehiculo::create($request->all());

        return redirect()->route('vehiculos.index')
                        ->with('success', 'Vehículo creado exitosamente.');
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
        $vehiculo->load(['user', 'tipoVehiculo']);

        return Inertia::render('Vehiculos/Show', [
            'vehiculo' => [
                'id' => $vehiculo->id,
                'marca' => $vehiculo->marca,
                'modelo' => $vehiculo->modelo,
                'anio' => $vehiculo->anio,
                'patente' => $vehiculo->patente,
                'color' => $vehiculo->color,
                'tipo_nombre' => $vehiculo->tipoVehiculo->nombre ?? 'N/A',
                'tipo_precio' => $vehiculo->tipoVehiculo->precio ?? 0,
                'cliente_nombre' => $vehiculo->user->name ?? 'N/A',
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
        $clientes = User::where('role', 'cliente')->orderBy('name')->get(['id', 'name']);
        $tiposVehiculo = TipoVehiculo::orderBy('nombre')->get(['id', 'nombre', 'precio']);

        return Inertia::render('Vehiculos/Edit', [
            'vehiculo' => [
                'id' => $vehiculo->id,
                'marca' => $vehiculo->marca,
                'modelo' => $vehiculo->modelo,
                'anio' => $vehiculo->anio,
                'patente' => $vehiculo->patente,
                'color' => $vehiculo->color,
                'tipo_vehiculo_id' => $vehiculo->tipo_vehiculo_id,
                'user_id' => $vehiculo->user_id,
            ],
            'clientes' => $clientes,
            'tiposVehiculo' => $tiposVehiculo,
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
            'marca' => ['required', 'string', 'max:255'],
            'modelo' => ['required', 'string', 'max:255'],
            'anio' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'patente' => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('vehiculos', 'patente')->ignore($vehiculo->id)],
            'color' => ['nullable', 'string', 'max:50'],
            'tipo_vehiculo_id' => ['required', 'exists:tipos_vehiculos,id'],
            'user_id' => ['required', 'exists:users,id', \Illuminate\Validation\Rule::exists('users', 'id')->where(function ($query) {
                $query->where('role', 'cliente');
            })],
        ]);

        $vehiculo->update($request->all());

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

        return redirect()->route('vehiculos.index')
                         ->with('success', 'Vehículo eliminado exitosamente.');
    }
}