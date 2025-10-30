<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = Usuario::select('id_usuario', 'nom_usuario', 'email')->get();
        return response()->json($usuarios);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_usuario' => 'required|string|max:255|unique:usuario,nom_usuario',
            'email' => 'required|email|max:255|unique:usuario,email',
            'password' => 'required|string|min:6',
        ]);

        // Obtener el siguiente ID disponible
        $nextId = DB::table('usuario')->max('id_usuario') + 1;

        $usuario = new Usuario();
        $usuario->id_usuario = $nextId;
        $usuario->nom_usuario = $request->nom_usuario;
        $usuario->email = $request->email;
        $usuario->password = $request->password;
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado correctamente',
            'usuario' => [
                'id_usuario' => $usuario->id_usuario,
                'nom_usuario' => $usuario->nom_usuario,
                'email' => $usuario->email
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $usuario = Usuario::find($id);
        
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'id_usuario' => $usuario->id_usuario,
            'nom_usuario' => $usuario->nom_usuario,
            'email' => $usuario->email
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $request->validate([
            'nom_usuario' => 'sometimes|required|string|max:255|unique:usuario,nom_usuario,' . $id . ',id_usuario',
            'email' => 'sometimes|required|email|max:255|unique:usuario,email,' . $id . ',id_usuario',
            'password' => 'sometimes|required|string|min:6',
        ]);

        if ($request->has('nom_usuario')) {
            $usuario->nom_usuario = $request->nom_usuario;
        }
        
        if ($request->has('email')) {
            $usuario->email = $request->email;
        }
        
        if ($request->has('password')) {
            $usuario->password = $request->password;
        }

        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'usuario' => [
                'id_usuario' => $usuario->id_usuario,
                'nom_usuario' => $usuario->nom_usuario,
                'email' => $usuario->email
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Verificar si tiene proyectos asignados
        $proyectos = $usuario->proyectos()->count();
        
        if ($proyectos > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el usuario porque tiene proyectos asignados'
            ], 400);
        }

        $usuario->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado correctamente'
        ]);
    }
}
