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
            // Obtener el user_id de los parámetros de la URL si existe
            $selectedUserId = $request->query('user_id');
            $selectedUserName = $request->query('user_name'); // Opcional

            // Puedes obtener el cliente si es necesario para validación o mostrar más datos
            $selectedUser = null;
            if ($selectedUserId) {
                $selectedUser = User::find($selectedUserId);
            }

            return Inertia::render('Vehiculos/Create', [
                'tiposVehiculos' => TipoVehiculo::all(),
                'marcas' => Marca::all(), // Asegúrate de pasar las marcas para el select
                'clientes' => User::orderBy('name')->get(['id', 'name']), // Pasa la lista de clientes para el select
                'selectedUserId' => $selectedUserId, // Pasa el ID del cliente al frontend
                'selectedUserName' => $selectedUserName, // Pasa el nombre para un mensaje
                'isAdminContext' => Auth::guard('administrators')->check(), // Indica si la sesión es de admin
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
        $validated = $request->validate([
            // user_id ahora puede ser opcional si el admin lo está agregando desde un contexto general,
            // pero requerido si viene de una redirección de cliente sin vehículo.
            // La validación en el frontend de Show.vue ya asegura que mandamos un user_id válido.
            // Aquí, lo hacemos nullable y luego asignamos si existe.
            'user_id' => ['nullable', 'exists:users,id'],
            'tipo_vehiculo_id' => ['required', 'exists:tipos_vehiculos,id'],
            'marca' => ['required', 'string', 'max:255'],
            'modelo' => ['required', 'string', 'max:255'],
            'patente' => ['required', 'string', 'max:20', 'unique:vehiculos,patente'],
            'anio' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            // 'modelo_id' => ['nullable', 'exists:modelos,id'], // Si usas Modelo ID
        ]);

        // Determinar el user_id. Priorizar el user_id de la solicitud,
        // de lo contrario, usar el user_id del usuario autenticado si es un cliente.
        // Asumimos que los administradores pueden registrar vehículos para cualquier user_id,
        // y que los clientes solo registran para sí mismos (si tuvieran acceso a este formulario).
        $userIdToAssign = $validated['user_id'] ?? null;

        // Si no se proporcionó user_id en el formulario y hay un usuario logueado
        // (y si tu sistema permitiera a los usuarios regulares registrar sus propios vehículos),
        // podrías usar Auth::id() aquí.
        // Pero dado que los administradores son quienes lo hacen, confiaremos en 'user_id' del request.
        if (is_null($userIdToAssign) && Auth::check()) { // Si un cliente logueado usa esto
             $userIdToAssign = Auth::id();
        }

        // Si no hay user_id para asignar, puedes lanzar un error o redirigir
        if (is_null($userIdToAssign)) {
            return redirect()->back()->withErrors(['user_id' => 'Debe seleccionarse un cliente para asignar el vehículo.']);
        }

        // Crear el vehículo con el user_id asignado
        $vehiculo = Vehiculo::create(array_merge($validated, ['user_id' => $userIdToAssign]));

        return redirect()->route('vehiculos.index') // O a la página del cliente si tienes una
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