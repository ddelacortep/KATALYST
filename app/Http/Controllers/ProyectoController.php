<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Participar;
use App\Models\Rols;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $proyectos = Proyecto::all();
        return view('proyectos', compact('proyectos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar que el usuario esté autenticado
        if (!Session::has('usuario_id')) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para crear un proyecto.');
        }

        // Obtener el ID del usuario autenticado
        $usuarioAutenticadoId = Session::get('usuario_id');

        // Obtener el siguiente ID disponible
        $nextId = DB::table('proyecto')->max('id_proyecto') + 1;
        
        $proyecto = new Proyecto();
        $proyecto->id_proyecto = $nextId;
        $proyecto->nom_proyecto = $request->input('nombre_del_proyecto');
        
        $proyecto->save();
        
        // Obtener el primer rol disponible o crear uno por defecto
        $rol = Rols::first();
        
        if (!$rol) {
            // Si no existe ningún rol, crear uno por defecto
            $rol = new Rols();
            $rol->id_rols = 1;
            $rol->nom_rols = 'Administrador';
            $rol->save();
        }
        
        // Asignar automáticamente el usuario autenticado como creador del proyecto
        $participar = new Participar();
        $participar->id_usuario = $usuarioAutenticadoId;
        $participar->id_proyecto = $proyecto->id_proyecto;
        $participar->id_rols = $rol->id_rols;
        $participar->save();
        
        // Obtener o crear colaboradores adicionales solo si se proporcionó
        $usuarioInput = $request->input('usuario');
        
        // Solo procesar colaboradores adicionales si se proporcionó un valor
        if (!empty($usuarioInput) && $usuarioInput != $usuarioAutenticadoId) {
            // Intentar buscar el usuario por ID o por nombre
            $usuario = Usuario::where('id_usuario', $usuarioInput)
                             ->orWhere('nom_usuario', $usuarioInput)
                             ->first();
            
            // Si existe el usuario, agregarlo como colaborador
            if ($usuario) {
                $participarColaborador = new Participar();
                $participarColaborador->id_usuario = $usuario->id_usuario;
                $participarColaborador->id_proyecto = $proyecto->id_proyecto;
                $participarColaborador->id_rols = $rol->id_rols;
                $participarColaborador->save();
            }
        }

        return redirect()->route('proyectos')->with('success', 'Proyecto creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Proyecto $proyecto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proyecto $proyecto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proyecto $proyecto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Primero eliminar las relaciones en la tabla participar
            DB::table('participar')->where('id_proyecto', $id)->delete();
            
            // Luego eliminar las tareas asociadas al proyecto (si existen)
            DB::table('tareas')->where('id_proyecto', $id)->delete();
            
            // Finalmente eliminar el proyecto
            DB::table('proyecto')->where('id_proyecto', $id)->delete();
            
            return redirect()->route('proyectos')->with('success', 'Proyecto eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('proyectos')->with('error', 'Error al eliminar el proyecto: ' . $e->getMessage());
        }
    }
}
