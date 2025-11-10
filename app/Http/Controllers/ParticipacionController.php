<?php

namespace App\Http\Controllers;

use App\Models\Participar;
use Illuminate\Http\Request;

class ParticipacionController extends Controller
{
    private const ROL_ADMINISTRADOR = 1;

    public function store(Request $request, $proyectoId)
    {
        $this->ensureAdmin($proyectoId);

        $data = $request->validate([
            'id_usuario' => 'required|exists:usuario,id_usuario',
            'id_rol' => 'required|exists:roles,id_rols',
        ]);

        Participar::firstOrCreate(
            ['id_usuario' => $data['id_usuario'], 'id_proyecto' => $proyectoId],
            ['id_rols' => $data['id_rol']]
        );

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => 'Usuario agregado al proyecto'], 201)
            : back()->with('success', 'Usuario agregado al proyecto');
    }

    public function destroy($proyectoId, $usuarioId)
    {
        $this->ensureAdmin($proyectoId);

        $deleted = Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->where('id_rols', '!=', self::ROL_ADMINISTRADOR)
            ->delete();

        $mensaje = $deleted ? 'Usuario eliminado del proyecto' : 'No se eliminó ninguna participación';

        return request()->expectsJson()
            ? response()->json(['success' => (bool) $deleted, 'message' => $mensaje])
            : back()->with($deleted ? 'success' : 'error', $mensaje);
    }

    public function updateRol(Request $request, $proyectoId, $usuarioId)
    {
        $this->ensureAdmin($proyectoId);

        $data = $request->validate(['id_rol' => 'required|exists:roles,id_rols']);

        $updated = Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->where('id_rols', '!=', self::ROL_ADMINISTRADOR)
            ->update(['id_rols' => $data['id_rol']]);

        $mensaje = $updated ? 'Rol actualizado' : 'No se realizó ninguna actualización';

        return $request->expectsJson()
            ? response()->json(['success' => (bool) $updated, 'message' => $mensaje])
            : back()->with($updated ? 'success' : 'info', $mensaje);
    }

    private function ensureAdmin($proyectoId): void
    {
        abort_unless(session("user_projects.{$proyectoId}") === self::ROL_ADMINISTRADOR, 403, 'Sin permisos');
    }
}
