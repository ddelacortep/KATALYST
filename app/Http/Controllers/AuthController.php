<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:usuario,nom_usuario',
            'email' => 'required|email|max:255|unique:usuario,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $usuario = Usuario::create([
            'id_usuario' => Usuario::max('id_usuario') + 1,
            'nom_usuario' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        Auth::login($usuario);
        $request->session()->regenerate();
        
        session([
            'user_id' => $usuario->id_usuario,
            'user_name' => $usuario->nom_usuario,
            'user_email' => $usuario->email,
            'user_projects' => [],
            'accessible_projects' => []
        ]);

        return redirect()->intended(route('proyectos'))->with('success', '¡Registro exitoso! Bienvenido.');
    }

    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $usuario = Usuario::where('nom_usuario', $request->username)
            ->orWhere('email', $request->username)
            ->where('password', $request->password)
            ->first(['id_usuario', 'nom_usuario', 'email']);

        if (!$usuario) {
            return back()->withErrors(['username' => 'Credenciales incorrectas.'])->withInput($request->only('username'));
        }

        // Cargar proyectos antes de hacer login
        $proyectosUsuario = $usuario->proyectos()
            ->pluck('participar.id_rols', 'participar.id_proyecto')
            ->toArray();

        // Autenticar usuario
        Auth::login($usuario);
        $request->session()->regenerate();
        
        // Establecer sesión después del login
        session([
            'user_id' => $usuario->id_usuario,
            'user_name' => $usuario->nom_usuario,
            'user_email' => $usuario->email,
            'user_projects' => $proyectosUsuario,
            'accessible_projects' => array_keys($proyectosUsuario)
        ]);

        return redirect()->intended(route('proyectos'))->with('success', "¡Bienvenido de nuevo, {$usuario->nom_usuario}!");
    }

    public function logout()
    {
        Auth::logout();
        session()->flush();
        return redirect()->route('index')->with('success', 'Sesión cerrada');
    }
}
