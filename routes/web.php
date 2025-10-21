<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Página de inicio
Route::get('/', function () {
    return view('proyectos'); // resources/views/index.blade.php
});
