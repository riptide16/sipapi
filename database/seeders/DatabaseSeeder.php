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
            RoleSeeder::class,
            MenuSeeder::class,
            PermissionSeeder::class,
            PermissionRoleSeeder::class,
            InstrumentSeeder::class,
            EmailTemplateSeeder::class,
            PublicMenuSeeder::class,
            FileDownloadSeeder::class,
        ]);
    }
}
