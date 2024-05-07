<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPredicateAndAccreditationExpiresAtToInstitutions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('predicate')->nullable()->after('last_certification_date')->index();
            $table->dateTime('accredited_at')->nullable()->after('predicate');
            $table->dateTime('accreditation_expires_at')->nullable()->after('accredited_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn('predicate');
            $table->dropColumn('accredited_at');
            $table->dropColumn('accreditation_expires_at');
        });
    }
}
