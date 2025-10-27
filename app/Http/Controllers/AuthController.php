<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Mostrar el formulario de registro
     */
    public function showRegisterForm()
    {
        return view('register');
    }

    /**
     * Procesar el registro
     */
    public function register(Request $request)
    {
        // Validar los datos
        $request->validate([
            'username' => 'required|string|max:255|unique:usuario,nom_usuario',
            'email' => 'required|email|max:255|unique:usuario,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'email.unique' => 'Este email ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Obtener el siguiente ID disponible
        $nextUserId = DB::table('usuario')->max('id_usuario') + 1;

        // Crear el usuario
        $usuario = new Usuario();
        $usuario->id_usuario = $nextUserId;
        $usuario->nom_usuario = $request->username;
        $usuario->email = $request->email;
        $usuario->password = $request->password; // Guardar en texto plano, SIN encriptar
        
        $usuario->save();

        // Iniciar sesión automáticamente
        Session::put('usuario_id', $usuario->id_usuario);
        Session::put('usuario_nombre', $usuario->nom_usuario);

        return redirect()->route('proyectos')->with('success', '¡Registro exitoso! Bienvenido.');
    }

    /**
     * Mostrar el formulario de login
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Procesar el login
     */
    public function login(Request $request)
    {
        // Validar los datos
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Buscar el usuario
        $usuario = Usuario::where('nom_usuario', $request->username)
                         ->orWhere('email', $request->username)
                         ->first();

        // Verificar si el usuario existe
        if (!$usuario) {
            return back()->withErrors([
                'username' => 'Usuario no encontrado.',
            ])->withInput($request->only('username'));
        }

        // Comparar la contraseña directamente (texto plano)
        if ($usuario->password === $request->password) {
            // Contraseña correcta - iniciar sesión
            Session::put('usuario_id', $usuario->id_usuario);
            Session::put('usuario_nombre', $usuario->nom_usuario);

            return redirect()->route('proyectos')->with('success', '¡Bienvenido de nuevo, ' . $usuario->nom_usuario . '!');
        }

        // Si la contraseña no coincide
        return back()->withErrors([
            'username' => 'La contraseña es incorrecta.',
        ])->withInput($request->only('username'));
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        Session::flush();
        return redirect()->route('index')->with('success', 'Sesión cerrada correctamente.');
    }
}
