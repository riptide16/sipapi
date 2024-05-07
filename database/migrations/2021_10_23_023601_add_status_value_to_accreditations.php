<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusValueToAccreditations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Raw query because altering enum is not supported
        DB::statement("ALTER TABLE accreditations MODIFY COLUMN status ENUM('diajukan', 'dinilai', 'ditinjau', 'penilaian_rapat', 'terakreditasi', 'belum_lengkap') DEFAULT 'diajukan' NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE accreditations MODIFY COLUMN status ENUM('diajukan', 'dinilai', 'ditinjau', 'penilaian_rapat', 'terakreditasi') DEFAULT 'diajukan' NOT NULL");
    }
}
