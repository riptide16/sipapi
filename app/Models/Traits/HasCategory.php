<?php

namespace App\Models\Traits;

trait HasCategory
{
    public static function categoryList()
    {
        return [
            'Perpustakaan Desa',
            'Kecamatan',
            'Kabupaten Kota',
            'Provinsi',
            'SD MI',
            'SMP MTs',
            'SMA SMK MA',
            'Perguruan Tinggi',
            'Khusus',
            'PAUD',
            'SLB',
            'Lembaga NonPemerintah',
            'Rumah Ibadah',
        ];
    }
}
