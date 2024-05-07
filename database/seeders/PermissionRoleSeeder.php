<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = $this->superAdminPermissions();
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->permissions()->sync($permissions->pluck('id')->toArray());
        }

        $permissions = $this->adminPermissions();
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->permissions()->sync($permissions->pluck('id')->toArray());
        }

        $permissions = $this->assessorPermissions();
        $assessor = Role::where('name', Role::ASSESSOR)->first();
        if ($assessor) {
            $assessor->permissions()->sync($permissions->pluck('id')->toArray());
        }

        $permissions = $this->assesseePermissions();
        $assessee = Role::where('name', ROLE::ASSESSEE)->first();
        if ($assessee) {
            $assessee->permissions()->sync($permissions->pluck('id')->toArray());
        }

    }

    private function superAdminPermissions()
    {
        $keys = [
            'browse_users',
            'read_users',
            'edit_users',
            'add_users',
            'delete_users',
            'browse_institutions',
            'read_institutions',
            'verify_institutions',
            'browse_master_data',
            'read_master_data',
            'edit_master_data',
            'add_master_data',
            'delete_master_data',
            'browse_instruments',
            'read_instruments',
            'edit_instruments',
            'add_instruments',
            'delete_instruments',
            'process_instruments',
            'browse_instrument_categories',
            'read_instrument_categories',
            'edit_instrument_categories',
            'add_instrument_categories',
            'delete_instrument_categories',
            'process_instrument_categories',
            'browse_accreditations',
            'read_accreditations',
            'add_accreditations',
            'recap_accreditations',
            'verify_accreditations',
            'process_accreditations',
            'browse_certifications',
            'read_certifications',
            'edit_certifications',
            'browse_reports',
            'read_reports',
            'browse_contents',
            'read_contents',
            'add_contents',
            'edit_contents',
            'delete_contents',
            'browse_banners',
            'read_banners',
            'add_banners',
            'edit_banners',
            'delete_banners',
            'browse_testimonies',
            'read_testimonies',
            'add_testimonies',
            'edit_testimonies',
            'delete_testimonies',
            'browse_galleries',
            'read_galleries',
            'add_galleries',
            'edit_galleries',
            'delete_galleries',
            'browse_videos',
            'read_videos',
            'add_videos',
            'edit_videos',
            'delete_videos',
            'browse_faqs',
            'read_faqs',
            'add_faqs',
            'edit_faqs',
            'delete_faqs',
            'browse_file_downloads',
            'read_file_downloads',
            'add_file_downloads',
            'edit_file_downloads',
            'delete_file_downloads',
            'browse_news',
            'read_news',
            'add_news',
            'edit_news',
            'delete_news',
            'browse_roles',
            'browse_accesses',
            'edit_accesses',
            'browse_email_templates',
            'read_email_templates',
            'edit_email_templates',
            'browse_logs',
            'read_logs',
        ];
        return Permission::whereIn('key', $keys)->get();
    }

    private function adminPermissions()
    {
        $keys = [
            'browse_users',
            'read_users',
            'edit_users',
            'add_users',
            'browse_institutions',
            'read_institutions',
            'verify_institutions',
            'browse_master_data',
            'read_master_data',
            'edit_master_data',
            'add_master_data',
            'delete_master_data',
            'browse_instruments',
            'read_instruments',
            'edit_instruments',
            'add_instruments',
            'delete_instruments',
            'process_instruments',
            'browse_instrument_categories',
            'read_instrument_categories',
            'edit_instrument_categories',
            'add_instrument_categories',
            'delete_instrument_categories',
            'process_instrument_categories',
            'browse_accreditations',
            'read_accreditations',
            'add_accreditations',
            'recap_accreditations',
            'verify_accreditations',
            'process_accreditations',
            'browse_certifications',
            'read_certifications',
            'edit_certifications',
            'browse_reports',
            'read_reports',
            'browse_contents',
            'read_contents',
            'add_contents',
            'edit_contents',
            'delete_contents',
            'browse_banners',
            'read_banners',
            'add_banners',
            'edit_banners',
            'delete_banners',
            'browse_testimonies',
            'read_testimonies',
            'add_testimonies',
            'edit_testimonies',
            'delete_testimonies',
            'browse_galleries',
            'read_galleries',
            'add_galleries',
            'edit_galleries',
            'delete_galleries',
            'browse_videos',
            'read_videos',
            'add_videos',
            'edit_videos',
            'delete_videos',
            'browse_faqs',
            'read_faqs',
            'add_faqs',
            'edit_faqs',
            'delete_faqs',
            'browse_file_downloads',
            'read_file_downloads',
            'add_file_downloads',
            'edit_file_downloads',
            'delete_file_downloads',
            'browse_news',
            'read_news',
            'add_news',
            'edit_news',
            'delete_news',
        ];
        return Permission::whereIn('key', $keys)->get();
    }

    private function assessorPermissions()
    {
        $keys = [
            'browse_institutions',
            'browse_evaluations',
            'process_evaluations',
            'input_evaluations',
            'recap_evaluations',
        ];
        return Permission::whereIn('key', $keys)->get();
    }

    private function assesseePermissions()
    {
        $keys = [
            'edit_institutions',
            'browse_accreditations',
            'add_accreditations',
            'recap_accreditations',
            'add_self_assessments',
        ];
        return Permission::whereIn('key', $keys)->get();
    }
}
