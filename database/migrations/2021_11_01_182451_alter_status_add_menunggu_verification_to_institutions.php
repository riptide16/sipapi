<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStatusAddMenungguVerificationToInstitutions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE institutions MODIFY COLUMN status ENUM('tidak_valid','valid','menunggu_verifikasi') DEFAULT 'menunggu_verifikasi' NOT NULL");
        DB::statement("ALTER TABLE institution_requests MODIFY COLUMN status ENUM('tidak_valid','valid','menunggu_verifikasi') DEFAULT 'menunggu_verifikasi' NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE institution_requests MODIFY COLUMN status ENUM('tidak_valid','valid') DEFAULT 'tidak_valid' NOT NULL");
        DB::statement("ALTER TABLE institutions MODIFY COLUMN status ENUM('tidak_valid','valid') DEFAULT 'tidak_valid' NOT NULL");
    }
}
