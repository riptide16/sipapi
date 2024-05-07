<?php

namespace Database\Seeders;

use App\Models\PublicMenu;
use Illuminate\Database\Seeder;

class PublicMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $main = [
            'tentang-kami' => [
                'name' => 'Tentang Kami',
                'order' => 1,
            ],
            'media' => [
                'name' => 'Media',
                'order' => 2,
            ],
            'layanan' => [
                'name' => 'Layanan',
                'order' => 3,
            ],
        ];
        foreach ($main as $url => $menu) {
            $pubMenu = PublicMenu::updateOrCreate([
                'url' => $url,
            ], [
                'name' => $menu['name'],
                'order' => $menu['order'],
                'is_default' => true,
            ]);
            $main[$url]['id'] = $pubMenu->id;
        }

        $subs = [
            'tentang-kami/assessor' => [
                'name' => 'Profil Asesor / LAPNAS',
                'order' => 2,
                'parent_id' => $main['tentang-kami']['id'],
            ],
            'tentang-kami/testimoni' => [
                'name' => 'Testimoni',
                'order' => 4,
                'parent_id' => $main['tentang-kami']['id'],
            ],
            'tentang-kami/penelusuran-akreditasi' => [
                'name' => 'Penelusuran Akreditasi',
                'order' => 3,
                'parent_id' => $main['tentang-kami']['id'],
            ],
            'tentang-kami/jumlah-perpustakaan-terakreditasi' => [
                'name' => 'Jumlah Perpustakaan Terakreditasi',
                'order' => 5,
                'parent_id' => $main['tentang-kami']['id'],
            ],
            'media/berita' => [
                'name' => 'Berita',
                'order' => 1,
                'parent_id' => $main['media']['id'],
            ],
            'media/galeri' => [
                'name' => 'Galeri',
                'order' => 2,
                'parent_id' => $main['media']['id'],
            ],
            'media/video' => [
                'name' => 'Video',
                'order' => 3,
                'parent_id' => $main['media']['id'],
            ],
            'layanan/faq' => [
                'name' => 'FAQ',
                'order' => 1,
                'parent_id' => $main['layanan']['id'],
            ],
            'layanan/unduh-berkas' => [
                'name' => 'Unduh Berkas',
                'order' => 2,
                'parent_id' => $main['layanan']['id'],
            ],
        ];

        $toDeletes = [
            'layanan/penelusuran-akreditasi',
            'tentang-kami/informasi-umum',
        ];

        if (!empty($toDeletes)) {
            PublicMenu::whereIn('url', $toDeletes)->where('is_default', true)->delete();
        }

        foreach ($subs as $url => $menu) {
            $pubMenu = PublicMenu::updateOrCreate([
                'url' => $url,
            ], [
                'name' => $menu['name'],
                'order' => $menu['order'],
                'parent_id' => $menu['parent_id'],
                'is_default' => true,
            ]);
            $subs[$url]['id'] = $pubMenu->id;
        }
    }
}
