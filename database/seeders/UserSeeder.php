<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superAdmin = Role::where('name', 'super_admin')->firstOrFail();
        $user = User::firstOrNew(['email' => 'superadmin@evalatore.com']);
        if (!$user->exists) {
            $user->fill([
                'password' => 'password',
                'username' => 'super_admin',
                'name' => 'Super Admin',
                'role_id' => $superAdmin->id,
            ])->save();
        }

        $admin = Role::where('name', 'admin')->firstOrFail();
        $user = User::firstOrNew(['email' => 'admin@evalatore.com']);
        if (!$user->exists) {
            $user->fill([
                'password' => 'password',
                'username' => 'admin',
                'name' => 'Admin',
                'role_id' => $admin->id,
            ])->save();
        }

        $assessor = Role::where('name', 'asesor')->firstOrFail();
        $user = User::firstOrNew(['email' => 'assessor@evalatore.com']);
        if (!$user->exists) {
            $user->fill([
                'password' => 'password',
                'username' => 'assessor',
                'name' => 'Assessor',
                'role_id' => $assessor->id,
            ])->save();
        }

        $assessee = Role::where('name', 'asesi')->firstOrFail();
        $user = User::firstOrNew(['email' => 'assessee@evalatore.com']);
        if (!$user->exists) {
            $user->fill([
                'password' => 'password',
                'username' => 'assessee',
                'name' => 'Assessee',
                'role_id' => $assessee->id,
            ])->save();
        }
    }
}
