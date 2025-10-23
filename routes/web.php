<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

// Página de inicio
Route::get('/', function () {
    return view('proyectos'); // resources/views/index.blade.php
})->name('proyectos');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/proyectos', function () {
    return view('proyectos'); 
})->name('proyectos');
