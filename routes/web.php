<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TareasController;
use App\Http\Controllers\RolsController;

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
Route::delete('/proyectos/{id}', [ProyectoController::class, 'destroy'])->name('proyectos.destroy');

// Ruta para ver un proyecto específico
Route::get('/proyectos/{id}', [ProyectoController::class, 'show'])->name('proyectos.show');

// Rutas para gestionar usuarios en proyectos
Route::post('/proyectos/{id}/usuarios', [ProyectoController::class, 'agregarUsuario'])->name('proyectos.usuarios.agregar');
Route::delete('/proyectos/{proyectoId}/usuarios/{usuarioId}', [ProyectoController::class, 'eliminarUsuario'])->name('proyectos.usuarios.eliminar');
Route::put('/proyectos/{proyectoId}/usuarios/{usuarioId}/rol', [ProyectoController::class, 'actualizarRolUsuario'])->name('proyectos.usuarios.actualizarRol');

// Rutas para tareas
Route::post('/tareas', [TareasController::class, 'store'])->name('tareas.store');
Route::put('/tareas/{id}', [TareasController::class, 'update'])->name('tareas.update');
Route::delete('/tareas/{id}', [TareasController::class, 'destroy'])->name('tareas.destroy');
Route::get('/tareas/{id}', [TareasController::class, 'show'])->name('tareas.show');

// Rutas para roles
Route::get('/roles', [RolsController::class, 'index'])->name('roles.index');
Route::post('/roles', [RolsController::class, 'store'])->name('roles.store');
Route::put('/roles/{id}', [RolsController::class, 'update'])->name('roles.update');
Route::delete('/roles/{id}', [RolsController::class, 'destroy'])->name('roles.destroy');
