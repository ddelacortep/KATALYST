<?php

namespace App\Http\Controllers;

use App\Models\Rols;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Rols::all();
        
        return request()->expectsJson() 
            ? response()->json($roles)
            : view('roles.index', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['nom_rols' => 'required|string|max:255']);

        $rol = Rols::create([
            'id_rols' => DB::table('roles')->max('id_rols') + 1,
            'nom_rols' => $request->nom_rols
        ]);

        return $request->expectsJson() 
            ? response()->json(['success' => true, 'message' => 'Rol creado', 'rol' => $rol], 201)
            : redirect()->back()->with('success', 'Rol creado');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(Rols::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate(['nom_rols' => 'required|string|max:255']);
        
        $rol = Rols::findOrFail($id);
        $rol->update(['nom_rols' => $request->nom_rols]);

        return $request->expectsJson() 
            ? response()->json(['success' => true, 'message' => 'Rol actualizado', 'rol' => $rol])
            : redirect()->back()->with('success', 'Rol actualizado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Rols::findOrFail($id)->delete();

        return request()->expectsJson() 
            ? response()->json(['success' => true, 'message' => 'Rol eliminado'])
            : redirect()->back()->with('success', 'Rol eliminado');
    }
}
