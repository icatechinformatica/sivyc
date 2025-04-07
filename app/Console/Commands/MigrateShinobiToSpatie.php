<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateShinobiToSpatie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:shinobi-to-spatie';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from Shinobi to Spatie Permissions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $shinobiPermissions = DB::table('permissions_')->get();

       foreach($shinobiPermissions as $permission) {
	 DB::table('permissions')->insert([
	   'id' => $permission->id,
	   'name'=> $permission->slug,
	   'guard_name' => 'web',
	 ]);
       }
      $this->info('Permissions Added');

     //migrate roles
      $shinobiRoles = DB::table('roles_')->get();
      foreach($shinobiRoles as $rol){
	DB::table('roles')->insert([
	 'id' => $rol->id,
	 'name' => $rol->name,
	 'guard_name' => 'web',
	]);
      }
      $this->info('roles asignados');

      $shinobiPermissionRole = DB::table('permission_role')->get();
      foreach($shinobiPermissionRole as $rolePermission){
	$exist = DB::table('role_has_permissions')
	 	->where('permission_id', $rolePermission->permission_id)
		->where('role_id', $rolePermission->role_id)
		->exists();
	if(!$exist){
	  DB::table('role_has_permissions')->insert([
		'permission_id' => $rolePermission->permission_id,
		'role_id' => $rolePermission->role_id,
	  ]);
	}
      }

      $this->info('roles and Permissions assigned');


      $shinobiModelRoles = DB::table('role_user')->get();
      foreach($shinobiModelRoles as $modelRole){
	$existModelRol = DB::table('model_has_roles')
		->where('model_id', $modelRole->user_id)
		->where('role_id', $modelRole->role_id)
		->exists();

	if(!$existModelRol){
		DB::table('model_has_roles')->insert([
		   'role_id' => $modelRole->role_id,
		   'model_type' => 'App\Models\User',
		   'model_id' => $modelRole->user_id,
		]);
	}
      }

     $this->info('Roles assigned to migrated users');


   	$shinobiModelPermissions = DB::table('permission_user')->get();
	foreach($shinobiModelPermissions as $modelPermission){
		$existsModelPermission = DB::table('model_has_permissions')
				->where('permission_id', $modelPermission->permission_id)
				->where('model_id', $modelPermission->user_id)
				->exists();

		if(!$existsModelPermission){
			DB::table('model_has_permissions')->insert([
				'permission_id' => $modelPermission->permission_id,
				'model_type' => 'App\Models\User',
				'model_id' => $modelPermission->user_id,
			]);
		}
	}

	$this->info('permissions assigned to users');

    }
}
