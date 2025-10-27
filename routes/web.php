<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\RegisterController;

Route::get('/', function () {
    return view('index');
})->name('index');

// Página de inicio
Route::get('/proyectos', function () {
    return view('proyectos'); // resources/views/proyectos.blade.php
})->name('proyectos');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/login', function () {
    return view('login'); 
})->name('login');

Route::get('/create', function () {
    return view('create'); 
})->name('create');

Route::post('/proyectos/store', [ProyectoController::class, 'store'])->name('proyectos.store');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');