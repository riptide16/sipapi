<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstrumentFirstSubcomponents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instrument_first_subcomponents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category');
            $table->string('name');
            $table->uuid('instrument_component_id');
            $table->foreign('instrument_component_id')->references('id')->on('instrument_components');
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
        Schema::dropIfExists('instrument_first_subcomponents');
    }
}
