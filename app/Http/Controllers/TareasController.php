<?php

namespace App\Http\Controllers;

use App\Models\Tareas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class TareasController extends Controller
{
    /**
     * Display a listing of the resource (tareas de un proyecto).
     */
    public function index($proyectoId = null)
    {
        if ($proyectoId) {
            $tareas = Tareas::where('id_proyecto', $proyectoId)->get();
        } else {
            $tareas = Tareas::all();
        }
        
        return response()->json($tareas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_tarea' => 'required|string|max:255',
            'descripcion_tarea' => 'nullable|string',
            'id_proyecto' => 'required|integer|exists:proyecto,id_proyecto',
            'id_usuario' => 'nullable|integer|exists:usuario,id_usuario',
            'id_estado' => 'nullable|integer|exists:estado_tarea,id_estado',
        ]);

        // Obtener el siguiente ID disponible
        $nextId = DB::table('tareas')->max('id_tareas') + 1;

        $tarea = new Tareas();
        $tarea->id_tareas = $nextId;
        $tarea->nom_tarea = $request->nom_tarea;
        $tarea->descripcion_tarea = $request->descripcion_tarea;
        $tarea->id_proyecto = $request->id_proyecto;
        $tarea->id_usuario = $request->id_usuario ?? Session::get('usuario_id');
        $tarea->id_estado = $request->id_estado ?? 1; // Estado por defecto: Pendiente
        $tarea->save();

        return response()->json([
            'success' => true,
            'message' => 'Tarea creada correctamente',
            'tarea' => $tarea
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tarea = Tareas::find($id);
        
        if (!$tarea) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        return response()->json($tarea);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tarea = Tareas::find($id);
        
        if (!$tarea) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        $request->validate([
            'nom_tarea' => 'sometimes|required|string|max:255',
            'descripcion_tarea' => 'nullable|string',
            'id_usuario' => 'sometimes|integer|exists:usuario,id_usuario',
            'id_estado' => 'sometimes|integer|exists:estado_tarea,id_estado',
        ]);

        if ($request->has('nom_tarea')) {
            $tarea->nom_tarea = $request->nom_tarea;
        }
        
        if ($request->has('descripcion_tarea')) {
            $tarea->descripcion_tarea = $request->descripcion_tarea;
        }
        
        if ($request->has('id_usuario')) {
            $tarea->id_usuario = $request->id_usuario;
        }
        
        if ($request->has('id_estado')) {
            $tarea->id_estado = $request->id_estado;
        }

        $tarea->save();

        return response()->json([
            'success' => true,
            'message' => 'Tarea actualizada correctamente',
            'tarea' => $tarea
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tarea = Tareas::find($id);
        
        if (!$tarea) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        $tarea->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tarea eliminada correctamente'
        ]);
    }
}
