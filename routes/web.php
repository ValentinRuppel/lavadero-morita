<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\VehiculoController;

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

// Aquí también puedes considerar especificar los guards si es necesario para estas rutas.
// Por ejemplo, si solo los 'web' users deben acceder a vehiculos:
// Route::middleware(['auth:web', 'verified'])->group(function () {
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