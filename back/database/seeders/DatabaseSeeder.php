<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
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
            'logo'        => 'defaultImage.png',
            'imagen'      => 'defaultImage.png',
            'descripcion' => 'La mejor veterinaria de la ciudad.',
            'estado'      => 'Activo',
            'color'       => '#5A4EF9',
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
//        class Mascota extends Model
//        {
//            use SoftDeletes;
//
//            protected $table = 'mascotas';
//
//            protected $fillable = [
//                'nombre',
//                'apellido',
//                'especie',
//                'raza',
//                'sexo',
//                'fecha_nac',
//                'edad',
//                'senas_particulares',
//                'color',
//                'photo',
//
//                'propietario_nombre',
//                'propietario_ci',
//                'propietario_direccion',
//                'propietario_telefono',
//                'propietario_ciudad',
//                'propietario_celular',
//
//                'veterinaria_id',
//            ];
//        crear 10000 mascotas fake
        \App\Models\Mascota::factory(1)->create([
            'veterinaria_id' => $veterinaria->id,
        ]);
//        productos_202512230511.sql
        $productosSqlPath = database_path('seeders/productos_202512230511.sql');
        DB::unprepared(file_get_contents($productosSqlPath));

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
