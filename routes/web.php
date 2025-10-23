<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

Route::get('/', function () {
    return view('welcome');
});

// Página de inicio
Route::get('/', function () {
    return view('proyectos'); // resources/views/index.blade.php
})->name('proyectos');

// Registro
Route::get('/register', function () {
    return view('register'); // resources/views/register.blade.php
})->name('register');

// Login
Route::get('/login', function () {
    return view('login'); // resources/views/login.blade.php
})->name('login');

// Proyectos
Route::get('/proyectos', function () {
    return view('proyectos'); // resources/views/proyectos.blade.php
})->name('proyectos');
