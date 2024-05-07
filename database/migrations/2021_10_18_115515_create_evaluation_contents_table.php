<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('evaluation_id');
            $table->string('statement')->nullable();
            $table->tinyInteger('value')->unsigned()->nullable();
            $table->foreign('evaluation_id')->references('id')->on('evaluations');
            $table->uuid('accreditation_content_id');
            $table->foreign('accreditation_content_id')->references('id')->on('accreditation_contents');
            $table->uuid('instrument_aspect_point_id')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_contents');
    }
}
