<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TransferInstitutionUserToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropForeign('institutions_user_id_foreign');
            $table->dropColumn('user_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('institution_id')->nullable();
            $table->foreign('institution_id')->references('id')->on('institutions');
        });
        Schema::table('institution_requests', function (Blueprint $table) {
            $table->uuid('institution_id')->nullable();
            $table->foreign('institution_id')->references('id')->on('institutions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('institution_requests', function (Blueprint $table) {
            $table->dropForeign('institution_requests_institution_id_foreign');
            $table->dropColumn('institution_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_institution_id_foreign');
            $table->dropColumn('institution_id');
        });
        Schema::table('institutions', function (Blueprint $table) {
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
}
