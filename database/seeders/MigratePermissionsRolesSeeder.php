<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigratePermissionsRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role_has_permissions')->truncate();

        $permissionRoles = DB::table('permission_role')->get();

        foreach ($permissionRoles as $item) {
            DB::table('role_has_permissions')->updateOrInsert(
                [
                    'permission_id' => $item->permission_id,
                    'role_id' => $item->role_id
                ],
                [
                    'permission_id' => $item->permission_id,
                    'role_id' => $item->role_id
                ]
            );
        }

    }
}
