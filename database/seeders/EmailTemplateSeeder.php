<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $template = EmailTemplate::firstOrNew([
            'slug' => 'verifikasi-email'
        ]);
        if (!$template->exists) {
            $template->fill([
                'name' => 'Email verifikasi akun',
                'subject' => 'Verifikasi Alamat Email',
                'body' => 'Tekan tombol dibawah untuk memverifikasi email.',
                'action_button' => 'Verifikasi Email',
            ])->save();
        }

        $template = EmailTemplate::firstOrNew([
            'slug' => 'notifikasi-akun-aktif'
        ]);
        if (!$template->exists) {
            $template->fill([
                'name' => 'Notifikasi Akun Aktif',
                'subject' => 'Akun Anda Telah Aktif',
                'body' => 'Akun Anda telah berhasil teraktivasi. Anda sudah bisa masuk.',
                'action_button' => 'Masuk',
            ])->save();
        }
    }
}
