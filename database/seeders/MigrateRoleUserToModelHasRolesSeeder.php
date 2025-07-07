<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrateRoleUserToModelHasRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('model_has_roles')->truncate();
        $roleUserData = DB::table('role_user')->get();

        foreach ($roleUserData as $item) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => $item->role_id,
                    'model_id' => $item->user_id,
                    'model_type' => 'App\\User'
                ],
                [
                    'role_id' => $item->role_id,
                    'model_id' => $item->user_id,
                    'model_type' => 'App\\User'
                ]
            );
        }
    }
}
