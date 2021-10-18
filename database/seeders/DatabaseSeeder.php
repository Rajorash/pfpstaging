<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
            // RoleSeeder::class, // RoleSeeder called on table creation during migration now
            UserSeeder::class,
            LicenseSeeder::class,
            BusinessSeeder::class,
            // AccountSeeder::class,
            // PermissionsSeeder::class // PermissionsSeeder called on table creation during migration now
        ]);
    }
}
