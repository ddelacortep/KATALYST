<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('index');
})->name('index');

// Rutas de autenticación
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Página de proyectos (requiere autenticación)
Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos');
Route::get('/create', function () {
    return view('create'); 
})->name('create');
Route::post('/proyectos/store', [ProyectoController::class, 'store'])->name('proyectos.store');
Route::delete('/proyectos/{id}', [ProyectoController::class, 'destroy'])->name('proyectos.destroy');