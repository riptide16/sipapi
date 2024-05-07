<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultiAspectEnumToInstrumentAspects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE instrument_aspects MODIFY COLUMN `type` ENUM('choice','proof','multi_aspect') NOT NULL");
        DB::statement("ALTER TABLE accreditation_contents MODIFY COLUMN `type` ENUM('choice','proof','multi_aspect') NOT NULL");
        Schema::table('instrument_aspects', function (Blueprint $table) {
            $table->uuid('parent_id')->after('order')->nullable();
            $table->foreign('parent_id')->references('id')->on('instrument_aspects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instrument_aspects', function (Blueprint $table) {
            $table->dropForeign('instrument_aspects_parent_id_foreign');
            $table->dropColumn('parent_id');
        });
        DB::statement("ALTER TABLE accreditation_contents MODIFY COLUMN `type` ENUM('choice','proof') NOT NULL");
        DB::statement("ALTER TABLE instrument_aspects MODIFY COLUMN `type` ENUM('choice','proof') NOT NULL");
    }
}
