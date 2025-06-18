<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia; // Si usas Inertia.js

class LoginController extends Controller
{

    public function showLoginForm()
    {
        return Inertia::render('Admin/Auth/Login'); // Crea esta vista
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Intenta autenticar usando el guardia 'admin'
        if (Auth::guard('admin')->attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout(); // Cierra sesión solo para el guardia 'admin'

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login'); // Redirige a la página de login de admin
    }
}