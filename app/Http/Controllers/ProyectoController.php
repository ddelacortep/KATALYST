<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Participar;
use App\Models\Rols;
use App\Models\Usuario;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    public function index()
    {
        return view('proyectos', ['proyectos' => Proyecto::all()]);
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_del_proyecto' => 'required|string|max:255',
        ]);

        try {
            $proyecto = Proyecto::create([
                'id_proyecto' => Proyecto::max('id_proyecto') + 1,
                'nom_proyecto' => $request->nombre_del_proyecto,
                'slug' => Proyecto::generateSlug($request->nombre_del_proyecto)
            ]);
            
            // Creador del proyecto es SuperAdmin (rol 3)
            Participar::create([
                'id_usuario' => session('user_id'),
                'id_proyecto' => $proyecto->id_proyecto,
                'id_rols' => 3 // SuperAdmin
            ]);
            
            // Actualizar sesión con el nuevo proyecto como SuperAdmin
            session(["user_projects.{$proyecto->id_proyecto}" => 3]);
            session()->push('accessible_projects', $proyecto->id_proyecto);

            return redirect()->route('proyectos')->with('success', 'Proyecto creado correctamente');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear el proyecto: ' . $e->getMessage());
        }
    }

    public function show(Proyecto $proyecto)
    {
        // Verificar acceso usando sesión
        if (!in_array($proyecto->id_proyecto, session('accessible_projects', []))) {
            return redirect()->route('proyectos')->with('error', 'No tienes acceso a este proyecto');
        }

        $proyecto->load(['tareas.usuario', 'participar.usuario', 'participar.rol']);
        
        $rolUsuario = session("user_projects.{$proyecto->id_proyecto}");
        
        return view('proyecto.show', [
            'proyecto' => $proyecto,
            'permisos' => [
                'puede_crear_tareas' => $rolUsuario !== null,
                'puede_gestionar_usuarios' => $rolUsuario == 3 || $rolUsuario == 1,
                'es_administrador' => $rolUsuario == 1,
                'es_participante' => $rolUsuario == 2,
                'es_superadmin' => $rolUsuario == 3,
                'rol_actual' => $rolUsuario
            ],
            'todosUsuarios' => Usuario::all(),
            'roles' => Rols::cached()
        ]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy(Proyecto $proyecto)
    {
        if (session("user_projects.{$proyecto->id_proyecto}") != 3) {
            return redirect()->route('proyectos')->with('error', 'Solo el SuperAdmin puede eliminar el proyecto');
        }

        $proyecto->delete();
        
        // Limpiar de sesión
        session()->forget("user_projects.{$proyecto->id_proyecto}");
        $accessible = session('accessible_projects', []);
        session(['accessible_projects' => array_values(array_diff($accessible, [$proyecto->id_proyecto]))]);
        
        return redirect()->route('proyectos')->with('success', 'Proyecto eliminado correctamente');
    }
}