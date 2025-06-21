<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tabla = $user->getTable();

        return Inertia::render(
            $tabla === 'administrators' ? 'Admin/Dashboard' : 'Dashboard',
            [
                'user' => $user,
            ]
        );
    }
}