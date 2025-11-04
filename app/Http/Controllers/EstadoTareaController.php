<?php

namespace App\Http\Controllers;

use App\Models\Tareas;
use App\Models\EstadoTarea;
use App\Helpers\PermisosHelper;
use Illuminate\Http\Request;

class EstadoTareaController extends Controller
{
    /**
     * Actualizar el estado de una tarea
     */
    public function update(Request $request, $tareaId)
    {
        $tarea = Tareas::find($tareaId);
        
        if (!$tarea) {
            return redirect()->back()->with('error', 'Tarea no encontrada');
        }

        // Verificar permisos para cambiar el estado
        // El administrador puede cambiar cualquier estado
        // El participante solo puede cambiar el estado de sus propias tareas
        $usuarioId = session('usuario_id');
        $esAdmin = PermisosHelper::esAdministrador($tarea->id_proyecto, $usuarioId);
        $esMiTarea = $tarea->id_usuario == $usuarioId;

        if (!$esAdmin && !$esMiTarea) {
            return redirect()->back()->with('error', 'No tienes permisos para cambiar el estado de esta tarea');
        }

        $request->validate([
            'nom_estat' => 'required|in:Pendiente,En Progreso,Completada'
        ]);

        // Obtener o crear el estado
        $estado = EstadoTarea::where('id_tarea', $tareaId)->first();
        
        if ($estado) {
            $estado->nom_estat = $request->nom_estat;
            $estado->save();
        } else {
            // Si no existe, crear el estado
            $nextEstadoId = (EstadoTarea::max('id_estado') ?? 0) + 1;
            EstadoTarea::create([
                'id_estado' => $nextEstadoId,
                'nom_estat' => $request->nom_estat,
                'id_tarea' => $tareaId
            ]);
        }

        return redirect()->back()->with('success', 'Estado actualizado correctamente');
    }
}
