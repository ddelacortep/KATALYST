<?php

namespace App\Http\Controllers;

use App\Models\Tareas;
use App\Models\EstadoTarea;
use Illuminate\Http\Request;

class EstadoTareaController extends Controller
{
    public function update(Request $request, $tareaId)
    {
        $tarea = Tareas::findOrFail($tareaId);
        
        // Verificar permisos usando sesión
        if (!$tarea->puedeEditar()) {
            return back()->with('error', 'Sin permisos para cambiar el estado');
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

        return back()->with('success', 'Estado actualizado');
    }
}
