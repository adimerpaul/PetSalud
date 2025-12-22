<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {

        $veterinaria = \App\Models\Veterinaria::create([
            'nombre'      => 'Veterinaria Central',
            'direccion'   => 'Calle Principal 123',
            'telefono'    => '555-1234',
            'email'       => 'veteriaria@gmail.com',
            'logo'        => 'defaultLogo.png',
            'imagen'      => 'defaultImage.png',
            'descripcion' => 'La mejor veterinaria de la ciudad.',
            'estado'      => 'Activo',
            'color'       => '#FF5733',
        ]);

        $userAdmin = User::create([
            'name'     => 'Admin User',
            'username' => 'admin',
            'role'     => 'Administrador',
            'avatar'   => 'defaultAvatar.png',
            'email'    => '',
            'password' => bcrypt('admin123Admin'),
            'veterinaria_id' => $veterinaria->id,
        ]);

        // --- Permisos básicos
//        $permisos = [
//            'Usuarios',
//            'Insumos',
//            'Productos',
//            'Clientes',
//            'Ventas',
//            'Compras',
//            'Reportes',
//        ];
//        foreach ($permisos as $permiso) {
//            Permission::firstOrCreate(['name' => $permiso]);
//        }
//        $userAdmin->givePermissionTo(Permission::all());
    }
}
