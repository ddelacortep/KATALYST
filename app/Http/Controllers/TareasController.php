<?php

namespace App\Http\Controllers;

use App\Models\Tareas;
use Illuminate\Http\Request;

class TareasController extends Controller
{
    public function index($proyectoId = null)
    {
        $tareas = $proyectoId 
            ? Tareas::with(['usuario', 'estadoTarea'])->where('id_proyecto', $proyectoId)->get()
            : Tareas::with(['usuario', 'estadoTarea'])->get();
        
        return response()->json($tareas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_tarea' => 'required|string|max:255',
            'id_proyecto' => 'required|integer|exists:proyecto,id_proyecto',
            'id_usuario' => 'nullable|integer|exists:usuario,id_usuario'
        ]);

        if (!Tareas::puedeCrear($request->id_proyecto)) {
            return $request->expectsJson() 
                ? response()->json(['success' => false, 'message' => 'Sin permisos'], 403)
                : back()->with('error', 'No tienes permisos para crear tareas');
        }

        try {
            $tarea = Tareas::crearConEstado($request->nom_tarea, $request->id_proyecto, $request->id_usuario);

            return $request->expectsJson() 
                ? response()->json(['success' => true, 'message' => 'Tarea creada', 'tarea' => $tarea], 201)
                : back()->with('success', 'Tarea creada correctamente');
        } catch (\Exception $e) {
            return $request->expectsJson() 
                ? response()->json(['success' => false, 'message' => 'Error al crear la tarea: ' . $e->getMessage()], 500)
                : back()->with('error', 'Error al crear la tarea');
        }
    }

    public function show($id)
    {
        return response()->json(Tareas::with(['usuario', 'estadoTarea', 'proyecto'])->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $tarea = Tareas::with('proyecto')->findOrFail($id);
        
        if (!$tarea->puedeEditar()) {
            return $request->expectsJson() 
                ? response()->json(['success' => false, 'message' => 'Sin permisos'], 403)
                : back()->with('error', 'Sin permisos para editar');
        }

        $request->validate([
            'nom_tarea' => 'sometimes|required|string|max:255',
            'id_usuario' => 'sometimes|integer|exists:usuario,id_usuario'
        ]);

        $tarea->fill(array_filter([
            'nom_tarea' => $request->nom_tarea,
            'id_usuario' => $request->has('id_usuario') && $tarea->esAdministrador() 
                ? $request->id_usuario 
                : null
        ]))->save();

        return $request->expectsJson() 
            ? response()->json(['success' => true, 'message' => 'Tarea actualizada', 'tarea' => $tarea->load(['usuario', 'estadoTarea'])])
            : back()->with('success', 'Tarea actualizada');
    }

    public function destroy($id)
    {
        $tarea = Tareas::findOrFail($id);
        
        if (!$tarea->puedeEliminar()) {
            return request()->expectsJson() 
                ? response()->json(['success' => false, 'message' => 'Sin permisos'], 403)
                : back()->with('error', 'Sin permisos para eliminar');
        }

        $tarea->eliminarConEstado();

        return request()->expectsJson() 
            ? response()->json(['success' => true, 'message' => 'Tarea eliminada'])
            : back()->with('success', 'Tarea eliminada');
    }
}
