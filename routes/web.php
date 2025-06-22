<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\BoxController;
use App\Http\Controllers\ServicioLavadoController;
Route::middleware(['auth:web,admin', 'verified'])->group(function () {
    Route::get('/boxes', [BoxController::class, 'index'])->name('boxes.index');
    Route::get('/boxes/create', [BoxController::class, 'create'])->name('boxes.create');
    Route::post('/boxes', [BoxController::class, 'store'])->name('boxes.store');
    Route::get('/boxes/{box}', [BoxController::class, 'show'])->name('boxes.show');
    Route::get('/boxes/{box}/edit', [BoxController::class, 'edit'])->name('boxes.edit');
    Route::put('/boxes/{box}', [BoxController::class, 'update'])->name('boxes.update');
    Route::delete('/boxes/{box}', [BoxController::class, 'destroy'])->name('boxes.destroy');
    Route::post('/servicios-lavado/iniciar', [ServicioLavadoController::class, 'store'])->name('servicios.iniciar');
    Route::put('/servicios-lavado/{servicioLavado}/finalizar', [ServicioLavadoController::class, 'update'])->name('servicios.finalizar');
    Route::post('/servicios-lavado/{servicioLavado}/cancelar', [ServicioLavadoController::class, 'cancel'])->name('servicios.cancelar'); // Ruta opcional
});



Route::get('/', function () {
    return Inertia::render('Bienvenido', [
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::middleware('auth')->group(function () {
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth'])->get('/perfil', function () {
    return Inertia::render('Profile/Edit');
})->name('profile.edit');

// --- MODIFICACIÓN IMPORTANTE AQUÍ ---
// Especifica que la ruta 'dashboard' debe permitir la autenticación con los guards 'web' o 'admin'
Route::middleware(['auth:web,admin', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::middleware(['auth'])->post('/logout', function () {
    // Es buena práctica desloguear de todos los guards explícitamente
    Auth::guard('web')->logout();
    Auth::guard('admin')->logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');
    Route::get('/vehiculos/create', [VehiculoController::class, 'create'])->name('vehiculos.create');
    Route::get('/vehiculos/modelos-by-marca', [VehiculoController::class, 'getModelosByMarca'])->name('vehiculos.modelosByMarca');
    Route::post('/vehiculos', [VehiculoController::class, 'store'])->name('vehiculos.store');
    Route::get('/vehiculos/{vehiculo}', [VehiculoController::class, 'show'])->name('vehiculos.show');
    Route::get('/vehiculos/{vehiculo}/edit', [VehiculoController::class, 'edit'])->name('vehiculos.edit');
    Route::put('/vehiculos/{vehiculo}', [VehiculoController::class, 'update'])->name('vehiculos.update');
    Route::delete('/vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');
});


require __DIR__.'/auth.php';