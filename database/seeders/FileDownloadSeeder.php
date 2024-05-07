<?php

namespace Database\Seeders;

use App\Models\FileDownload;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FileDownloadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dummyFile = 'files/attachment.pdf';

        $file = FileDownload::firstOrNew([
            'filename' => 'Contoh Surat Pendaftaran',
            'is_preset' => true,
        ]);
        if (!$file->exists) {
            $path = 'files/contoh_surat_pendaftaran.pdf';
            if (!Storage::disk('public')->exists($path)) {
                copy(__DIR__.'/'.$dummyFile, public_path('storage/'.$path));
                $file->fill([
                    'attachment' => $path,
                ])->save();
            }
        }

        $file = FileDownload::firstOrNew([
            'filename' => 'Contoh Surat Perubahan Data',
            'is_preset' => true,
        ]);
        if (!$file->exists) {
            $path = 'files/contoh_surat_perubahan_data.pdf';
            if (!Storage::disk('public')->exists($path)) {
                copy(__DIR__.'/'.$dummyFile, public_path('storage/'.$path));
                $file->fill([
                    'attachment' => $path,
                ])->save();
            }
        }
    }
}
