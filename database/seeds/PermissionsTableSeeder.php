<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Permission::create([
            'name' => 'Navegar indice',
            'slug' => 'home.index',
            'description' => 'Indice para todos los usuarios del sistema'
        ]);
        // alumnos
        Permission::create([
            'name' => 'Ver detalles del alumno',
            'slug' => 'alumnos.index',
            'description' => 'Ver detalle de cada alumnos inscrito en el sistema'
        ]);
        Permission::create([
            'name' => 'inscripcion del alumno',
            'slug' => 'alumnos.inscripcion-paso1',
            'description' => 'Inscripción de los alumnos del sistema'
        ]);
        Permission::create([
            'name' => 'Eliminar Alumno',
            'slug' => 'alumnos.destroy',
            'description' => 'Eliminar cualquier Alumno del Sistema'
        ]);
        // contratos
        Permission::create([
            'name' => 'Ver detalles del contrato',
            'slug' => 'contratos.index',
            'description' => 'Ver detalle de cada contratos en el sistema'
        ]);
        Permission::create([
            'name' => 'agregar contrato',
            'slug' => 'contratos.create',
            'description' => 'crear nuevo contrato del sistema'
        ]);
        Permission::create([
            'name' => 'Eliminar contrato',
            'slug' => 'contratos.destroy',
            'description' => 'Eliminar contrato en el sistema'
        ]);
        // pagos
        Permission::create([
            'name' => 'Ver detalle de los pagos',
            'slug' => 'pagos.inicio',
            'description' => 'Ver detalle de cada pagos en el sistema'
        ]);
        Permission::create([
            'name' => 'crear pago',
            'slug' => 'pagos.create',
            'description' => 'agregar pagos en el sistema'
        ]);
        Permission::create([
            'name' => 'Eliminar Registro de pago',
            'slug' => 'pagos.destroy',
            'description' => 'Eliminar cualquier pago del sistema'
        ]);
        // cursos
        Permission::create([
            'name' => 'Ver detalles de los cursos',
            'slug' => 'cursos.index',
            'description' => 'Ver detalle de cada cursos en el sistema'
        ]);
        Permission::create([
            'name' => 'inscripcion del alumno',
            'slug' => 'cursos.create',
            'description' => 'Inscripción de los cursos del sistema'
        ]);
        Permission::create([
            'name' => 'Eliminar Alumno',
            'slug' => 'cursos.destroy',
            'description' => 'Eliminar cualquier Alumno del Sistema'
        ]);
        // instructor
        Permission::create([
            'name' => 'Ver detalles de los instructores',
            'slug' => 'instructor.index',
            'description' => 'Ver detalle de cada instructor en el sistema'
        ]);
        Permission::create([
            'name' => 'inscripcion del alumno',
            'slug' => 'instructor.create',
            'description' => 'creación de los instructores del sistema'
        ]);
        Permission::create([
            'name' => 'Eliminar instructor',
            'slug' => 'instructor.destroy',
            'description' => 'Eliminar instructores del sistema'
        ]);
        // convenios
        Permission::create([
            'name' => 'Ver detalles de los convenios',
            'slug' => 'convenios.index',
            'description' => 'Ver detalle de cada convenios en el sistema'
        ]);
        Permission::create([
            'name' => 'creacion de inscripcion',
            'slug' => 'convenios.create',
            'description' => 'creacion de los convenios del sistema'
        ]);
        Permission::create([
            'name' => 'Eliminar Alumno',
            'slug' => 'convenios.destroy',
            'description' => 'Eliminar cualquier convenio del Sistema'
        ]);
        // supre
        Permission::create([
            'name' => 'ver Detalles del supre',
            'slug' => 'supre.index',
            'description' => 'Ver detalle de cada alumnos inscrito en el sistema'
        ]);
        Permission::create([
            'name' => 'creacion de supre',
            'slug' => 'supre.create',
            'description' => 'Inscripción de los alumnos del sistema'
        ]);
        Permission::create([
            'name' => 'eliminar supre',
            'slug' => 'supre.destroy',
            'description' => 'Eliminar registro supre del Sistema'
        ]);

        //DB::table('permissions')->insert($TiposEncuesta);
    }
}
