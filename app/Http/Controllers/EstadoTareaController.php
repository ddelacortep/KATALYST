<?php

namespace App\Http\Controllers;

use App\Models\Tareas;
use App\Models\EstadoTarea;
use App\Models\Participar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstadoTareaController extends Controller
{
    /**
     * Actualizar el estado de una tarea
     */
    public function update(Request $request, $tareaId)
    {
        $tarea = Tareas::find($tareaId) ?: abort(404, 'Tarea no encontrada');

        // Permisos: comprobar siempre en la BD (tabla participar)
        $usuarioId = session('usuario_id') ?? Auth::id();
        $rol = Participar::where('id_proyecto', $tarea->id_proyecto)
            ->where('id_usuario', $usuarioId)
            ->value('id_rols');

        // Solo admin o propietario de la tarea pueden cambiar estado
        abort_unless($rol === 1 || $tarea->id_usuario == $usuarioId, 403, 'No tienes permisos para cambiar el estado de esta tarea');

        $data = $request->validate(['nom_estat' => 'required|in:Pendiente,En Progreso,Completada']);

        $estado = EstadoTarea::firstOrNew(['id_tarea' => $tareaId]);
        $estado->nom_estat = $data['nom_estat'];
        if (empty($estado->id_estado)) {
            $estado->id_estado = (EstadoTarea::max('id_estado') ?? 0) + 1;
        }
        $estado->save();

        return redirect()->back()->with('success', 'Estado actualizado correctamente');
    }
}
