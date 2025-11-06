<?php

namespace App\Http\Controllers;

use App\Models\Participar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParticipacionController extends Controller
{
    // Constante local para simplificar referencias a rol administrador
    private const ROL_ADMINISTRADOR = 1;
    /**
     * Agregar un usuario a un proyecto
     */
    public function store(Request $request, $proyectoId)
    {
        $this->ensureAdmin($proyectoId);

        $data = $request->validate([
            'id_usuario' => 'required|exists:usuario,id_usuario',
            'id_rol' => 'required|exists:roles,id_rols',
        ]);

        $participacion = Participar::firstOrCreate(
            ['id_usuario' => $data['id_usuario'], 'id_proyecto' => $proyectoId],
            ['id_rols' => $data['id_rol']]
        );

        return response()->json([
            'success' => true,
            'message' => 'Usuario agregado al proyecto',
            'data' => $participacion,
        ], 201);
    }

    /**
     * Eliminar un usuario de un proyecto
     */
    public function destroy($proyectoId, $usuarioId)
    {
        $this->ensureAdmin($proyectoId);

        $deleted = Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->where('id_rols', '!=', self::ROL_ADMINISTRADOR)
            ->delete();

        return response()->json([
            'success' => (bool) $deleted,
            'message' => $deleted ? 'Usuario eliminado del proyecto' : 'No se eliminó ninguna participación',
        ]);
    }

    /**
     * Actualizar el rol de un usuario en un proyecto
     */
    public function updateRol(Request $request, $proyectoId, $usuarioId)
    {
        $this->ensureAdmin($proyectoId);

        $data = $request->validate(['id_rol' => 'required|exists:roles,id_rols']);

        $updated = Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->where('id_rols', '!=', self::ROL_ADMINISTRADOR)
            ->update(['id_rols' => $data['id_rol']]);

        return response()->json([
            'success' => (bool) $updated,
            'message' => $updated ? 'Rol actualizado' : 'No se realizó ninguna actualización',
        ]);
    }
    /**
     * Obtener el rol de un usuario en un proyecto consultando la BD.
     * Si no se pasa $userId, devuelve el rol del usuario autenticado.
     */
    private function roleInProject($proyectoId, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        return Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $userId)
            ->value('id_rols');
    }

    /**
     * Abort 403 unless the authenticated user is admin in the project.
     */
    private function ensureAdmin($proyectoId): void
    {
        abort_unless($this->roleInProject($proyectoId) === self::ROL_ADMINISTRADOR, 403, 'Sin permisos');
    }

}
