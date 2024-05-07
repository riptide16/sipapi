<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentIdTypeToInstrumentComponents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instrument_components', function (Blueprint $table) {
            $table->uuid('parent_id')->nullable()->after('weight');
            $table->foreign('parent_id')->references('id')->on('instrument_components');
            $table->string('type')->nullable()->after('weight');
            $table->smallInteger('weight')->unsigned()->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instrument_components', function (Blueprint $table) {
            $table->dropColumn('parent_id');
            $table->dropColumn('type');
            $table->smallInteger('weight')->unsigned()->nullable(false)->change();
        });
    }
}
