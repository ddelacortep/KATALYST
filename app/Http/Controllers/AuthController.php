<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Participar;

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
        $request->validate([
            'username' => 'required|string|max:255|unique:usuario,nom_usuario',
            'email' => 'required|email|max:255|unique:usuario,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $usuario = Usuario::create([
            'id_usuario' => DB::table('usuario')->max('id_usuario') + 1,
            'nom_usuario' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        Auth::login($usuario);
        
        // Inicializar cache de proyectos vacío
        session(['user_projects' => []]);

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
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::where('nom_usuario', $request->username)
                        ->orWhere('email', $request->username)
                        ->first();

        if (!$usuario || $usuario->password !== $request->password) {
            return back()->withErrors(['username' => 'Credenciales incorrectas.'])
                        ->withInput($request->only('username'));
        }

        Auth::login($usuario);
        
        // Cargar proyectos del usuario en sesión
        $proyectos = Participar::where('id_usuario', $usuario->id_usuario)
            ->pluck('id_rols', 'id_proyecto')
            ->toArray();
        
        session(['user_projects' => $proyectos]);

        return redirect()->route('proyectos')->with('success', '¡Bienvenido de nuevo, ' . $usuario->nom_usuario . '!');
    }

    /**
     * Cerrar sesión
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('index')->with('success', 'Sesión cerrada correctamente.');
    }
}
