<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstrumentAspectPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instrument_aspect_points', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('instrument_aspect_id');
            $table->foreign('instrument_aspect_id')->references('id')->on('instrument_aspects');
            $table->text('statement');
            $table->tinyInteger('value')->unsigned()->nullable();
            $table->smallInteger('order')->unsigned();
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
        Schema::dropIfExists('instrument_aspect_points');
    }
}
