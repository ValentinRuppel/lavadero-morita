<?php

namespace App\Http\Controllers;

use App\Models\Box;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User; // Para los clientes
use App\Models\TipoLavado; // ¡Importa el modelo TipoLavado!
use App\Models\Vehiculo; // ¡Importa el modelo Vehiculo! (Si vas a permitir registrarlo en el mismo formulario)
use Illuminate\Support\Facades\Auth; // Para obtener el administrador logueado

class BoxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtiene todos los boxes, ordenados por nombre
        $boxes = Box::orderBy('nombre_box')->get();

        // Puedes cargar también información de servicios en curso para cada box
        // Esto es un ejemplo, ajusta las relaciones si necesitas más datos
        $boxes->load(['serviciosLavado' => function ($query) {
            $query->where('estado_servicio', 'en_curso')
                ->with(['vehiculo', 'tipoLavado', 'administrador']); // Carga las relaciones necesarias para el servicio
        }]);
        $user = $request->user();
        $tabla = $user->getTable();

        if ($tabla === 'administrators') {
            // Retorna la vista de Inertia con los datos de los boxes
            return Inertia::render('Boxes/Index', [
                'user' => $user,
                'boxes' => $boxes->map(function ($box) {
                    // Mapea los boxes para incluir solo los servicios en curso y formatear datos
                    return [
                        'id' => $box->id,
                        'nombre_box' => $box->nombre_box,
                        'descripcion' => $box->descripcion,
                        'estado' => $box->estado,
                        'servicio_en_curso' => $box->serviciosLavado->first(), // Obtiene el primer servicio en curso (asumiendo uno por box)
                        'created_at' => $box->created_at->diffForHumans(), // Formatear fechas si es necesario
                        'updated_at' => $box->updated_at->diffForHumans(),
                    ];
                }),
            ]);
        }
        return redirect()->route('dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Aquí podrías retornar la vista para crear un nuevo box
        return Inertia::render('Boxes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_box' => 'required|string|max:255|unique:boxes',
            'descripcion' => 'nullable|string|max:1000',
            // El estado por defecto es 'disponible', no es necesario validarlo aquí si no se permite cambiarlo al crear
        ]);

        Box::create($validated);

        return redirect()->route('boxes.index')
            ->with('success', 'Box creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Box $box, Request $request)
    {
        // 1. Carga los servicios en curso para el box
        //    Asegura cargar 'vehiculo.modelo.marca' para acceder a la marca desde el modelo del vehículo
        $box->load(['serviciosLavado' => function ($query) {
            $query->where('estado_servicio', 'en_curso')
                  ->with(['vehiculo.user', 'vehiculo.tipoVehiculo','vehiculo.modelo.marca', 'tipoLavado', 'administrador']); // Asegura cargar tipoVehiculo
        }]);

        $tiposLavado = TipoLavado::orderBy('nombre_lavado')->get();

        // 2. Cargar los clientes y sus vehículos, y las relaciones necesarias de los vehículos
        //    Asegura cargar solo clientes que tienen al menos un vehículo
        $clientes = User::with(['vehiculos.modelo.marca', 'vehiculos.tipoVehiculo'])
                         ->has('vehiculos') // <-- ¡AGREGADO: Filtra solo clientes con vehículos!
                         ->orderBy('name')
                         ->get();
        // 3. Determinar si el usuario logueado es un administrador
        $user = $request->user();
        $tabla = $user->getTable();
        $isAdmin = $tabla !== 'users';
        if($isAdmin){
                    $loggedInAdminId = $user->id;
                    return Inertia::render('Boxes/Show', [
            'box' => [
                'id' => $box->id,
                'nombre_box' => $box->nombre_box,
                'descripcion' => $box->descripcion,
                'estado' => $box->estado,
                'servicio_en_curso' => $box->serviciosLavado->first() ? [
                    'id' => $box->serviciosLavado->first()->id,
                    'vehiculo' => $box->serviciosLavado->first()->vehiculo ? [
                        'id' => $box->serviciosLavado->first()->vehiculo->id,
                        'patente' => $box->serviciosLavado->first()->vehiculo->patente,
                        'marca' => $box->serviciosLavado->first()->vehiculo->modelo->marca->nombre ?? $box->serviciosLavado->first()->vehiculo->marca,
                        'modelo' => $box->serviciosLavado->first()->vehiculo->modelo->nombre ?? $box->serviciosLavado->first()->vehiculo->modelo,
                        'anio' => $box->serviciosLavado->first()->vehiculo->anio,
                        'cliente_nombre' => $box->serviciosLavado->first()->vehiculo->user->name ?? 'N/A',
                        'tipo_vehiculo_precio' => $box->serviciosLavado->first()->vehiculo->tipoVehiculo->precio ?? 0,
                    ] : null,
                    'tipo_lavado' => $box->serviciosLavado->first()->tipoLavado ? [
                        'id' => $box->serviciosLavado->first()->tipoLavado->id,
                        'nombre_lavado' => $box->serviciosLavado->first()->tipoLavado->nombre_lavado,
                        'precio' => $box->serviciosLavado->first()->tipoLavado->precio,
                        'duracion_estimada' => $box->serviciosLavado->first()->tipoLavado->duracion_estimada,
                    ] : null,
                    'administrador' => $box->serviciosLavado->first()->administrador ? [
                        'id' => $box->serviciosLavado->first()->administrador->id,
                        'name' => $box->serviciosLavado->first()->administrador->name,
                    ] : null,
                    'fecha_hora_inicio' => $box->serviciosLavado->first()->fecha_hora_inicio,
                    'precio_total_servicio' => $box->serviciosLavado->first()->precio_total_servicio,
                    'estado_servicio' => $box->serviciosLavado->first()->estado_servicio,
                ] : null,
                'created_at' => $box->created_at->diffForHumans(),
                'updated_at' => $box->updated_at->diffForHumans(),
            ],
            'tiposLavado' => $tiposLavado->map(fn($tipo) => [
                'id' => $tipo->id,
                'nombre_lavado' => $tipo->nombre_lavado,
                'precio' => $tipo->precio,
                'duracion_estimada' => $tipo->duracion_estimada,
            ]),
            'user' => $user,
            'clientes' => $clientes->map(fn($cliente) => [
                'id' => $cliente->id,
                'name' => $cliente->name,
                'email' => $cliente->email,
                'vehiculos' => $cliente->vehiculos->map(fn($v) => [
                    'id' => $v->id,
                    'patente' => $v->patente,
                    'marca' => $v->marca, // Esta es la marca directa del vehículo, el 'display' usa la del modelo
                    'modelo' => $v->modelo, // Este es el modelo directo del vehículo, el 'display' usa la del modelo
                    'anio' => $v->anio,
                    'display' => $v->patente . ' ' . ($v->modelo->marca->nombre ?? $v->marca) . ' ' . ($v->modelo->nombre ?? $v->modelo) . ' ' . $v->anio,
                    'tipo_vehiculo_id' => $v->tipo_vehiculo_id,
                    'tipo_vehiculo_precio' => $v->tipoVehiculo->precio ?? 0,
                ])->toArray(),
            ]),
            'adminId' => $loggedInAdminId,
        ]);
        }
        return redirect()->route('dashboard');

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Box $box)
    {
        if ($box->estado === 'ocupado') {
            return redirect()->route('boxes.index')
                ->with('error', 'No se puede editar un box que está ocupado.');
        }
        return Inertia::render('Boxes/Edit', [
            'box' => $box,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Box $box)
    {
        if ($box->estado === 'ocupado') {
            return redirect()->route('boxes.index')
                ->with('error', 'No se puede actualizar un box que está ocupado.');
        }
        $validated = $request->validate([
            'nombre_box' => 'required|string|max:255|unique:boxes,nombre_box,' . $box->id,
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|in:activo,mantenimiento',
        ]);

        $box->update($validated);

        return redirect()->route('boxes.index')
            ->with('success', 'Box actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Box $box)
    {
        if ($box->estado === 'ocupado') {
            return redirect()->route('boxes.index')
                ->with('error', 'No se puede eliminar un box que está ocupado.');
        }
        $box->delete();

        return redirect()->route('boxes.index')
            ->with('success', 'Box eliminado exitosamente.');
    }
}
