<?php

namespace App\Http\Controllers;

use App\Models\Participar;
use App\Helpers\PermisosHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParticipacionController extends Controller
{
    /**
     * Agregar un usuario a un proyecto
     */
    public function store(Request $request, $proyectoId)
    {
        if (!PermisosHelper::puedeGestionarUsuarios($proyectoId)) {
            return redirect()->back()->with('error', 'Sin permisos');
        }

        $request->validate([
            'id_usuario' => 'required|exists:usuario,id_usuario',
            'id_rol' => 'required|exists:roles,id_rols',
        ]);

        Participar::firstOrCreate(
            ['id_usuario' => $request->id_usuario, 'id_proyecto' => $proyectoId],
            ['id_rols' => $request->id_rol]
        );

        return redirect()->back()->with('success', 'Usuario agregado al proyecto');
    }

    /**
     * Eliminar un usuario de un proyecto
     */
    public function destroy($proyectoId, $usuarioId)
    {
        if (!PermisosHelper::puedeGestionarUsuarios($proyectoId)) {
            return redirect()->back()->with('error', 'Sin permisos');
        }

        Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->where('id_rols', '!=', PermisosHelper::ROL_ADMINISTRADOR)
            ->delete();

        return redirect()->back()->with('success', 'Usuario eliminado del proyecto');
    }

    /**
     * Actualizar el rol de un usuario en un proyecto
     */
    public function updateRol(Request $request, $proyectoId, $usuarioId)
    {
        if (!PermisosHelper::puedeGestionarUsuarios($proyectoId)) {
            return redirect()->back()->with('error', 'Sin permisos');
        }

        $request->validate(['id_rol' => 'required|exists:roles,id_rols']);

        Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->where('id_rols', '!=', PermisosHelper::ROL_ADMINISTRADOR)
            ->update(['id_rols' => $request->id_rol]);

        return redirect()->back()->with('success', 'Rol actualizado');
    }
}
