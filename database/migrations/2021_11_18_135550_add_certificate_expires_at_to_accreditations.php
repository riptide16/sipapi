<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCertificateExpiresAtToAccreditations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accreditations', function (Blueprint $table) {
            $table->dateTime('certificate_expires_at')->nullable()->after('certificate_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accreditations', function (Blueprint $table) {
            $table->dropColumn('certificate_expires_at');
        });
    }
}
