<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen roles
        $existingRoles = DB::table('roles')->count();
        
        if ($existingRoles == 0) {
            // Crear roles predeterminados
            DB::table('roles')->insert([
                [
                    'id_rols' => 1,
                    'nom_rols' => 'Administrador'
                ],
                [
                    'id_rols' => 2,
                    'nom_rols' => 'Editor'
                ],
                [
                    'id_rols' => 3,
                    'nom_rols' => 'Visualizador'
                ]
            ]);
            
            echo "Roles creados correctamente.\n";
        } else {
            echo "Los roles ya existen en la base de datos.\n";
        }
    }
}
