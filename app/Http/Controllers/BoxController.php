<?php

namespace App\Http\Controllers;

use App\Models\Box;
use Illuminate\Http\Request;
use Inertia\Inertia;
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
        // Carga el servicio en curso, si lo hay, con sus relaciones
        $box->load(['serviciosLavado' => function ($query) {
            $query->where('estado_servicio', 'en_curso')
                ->with(['vehiculo', 'tipoLavado', 'administrador']);
        }]);

        // Obtener todos los tipos de lavado disponibles
        $tiposLavado = TipoLavado::orderBy('nombre_lavado')->get();

        // Puedes obtener una lista de vehículos existentes si el usuario va a seleccionarlos
        // O si vas a permitir registrar uno nuevo en el momento, esta lista podría ser para "vehículos frecuentes"
        $vehiculos = Vehiculo::orderBy('patente')->get(); // O solo los más recientes, o con paginación
        $user = $request->user();

        return Inertia::render('Boxes/Show', [
            'box' => [
                'id' => $box->id,
                'nombre_box' => $box->nombre_box,
                'descripcion' => $box->descripcion,
                'estado' => $box->estado,
                'servicio_en_curso' => $box->serviciosLavado->first(),
                'created_at' => $box->created_at->diffForHumans(),
                'updated_at' => $box->updated_at->diffForHumans(),
            ],
            'tiposLavado' => $tiposLavado->map(fn($tipo) => [ // Mapeamos para enviar solo lo necesario
                'id' => $tipo->id,
                'nombre_lavado' => $tipo->nombre_lavado,
                'precio' => $tipo->precio,
            ]),
            'vehiculos' => $vehiculos->map(fn($vehiculo) => [ // Mapeamos para enviar solo lo necesario
                'id' => $vehiculo->id,
                'patente' => $vehiculo->patente,
                'marca' => $vehiculo->marca,
                'modelo' => $vehiculo->modelo,
                // Puedes añadir más campos si los necesitas para la selección
                'display' => $vehiculo->patente . ' (' . $vehiculo->marca . ' ' . $vehiculo->modelo . ')',
            ]),
            'adminId' => $user->id, // ID del administrador logueado
            'user' => $user,
        ]);
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
