<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlAndTypeVideoToAccreditationContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accreditation_contents', function (Blueprint $table) {
            $table->string('url')->nullable()->after('file');
        });

        // Raw query because altering enum is not supported
        DB::statement("ALTER TABLE accreditation_contents MODIFY COLUMN `type` ENUM('choice', 'proof', 'video') NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE accreditation_contents MODIFY COLUMN `type` ENUM('choice', 'proof') NOT NULL");

        Schema::table('accreditation_contents', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
}
