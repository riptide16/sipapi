<?php

namespace Database\Seeders;

use App\Models\Instrument;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Instrument::firstOrCreate([
            'category' => 'Perpustakaan Desa'
        ]);

        Instrument::firstOrCreate([
            'category' => 'Kecamatan',
        ]);

        Instrument::firstOrCreate([
            'category' => 'Kabupaten Kota',
        ]);

        Instrument::firstOrCreate([
            'category' => 'Provinsi',
        ]);

        Instrument::firstOrCreate([
            'category' => 'SD MI',
        ]);

        Instrument::firstOrCreate([
            'category' => 'SMP MTs',
        ]);

        Instrument::firstOrCreate([
            'category' => 'SMA SMK MA',
        ]);

        Instrument::firstOrCreate([
            'category' => 'Perguruan Tinggi',
        ]);

        Instrument::firstOrCreate([
            'category' => 'Khusus',
        ]);
    }
}
