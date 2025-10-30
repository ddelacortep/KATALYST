<?php

namespace App\Http\Controllers;

use App\Models\Rols;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Rols::all();
        
        // Si es una petición AJAX, devolver JSON
        if (request()->expectsJson()) {
            return response()->json($roles);
        }
        
        // Si no, devolver vista
        return view('roles.index', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_rols' => 'required|string|max:255',
        ]);

        // Obtener el siguiente ID disponible
        $nextId = DB::table('roles')->max('id_rols') + 1;

        $rol = new Rols();
        $rol->id_rols = $nextId;
        $rol->nom_rols = $request->nom_rols;
        $rol->save();

        // Si es una petición AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rol creado correctamente',
                'rol' => $rol
            ], 201);
        }

        // Si es una petición normal
        return redirect()->back()->with('success', 'Rol creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $rol = Rols::find($id);
        
        if (!$rol) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], 404);
        }

        return response()->json($rol);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rol = Rols::find($id);
        
        if (!$rol) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], 404);
        }

        $request->validate([
            'nom_rols' => 'sometimes|required|string|max:255',
        ]);

        if ($request->has('nom_rols')) {
            $rol->nom_rols = $request->nom_rols;
        }

        $rol->save();

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado correctamente',
            'rol' => $rol
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rol = Rols::find($id);
        
        if (!$rol) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], 404);
        }

        // Verificar si hay participaciones con este rol
        $participaciones = $rol->participaciones()->count();
        
        if ($participaciones > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el rol porque tiene usuarios asignados'
            ], 400);
        }

        $rol->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rol eliminado correctamente'
        ]);
    }
}
