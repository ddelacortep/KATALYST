<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'nom_usuari' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuario,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verificar si el usuario ya existe
        $usuarioExistente = Usuario::where('email', $request->input('email'))->first();

        if ($usuarioExistente) {
            return redirect()->back()->withErrors(['email' => 'El correo electrónico ya está en uso.'])->withInput();
        }

        // Crear el nuevo usuario
        $usuario = Usuario::create([
            'nom_usuari' => $request->input('nom_usuari'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Iniciar sesión automáticamente
        session(['usuario_id' => $usuario->id_usuari]);

        // Redirigir a la página de proyectos
        return redirect()->route('proyectos')->with('success', '¡Registro exitoso! Bienvenido a Katalyst.');
    }
}
