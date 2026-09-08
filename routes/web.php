<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Página principal (para clientes)
Route::get('/', function () {
    return view('welcome');
})->name('cliente.home');

// Rutas para el Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

// Rutas para el Cajero
Route::middleware(['auth', 'role:cajero'])->group(function () {
    Route::get('/cajero', function () {
        return view('cajero.dashboard');
    })->name('cajero.dashboard');
});

// Rutas para el Repartidor
Route::middleware(['auth', 'role:repartidor'])->group(function () {
    Route::get('/repartidor', function () {
        return view('repartidor.dashboard');
    })->name('repartidor.dashboard');
});

// Rutas de perfil (de Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
