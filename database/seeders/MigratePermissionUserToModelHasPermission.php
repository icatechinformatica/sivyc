<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigratePermissionUserToModelHasPermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('model_has_permissions')->truncate();
        $permissionuserData = DB::table('permission_user')->get();

        foreach ($permissionuserData as $item) {
            DB::table('model_has_permissions')->updateOrInsert(
                [
                    'permission_id' => $item->permission_id,
                    'model_id' => $item->user_id,
                    'model_type' => 'App\\User'
                ],
                [
                    'permission_id' => $item->permission_id,
                    'model_id' => $item->user_id,
                    'model_type' => 'App\\User'
                ]
            );
        }
    }
}
