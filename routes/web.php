<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Página de inicio
Route::get('/', function () {
    return view('index'); // resources/views/index.blade.php
});

// Registro
Route::get('/register', function () {
    return view('register'); // resources/views/register.blade.php
});

// Login
Route::get('/login', function () {
    return view('login'); // resources/views/login.blade.php
});

// Proyectos
Route::get('/proyectos', function () {
    return view('proyectos'); // resources/views/proyectos.blade.php
});
