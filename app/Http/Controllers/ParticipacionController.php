<?php

namespace App\Http\Controllers;

use App\Models\Participar;
use App\Helpers\PermisosHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipacionController extends Controller
{
    /**
     * Agregar un usuario a un proyecto
     */
    public function store(Request $request, $proyectoId)
    {
        // Verificar permisos
        if (!PermisosHelper::puedeGestionarUsuarios($proyectoId)) {
            return redirect()->back()->with('error', 'No tienes permisos para gestionar usuarios');
        }

        $request->validate([
            'id_usuario' => 'required|exists:usuario,id_usuario',
            'id_rol' => 'required|exists:roles,id_rols',
        ]);

        // Verificar si el usuario ya está en el proyecto
        $existe = Participar::where('id_usuario', $request->id_usuario)
            ->where('id_proyecto', $proyectoId)
            ->exists();

        if ($existe) {
            return redirect()->back()->with('error', 'El usuario ya participa en este proyecto');
        }

        try {
            $participar = new Participar();
            $participar->id_usuario = $request->id_usuario;
            $participar->id_proyecto = $proyectoId;
            $participar->id_rols = $request->id_rol;
            $participar->save();

            return redirect()->back()->with('success', 'Usuario agregado al proyecto correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al agregar usuario: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un usuario de un proyecto
     */
    public function destroy($proyectoId, $usuarioId)
    {
        // Verificar permisos
        if (!PermisosHelper::puedeGestionarUsuarios($proyectoId)) {
            return redirect()->back()->with('error', 'No tienes permisos para gestionar usuarios');
        }

        // Verificar que el usuario a eliminar no sea administrador
        $participacion = Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->first();

        if (!$participacion) {
            return redirect()->back()->with('error', 'El usuario no participa en este proyecto');
        }

        if ($participacion->id_rols == PermisosHelper::ROL_ADMINISTRADOR) {
            return redirect()->back()->with('error', 'No se puede eliminar al administrador del proyecto');
        }

        try {
            $participacion->delete();
            return redirect()->back()->with('success', 'Usuario eliminado del proyecto');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar el rol de un usuario en un proyecto
     */
    public function updateRol(Request $request, $proyectoId, $usuarioId)
    {
        // Verificar permisos
        if (!PermisosHelper::puedeGestionarUsuarios($proyectoId)) {
            return redirect()->back()->with('error', 'No tienes permisos para gestionar usuarios');
        }

        $request->validate([
            'id_rol' => 'required|exists:roles,id_rols',
        ]);

        // Verificar que no se intente cambiar el rol del administrador
        $participacion = Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->first();

        if (!$participacion) {
            return redirect()->back()->with('error', 'El usuario no participa en este proyecto');
        }

        if ($participacion->id_rols == PermisosHelper::ROL_ADMINISTRADOR) {
            return redirect()->back()->with('error', 'No se puede cambiar el rol del administrador del proyecto');
        }

        try {
            $participacion->id_rols = $request->id_rol;
            $participacion->save();

            return redirect()->back()->with('success', 'Rol actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el rol: ' . $e->getMessage());
        }
    }
}
