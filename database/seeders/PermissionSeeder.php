<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menu = Menu::whereSlug('user')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_users',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_users',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_users',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'add_users',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'delete_users',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('data-kelembagaan')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_institutions',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_institutions',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_institutions',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'verify_institutions',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('master-data')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'add_master_data',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_master_data',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'browse_master_data',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_master_data',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'delete_master_data',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('instrumen')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_instruments',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_instruments',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_instruments',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'add_instruments',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'delete_instruments',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'process_instruments',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('kategori-instrumen')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_instrument_categories',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_instrument_categories',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_instrument_categories',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'add_instrument_categories',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'delete_instrument_categories',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'process_instrument_categories',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('akreditasi')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_accreditations',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_accreditations',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'add_accreditations',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'recap_accreditations',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'verify_accreditations',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'process_accreditations',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('self-assessment')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'add_self_assessments',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('sertifikasi')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_certifications',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_certifications',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_certifications',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('report')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_reports',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_reports',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('content-website')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_contents',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_contents',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'add_contents',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_contents',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'delete_contents',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('banner')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_banners',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_banners',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'add_banners',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_banners',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'delete_banners',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('testimoni')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_testimonies',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_testimonies',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'add_testimonies',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_testimonies',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'delete_testimonies',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('galeri')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_galleries',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_galleries',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'add_galleries',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_galleries',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'delete_galleries',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('video')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_videos',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_videos',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'add_videos',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_videos',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'delete_videos',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('faq')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_faqs',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_faqs',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'add_faqs',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_faqs',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'delete_faqs',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('file-download')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_file_downloads',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_file_downloads',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'add_file_downloads',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_file_downloads',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'delete_file_downloads',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('berita')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_news',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_news',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'add_news',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_news',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'delete_news',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('role')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_roles',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('access')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_accesses',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_accesses',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('email-template')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_email_templates',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_email_templates',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'edit_email_templates',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('log')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_logs',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'read_logs',
        ], [
            'menu_id' => $menu->id,
        ]);

        $menu = Menu::whereSlug('penilaian')->first();
        $permission = Permission::updateOrCreate([
            'key' => 'browse_evaluations',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'process_evaluations',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'input_evaluations',
        ], [
            'menu_id' => $menu->id,
        ]);

        $permission = Permission::updateOrCreate([
            'key' => 'recap_evaluations',
        ], [
            'menu_id' => $menu->id,
        ]);
    }
}
