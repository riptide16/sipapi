<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstitutionStatusToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('institution_name')->nullable()->after('password');
            $table->after('role_id', function ($table) {
                $table->enum('status', ['inactive', 'active', 'failed'])
                      ->default('inactive')
                      ->index();
                $table->timestamp('activated_at')->nullable();
                $table->timestamp('failed_at')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('institution_name');
            $table->dropColumn('status');
            $table->dropColumn('activated_at');
            $table->dropColumn('failed_at');
        });
    }
}
