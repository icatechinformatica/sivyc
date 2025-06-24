<?php

use Illuminate\Database\Seeder;
use Database\Seeders\RolesTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // PermissionsTableSeeder::class,
            // RoleTableSeeder::class
            //CalendarioFormatotSeeder::class,
            // RolesTableSeeder::class,
            RolesTableSeeder::class,
        ]);
    }
}
