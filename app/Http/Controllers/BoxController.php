<?php

namespace App\Http\Controllers;

use App\Models\Box;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BoxController extends Controller
{
    public function index()
    {
        $boxes = Box::all();
        return Inertia::render('Boxes/Index', [
            'boxes' => $boxes->map(function ($box) {
                return [
                    'id' => $box->id,
                    'numero' => $box->numero,
                    'estado' => $box->estado,
                ];
            }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Boxes/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|string|max:255',
        ]);

        Box::create([
            'numero' => $request->numero,
            'estado' => 'libre', // por defecto libre
        ]);

        return redirect()->route('boxes.index');
    }

    public function show(Box $box)
    {
        return Inertia::render('Boxes/Show', compact('box'));
    }

    public function edit(Box $box)
    {
        return Inertia::render('Boxes/Edit', compact('box'));
    }

    public function update(Request $request, Box $box)
    {
        $validated = $request->validate([
            'numero' => 'required',
        ]);

        $box->update($validated);

        return redirect()->route('boxes.index');
    }

    public function destroy(Box $box)
    {
        $box->delete();
        return redirect()->route('boxes.index');
    }
}
