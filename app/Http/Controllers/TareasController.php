<?php

namespace App\Http\Controllers;

use App\Models\Tareas;
use App\Helpers\PermisosHelper;
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
            'id_proyecto' => 'required|integer|exists:proyecto,id_proyecto',
            'id_usuario' => 'nullable|integer|exists:usuario,id_usuario'
        ]);

        // Verificar permisos para crear tareas
        if (!PermisosHelper::puedeCrearTareas($request->id_proyecto)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para crear tareas en este proyecto'
                ], 403);
            }
            return redirect()->back()->with('error', 'No tienes permisos para crear tareas en este proyecto');
        }

        // Usar transacción para crear tarea y estado juntos
        DB::beginTransaction();
        
        try {
            // Obtener el siguiente ID disponible para tarea
            $nextTareaId = (DB::table('tareas')->max('id_tarea') ?? 0) + 1;
            
            // Obtener el siguiente ID disponible para estado
            $nextEstadoId = (DB::table('estado_tarea')->max('id_estado') ?? 0) + 1;
            
            // Crear el estado primero
            DB::table('estado_tarea')->insert([
                'id_estado' => $nextEstadoId,
                'nom_estat' => 'Pendiente',
                'id_tarea' => $nextTareaId
            ]);

            // Crear la tarea
            $tarea = new Tareas();
            $tarea->id_tarea = $nextTareaId;
            $tarea->nom_tarea = $request->nom_tarea;
            $tarea->id_proyecto = $request->id_proyecto;
            $tarea->id_estados = $nextEstadoId;
            
            // Obtener el ID del usuario de la sesión
            $usuarioId = Session::get('usuario_id');
            
            // Validar que el usuario esté en sesión
            if (!$usuarioId) {
                throw new \Exception('No se pudo obtener el ID del usuario de la sesión. Por favor, inicia sesión nuevamente.');
            }
            
            // Obtener el id_usuario del request (puede venir como string vacío)
            $idUsuarioRequest = $request->id_usuario;
            
            // Si es editor, solo puede asignarse tareas a sí mismo
            if (PermisosHelper::esEditor($request->id_proyecto, $usuarioId)) {
                $tarea->id_usuario = $usuarioId;
            } else {
                // Administrador: si no selecciona usuario o selecciona "Sin asignar", asignar a sí mismo
                if (empty($idUsuarioRequest)) {
                    $tarea->id_usuario = $usuarioId;
                } else {
                    $tarea->id_usuario = $idUsuarioRequest;
                }
            }
            
            $tarea->save();
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la tarea: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error al crear la tarea');
        }

        // Si es una petición AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tarea creada correctamente',
                'tarea' => $tarea
            ], 201);
        }

        // Si es una petición normal
        return redirect()->back()->with('success', 'Tarea creada correctamente');
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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tarea no encontrada'
                ], 404);
            }
            return redirect()->back()->with('error', 'Tarea no encontrada');
        }

        // Verificar permisos para editar
        if (!PermisosHelper::puedeEditarTarea($tarea)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para editar esta tarea'
                ], 403);
            }
            return redirect()->back()->with('error', 'No tienes permisos para editar esta tarea');
        }

        $request->validate([
            'nom_tarea' => 'sometimes|required|string|max:255',
            'id_usuario' => 'sometimes|integer|exists:usuario,id_usuario'
        ]);

        if ($request->has('nom_tarea')) {
            $tarea->nom_tarea = $request->nom_tarea;
        }
        
        // Solo administrador puede reasignar tareas
        if ($request->has('id_usuario') && PermisosHelper::esAdministrador($tarea->id_proyecto)) {
            $tarea->id_usuario = $request->id_usuario;
        }

        $tarea->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tarea actualizada correctamente',
                'tarea' => $tarea
            ]);
        }

        return redirect()->back()->with('success', 'Tarea actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tarea = Tareas::find($id);
        
        if (!$tarea) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tarea no encontrada'
                ], 404);
            }
            return redirect()->back()->with('error', 'Tarea no encontrada');
        }

        // Verificar permisos para eliminar
        if (!PermisosHelper::puedeEliminarTarea($tarea->id_proyecto)) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo el administrador puede eliminar tareas'
                ], 403);
            }
            return redirect()->back()->with('error', 'Solo el administrador puede eliminar tareas');
        }

        // Eliminar tarea y su estado asociado en una transacción
        DB::beginTransaction();
        
        try {
            // Eliminar el estado asociado
            DB::table('estado_tarea')->where('id_tarea', $tarea->id_tarea)->delete();
            
            // Eliminar la tarea
            $tarea->delete();
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la tarea: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error al eliminar la tarea');
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tarea eliminada correctamente'
            ]);
        }

        return redirect()->back()->with('success', 'Tarea eliminada correctamente');
    }
}
