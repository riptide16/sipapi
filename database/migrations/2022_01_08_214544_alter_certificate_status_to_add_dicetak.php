<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCertificateStatusToAddDicetak extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE accreditations MODIFY COLUMN certificate_status ENUM('ditandatangani','dikirim','terakreditasi','cetak_sertifikat') DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE accreditations MODIFY COLUMN certificate_status ENUM('ditandatangani','dikirim','terakreditasi') DEFAULT NULL");
    }
}
