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

Route::middleware(['auth', 'verified'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// logout
Route::middleware(['auth'])->post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::middleware(['auth', 'verified'])->get('/vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');
Route::middleware(['auth', 'verified'])->get('/vehiculos/create', [VehiculoController::class, 'create'])->name('vehiculos.create');
Route::middleware(['auth', 'verified'])->post('/vehiculos', [VehiculoController::class, 'store'])->name('vehiculos.store');
Route::middleware(['auth', 'verified'])->get('/vehiculos/{vehiculo}', [VehiculoController::class, 'show'])->name('vehiculos.show');
Route::middleware(['auth', 'verified'])->get('/vehiculos/{vehiculo}/edit', [VehiculoController::class, 'edit'])->name('vehiculos.edit');
Route::middleware(['auth', 'verified'])->put('/vehiculos/{vehiculo}', [VehiculoController::class, 'update'])->name('vehiculos.update');
Route::middleware(['auth', 'verified'])->delete('/vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');

require __DIR__.'/auth.php';
