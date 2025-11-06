<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        return response()->json(Usuario::select('id_usuario', 'nom_usuario', 'email')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_usuario' => 'required|string|max:255|unique:usuario,nom_usuario',
            'email' => 'required|email|max:255|unique:usuario,email',
            'password' => 'required|string|min:6',
        ]);

        $usuario = Usuario::create([
            'id_usuario' => Usuario::max('id_usuario') + 1,
            'nom_usuario' => $request->nom_usuario,
            'email' => $request->email,
            'password' => $request->password
        ]);

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

    public function show($id)
    {
        $usuario = Usuario::find($id);
        
        return $usuario 
            ? response()->json(['id_usuario' => $usuario->id_usuario, 'nom_usuario' => $usuario->nom_usuario, 'email' => $usuario->email])
            : response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        
        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
            'nom_usuario' => 'sometimes|required|string|max:255|unique:usuario,nom_usuario,' . $id . ',id_usuario',
            'email' => 'sometimes|required|email|max:255|unique:usuario,email,' . $id . ',id_usuario',
            'password' => 'sometimes|required|string|min:6',
        ]);

        $usuario->update($request->only(['nom_usuario', 'email', 'password']));

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'usuario' => ['id_usuario' => $usuario->id_usuario, 'nom_usuario' => $usuario->nom_usuario, 'email' => $usuario->email]
        ]);
    }

    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        
        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        if ($usuario->proyectos()->exists()) {
            return response()->json(['success' => false, 'message' => 'No se puede eliminar el usuario porque tiene proyectos asignados'], 400);
        }

        $usuario->delete();

        return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
    }
}