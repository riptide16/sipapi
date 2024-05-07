<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCertificateFieldsToAccreditations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accreditations', function (Blueprint $table) {
            $table->enum('certificate_status', ['ditandatangani', 'dikirim', 'terakreditasi'])->nullable()->after('predicate');
            $table->date('certificate_sent_at')->nullable()->after('certificate_status');
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
            $table->dropColumn('certificate_status');
            $table->dropColumn('certificate_sent_at');
        });
    }
}
