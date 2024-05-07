<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAspectStatementOfAccreditationContentsToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accreditation_contents', function (Blueprint $table) {
            $table->text('aspect')->change();
            $table->text('statement')->nullable()->change();
        });

        Schema::table('evaluation_contents', function (Blueprint $table) {
            $table->text('statement')->nullable()->change();
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
            $table->string('aspect')->change();
            $table->string('statement')->nullable()->change();
        });

        Schema::table('evaluation_contents', function (Blueprint $table) {
            $table->string('statement')->nullable()->change();
        });
    }
}
