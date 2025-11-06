<?php

namespace App\Http\Controllers;

use App\Models\Tareas;
use App\Helpers\PermisosHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
            'id_proyecto' => 'required|integer|exists:proyecto,id_proyecto',
            'id_usuario' => 'nullable|integer|exists:usuario,id_usuario'
        ]);

        if (!PermisosHelper::puedeCrearTareas($request->id_proyecto)) {
            return $request->expectsJson() 
                ? response()->json(['success' => false, 'message' => 'Sin permisos'], 403)
                : redirect()->back()->with('error', 'No tienes permisos para crear tareas');
        }

        DB::beginTransaction();
        
        try {
            // Determinar usuario asignado
            $idUsuario = PermisosHelper::esParticipante($request->id_proyecto) 
                ? Auth::id() 
                : ($request->id_usuario ?: Auth::id());

            // IDs siguientes
            $idTarea = (DB::table('tareas')->max('id_tarea') ?? 0) + 1;
            $idEstado = (DB::table('estado_tarea')->max('id_estado') ?? 0) + 1;

            // Crear tarea
            DB::table('tareas')->insert([
                'id_tarea' => $idTarea,
                'nom_tarea' => $request->nom_tarea,
                'id_proyecto' => $request->id_proyecto,
                'id_usuario' => $idUsuario,
                'id_estados' => $idEstado,
                'fecha_creacion' => now(),
                'fecha_actualizacion' => now()
            ]);
            
            // Crear estado
            DB::table('estado_tarea')->insert([
                'id_estado' => $idEstado,
                'nom_estat' => 'Pendiente',
                'id_tarea' => $idTarea
            ]);
            
            $tarea = Tareas::find($idTarea);
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $request->expectsJson() 
                ? response()->json(['success' => false, 'message' => $e->getMessage()], 500)
                : redirect()->back()->with('error', 'Error al crear la tarea');
        }

        // Respuesta según tipo de petición
        return $request->expectsJson() 
            ? response()->json(['success' => true, 'message' => 'Tarea creada', 'tarea' => $tarea], 201)
            : redirect()->back()->with('success', 'Tarea creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tarea = Tareas::findOrFail($id);
        return response()->json($tarea);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tarea = Tareas::findOrFail($id);
        
        if (!PermisosHelper::puedeEditarTarea($tarea)) {
            return $request->expectsJson() 
                ? response()->json(['success' => false, 'message' => 'Sin permisos'], 403)
                : redirect()->back()->with('error', 'Sin permisos para editar');
        }

        $request->validate([
            'nom_tarea' => 'sometimes|required|string|max:255',
            'id_usuario' => 'sometimes|integer|exists:usuario,id_usuario'
        ]);

        if ($request->has('nom_tarea')) {
            $tarea->nom_tarea = $request->nom_tarea;
        }
        
        // Solo administrador puede reasignar
        if ($request->has('id_usuario') && PermisosHelper::esAdministrador($tarea->id_proyecto)) {
            $tarea->id_usuario = $request->id_usuario;
        }

        $tarea->save();

        return $request->expectsJson() 
            ? response()->json(['success' => true, 'message' => 'Tarea actualizada', 'tarea' => $tarea])
            : redirect()->back()->with('success', 'Tarea actualizada');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tarea = Tareas::findOrFail($id);
        
        if (!PermisosHelper::puedeEliminarTarea($tarea)) {
            return request()->expectsJson() 
                ? response()->json(['success' => false, 'message' => 'Sin permisos'], 403)
                : redirect()->back()->with('error', 'Sin permisos para eliminar');
        }

        DB::transaction(function() use ($tarea) {
            DB::table('estado_tarea')->where('id_tarea', $tarea->id_tarea)->delete();
            $tarea->delete();
        });

        return request()->expectsJson() 
            ? response()->json(['success' => true, 'message' => 'Tarea eliminada'])
            : redirect()->back()->with('success', 'Tarea eliminada');
    }
}
