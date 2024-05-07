<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = [
            [
                'slug' => 'user',
                'title' => 'Users',
                'order' => 1,
                'icon' => 'user',
            ],
            [
                'slug' => 'master-data',
                'title' => 'Master Data',
                'order' => 2,
                'icon' => 'sitemap',
            ],
            [
                'slug' => 'kategori-instrumen',
                'title' => 'Kategori Instrumen',
                'order' => 3,
                'icon' => 'th-list',
            ],
            [
                'slug' => 'instrumen',
                'title' => 'Instrumen',
                'order' => 4,
                'icon' => 'list-ul',
            ],
            [
                'slug' => 'data-kelembagaan',
                'title' => 'Data Kelembagaan',
                'order' => 5,
                'icon' => 'university',
            ],
            [
                'slug' => 'akreditasi',
                'title' => 'Akreditasi',
                'order' => 6,
                'icon' => 'tasks',
            ],
            [
                'slug' => 'self-assessment',
                'title' => 'Self Assessment',
                'order' => 7,
                'icon' => 'tasks',
            ],
            [
                'slug' => 'sertifikasi',
                'title' => 'Sertifikasi',
                'order' => 7,
                'icon' => 'certificate',
            ],
            [
                'slug' => 'penilaian',
                'title' => 'Penilaian',
                'order' => 8,
                'icon' => 'pen-alt'
            ],
            [
                'slug' => 'report',
                'title' => 'Report',
                'order' => 9,
                'icon' => 'file-excel',
            ],
            [
                'slug' => 'content-website',
                'title' => 'Content Website',
                'order' => 10,
                'icon' => 'columns',
            ],
            [
                'slug' => 'banner',
                'title' => 'Manajemen Banner',
                'order' => 11,
                'icon' => 'images',
            ],
            [
                'slug' => 'testimoni',
                'title' => 'Testimony',
                'order' => 12,
                'icon' => 'comment-dots',
            ],
            [
                'slug' => 'galeri',
                'title' => 'Manajemen Gallery',
                'order' => 13,
                'icon' => 'image',
            ],
            [
                'slug' => 'video',
                'title' => 'Video',
                'order' => 14,
                'icon' => 'video',
            ],
            [
                'slug' => 'faq',
                'title' => 'FAQ',
                'order' => 15,
                'icon' => 'question-circle',
            ],
            [
                'slug' => 'file-download',
                'title' => 'File Download',
                'order' => 16,
                'icon' => 'file-download',
            ],
            [
                'slug' => 'berita',
                'title' => 'Manajemen Berita',
                'order' => 17,
                'icon' => 'newspaper',
            ],
            [
                'slug' => 'role',
                'title' => 'Roles',
                'order' => 18,
                'icon' => 'user-friends',
            ],
            [
                'slug' => 'access',
                'title' => 'Access',
                'order' => 19,
                'icon' => 'user-cog',
            ],
            [
                'slug' => 'email-template',
                'title' => 'Email Template',
                'order' => 20,
                'icon' => 'envelope-open-text',
            ],
            [
                'slug' => 'log',
                'title' => 'Log',
                'order' => 21,
                'icon' => 'history',
            ],
        ];

        foreach ($menus as $index => $menu) {
            Menu::updateOrCreate([
                'slug' => $menu['slug'],
            ], [
                'title' => $menu['title'],
                'order' => $menu['order'],
                'icon' => $menu['icon']
            ]);
        }
    }
}
