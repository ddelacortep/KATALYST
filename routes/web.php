<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/createproyecto', function () {
    return view('createproyecto'); 
})->name('createproyecto');
