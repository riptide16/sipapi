<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMainComponentIdToAccreditationContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accreditation_contents', function (Blueprint $table) {
            $table->uuid('main_component_id')->after('aspectable_id')->nullable();
            $table->foreign('main_component_id')->references('id')->on('instrument_components');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accreditation_contents', function (Blueprint $table) {
            $table->dropColumn('main_component_id');
        });
    }
}
