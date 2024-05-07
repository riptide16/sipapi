<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Infographic;
use Ramsey\Uuid\Uuid;

class InfographicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $populateData = [
            [
                'province_name' => 'Bali',
                'province_code' => 'id-ba'
            ],
            [
                'province_name' => 'Nanggroe Aceh Darussalam (NAD)',
                'province_code' => 'id-ac'
            ],
            [
                'province_name' => 'Sumatera Utara',
                'province_code' => 'id-su'
            ],
            [
                'province_name' => 'Riau',
                'province_code' => 'id-ri'
            ],
            [
                'province_name' => 'Kepulauan Riau',
                'province_code' => 'id-kr'
            ],
            [
                'province_name' => 'Sumatera Barat',
                'province_code' => 'id-sb'
            ],
            [
                'province_name' => 'Jambi',
                'province_code' => 'id-ja'
            ],
            [
                'province_name' => 'Sumatera Selatan',
                'province_code' => 'id-sl'
            ],
            [
                'province_name' => 'Bengkulu',
                'province_code' => 'id-be'
            ],
            [
                'province_name' => 'Lampung',
                'province_code' => 'id-1024'
            ],
            [
                'province_name' => 'Bangka Belitung',
                'province_code' => 'id-bb'
            ],
            [
                'province_name' => 'Banten',
                'province_code' => 'id-bt'
            ],
            [
                'province_name' => 'DKI Jakarta',
                'province_code' => 'id-jk'
            ],
            [
                'province_name' => 'Jawa Barat',
                'province_code' => 'id-jr'
            ],
            [
                'province_name' => 'Jawa Tengah',
                'province_code' => 'id-jt'
            ],
            [
                'province_name' => 'DI Yogyakarta',
                'province_code' => 'id-yo'
            ],
            [
                'province_name' => 'Jawa Timur',
                'province_code' => 'id-ji'
            ],
            [
                'province_name' => 'Nusa Tenggara Barat (NTB)',
                'province_code' => 'id-nb'
            ],
            [
                'province_name' => 'Nusa Tenggara Timur (NTT)',
                'province_code' => 'id-nt'
            ],
            [
                'province_name' => 'Kalimantan Barat',
                'province_code' => 'id-kb'
            ],
            [
                'province_name' => 'Kalimantan Timur',
                'province_code' => 'id-ki'
            ],
            [
                'province_name' => 'Kalimantan Selatan',
                'province_code' => 'id-ks'
            ],
            [
                'province_name' => 'Kalimantan Tengah',
                'province_code' => 'id-kt'
            ],
            [
                'province_name' => 'Gorontalo',
                'province_code' => 'id-go'
            ],
            [
                'province_name' => 'Sulawesi Tengah',
                'province_code' => 'id-st'
            ],
            [
                'province_name' => 'Sulawesi Barat',
                'province_code' => 'id-sr'
            ],
            [
                'province_name' => 'Sulawesi Selatan',
                'province_code' => 'id-se'
            ],
            [
                'province_name' => 'Sulawesi Utara',
                'province_code' => 'id-sw'
            ],
            [
                'province_name' => 'Sulawesi Tenggara',
                'province_code' => 'id-sg'
            ],
            [
                'province_name' => 'Maluku Utara',
                'province_code' => 'id-la'
            ],
            [
                'province_name' => 'Maluku',
                'province_code' => 'id-ma'
            ],
            [
                'province_name' => 'Papua',
                'province_code' => 'id-pa'
            ],
            [
                'province_name' => 'Papua Barat',
                'province_code' => 'id-ib'
            ],
        ];

        foreach ($populateData as $index => $data) {
            Infographic::updateOrCreate([
                'province_name' => $data['province_name'],
            ], [
                'province_name' => $data['province_name'],
                'province_code' => $data['province_code']
            ]);
        }
    }
}
