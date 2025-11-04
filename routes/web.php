<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TareasController;
use App\Http\Controllers\RolsController;
use App\Http\Controllers\ParticipacionController;
use App\Http\Controllers\EstadoTareaController;

Route::get('/', function () {
    return view('index');
})->name('index');

// Rutas de autenticación
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de proyectos (requiere autenticación)
Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos');
Route::get('/create', function () {
    return view('create'); 
})->name('create');
Route::post('/proyectos/store', [ProyectoController::class, 'store'])->name('proyectos.store');
Route::delete('/proyectos/{proyecto:slug}', [ProyectoController::class, 'destroy'])->name('proyectos.destroy');

// Ruta para ver un proyecto específico (usa slug en lugar de ID)
Route::get('/proyectos/{proyecto:slug}', [ProyectoController::class, 'show'])->name('proyectos.show');

// Rutas para gestionar participación de usuarios en proyectos
Route::post('/proyectos/{proyectoId}/participacion', [ParticipacionController::class, 'store'])->name('participacion.store');
Route::delete('/proyectos/{proyectoId}/participacion/{usuarioId}', [ParticipacionController::class, 'destroy'])->name('participacion.destroy');
Route::put('/proyectos/{proyectoId}/participacion/{usuarioId}/rol', [ParticipacionController::class, 'updateRol'])->name('participacion.updateRol');

// Rutas para tareas
Route::post('/tareas', [TareasController::class, 'store'])->name('tareas.store');
Route::put('/tareas/{id}', [TareasController::class, 'update'])->name('tareas.update');
Route::delete('/tareas/{id}', [TareasController::class, 'destroy'])->name('tareas.destroy');
Route::get('/tareas/{id}', [TareasController::class, 'show'])->name('tareas.show');

// Ruta para cambiar estado de tareas
Route::put('/tareas/{tareaId}/estado', [EstadoTareaController::class, 'update'])->name('estado.update');

// Rutas para roles (solo accesibles desde dentro de un proyecto)
Route::post('/roles', [RolsController::class, 'store'])->name('roles.store');
Route::put('/roles/{id}', [RolsController::class, 'update'])->name('roles.update');
Route::delete('/roles/{id}', [RolsController::class, 'destroy'])->name('roles.destroy');

