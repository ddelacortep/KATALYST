<?php

namespace App\Http\Controllers;

use App\Models\Participar;
use Illuminate\Http\Request;

class ParticipacionController extends Controller
{
    private const ROL_SUPERADMIN = 3;
    private const ROL_ADMINISTRADOR = 1;

    public function store(Request $request, $proyectoId)
    {
        $this->ensureSuperAdmin($proyectoId);

        $data = $request->validate([
            'id_usuario' => 'required|exists:usuario,id_usuario',
            'id_rol' => 'required|exists:roles,id_rols|in:1,2', // Solo puede asignar Admin o Participante
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
        $this->ensureSuperAdmin($proyectoId);

        // No permitir eliminar al SuperAdmin
        $participacion = Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->first();

        if ($participacion && $participacion->id_rols == self::ROL_SUPERADMIN) {
            return request()->expectsJson()
                ? response()->json(['success' => false, 'message' => 'No se puede eliminar al SuperAdmin'], 403)
                : back()->with('error', 'No se puede eliminar al SuperAdmin del proyecto');
        }

        $deleted = Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->delete();

        $mensaje = $deleted ? 'Usuario eliminado del proyecto' : 'No se eliminó ninguna participación';

        return request()->expectsJson()
            ? response()->json(['success' => (bool) $deleted, 'message' => $mensaje])
            : back()->with($deleted ? 'success' : 'error', $mensaje);
    }

    public function updateRol(Request $request, $proyectoId, $usuarioId)
    {
        $this->ensureSuperAdmin($proyectoId);

        $data = $request->validate(['id_rol' => 'required|exists:roles,id_rols|in:1,2']); // Solo Admin o Participante

        // Buscar participación actual
        $participacion = Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->first();

        if (!$participacion) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'Usuario no encontrado en el proyecto'], 404)
                : back()->with('error', 'Usuario no encontrado en el proyecto');
        }

        // No permitir cambiar el rol del SuperAdmin
        if ($participacion->id_rols == self::ROL_SUPERADMIN) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'No se puede cambiar el rol del SuperAdmin'], 403)
                : back()->with('error', 'No se puede cambiar el rol del SuperAdmin');
        }

        // Actualizar el rol usando update() debido a la clave primaria compuesta
        Participar::where('id_proyecto', $proyectoId)
            ->where('id_usuario', $usuarioId)
            ->update(['id_rols' => (int) $data['id_rol']]);

        return $request->expectsJson()
            ? response()->json(['success' => true, 'message' => 'Rol actualizado correctamente'])
            : back()->with('success', 'Rol actualizado correctamente');
    }

    private function ensureSuperAdmin($proyectoId): void
    {
        abort_unless(session("user_projects.{$proyectoId}") === self::ROL_SUPERADMIN, 403, 'Solo el SuperAdmin puede realizar esta acción');
    }

    private function ensureAdmin($proyectoId): void
    {
        $rol = session("user_projects.{$proyectoId}");
        abort_unless($rol === self::ROL_SUPERADMIN || $rol === self::ROL_ADMINISTRADOR, 403, 'Sin permisos');
    }
}
