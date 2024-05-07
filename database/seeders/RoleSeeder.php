<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::firstOrNew(['name' => 'super_admin']);
        if (!$role->exists) {
            $role->fill([
                'display_name' => 'Super Admin',
            ])->save();
        }

        $role = Role::firstOrNew(['name' => 'admin']);
        if (!$role->exists) {
            $role->fill([
                'display_name' => 'Admin',
            ])->save();
        }

        $role = Role::firstOrNew(['name' => Role::ASSESSOR]);
        if (!$role->exists) {
            $role->fill([
                'display_name' => 'Asesor',
            ])->save();
        }

        $role = Role::firstOrNew(['name' => Role::ASSESSEE]);
        if (!$role->exists) {
            $role->fill([
                'display_name' => 'Asesi',
            ])->save();
        }
    }
}
