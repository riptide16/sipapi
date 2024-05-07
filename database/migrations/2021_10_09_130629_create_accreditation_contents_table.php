<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccreditationContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accreditation_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('aspect');
            $table->string('statement')->nullable();
            $table->tinyInteger('value')->unsigned()->nullable();
            $table->string('file')->nullable();
            $table->enum('type', ['choice', 'proof'])->index();
            $table->string('aspectable_type')->nullable();
            $table->uuid('aspectable_id')->nullable();
            $table->uuid('instrument_aspect_point_id')->nullable()->index();
            $table->uuid('accreditation_id');
            $table->foreign('accreditation_id')->references('id')->on('accreditations');
            $table->timestamps();
            $table->index(['aspectable_type', 'aspectable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accreditation_contents');
    }
}
