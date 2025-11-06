<?php

namespace App\Http\Controllers;

use App\Models\Tareas;
use App\Models\EstadoTarea;
use App\Helpers\PermisosHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstadoTareaController extends Controller
{
    /**
     * Actualizar el estado de una tarea
     */
    public function update(Request $request, $tareaId)
    {
        $tarea = Tareas::findOrFail($tareaId);
        
        // Verificar permisos
        if (!PermisosHelper::esAdministrador($tarea->id_proyecto) && $tarea->id_usuario != Auth::id()) {
            return redirect()->back()->with('error', 'Sin permisos para cambiar el estado');
        }

        $request->validate([
            'nom_estat' => 'required|in:Pendiente,En Progreso,Completada'
        ]);

        EstadoTarea::updateOrCreate(
            ['id_tarea' => $tareaId],
            [
                'id_estado' => EstadoTarea::max('id_estado') + 1,
                'nom_estat' => $request->nom_estat
            ]
        );

        return redirect()->back()->with('success', 'Estado actualizado');
    }
}
