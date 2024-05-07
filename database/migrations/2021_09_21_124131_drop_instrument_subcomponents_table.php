<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropInstrumentSubcomponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('instrument_second_subcomponents');
        Schema::dropIfExists('instrument_first_subcomponents');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('instrument_first_subcomponents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category');
            $table->string('name');
            $table->uuid('instrument_component_id');
            $table->foreign('instrument_component_id')->references('id')->on('instrument_components');
            $table->timestamps();
        });

        Schema::create('instrument_second_subcomponents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category');
            $table->string('name');
            $table->uuid('instrument_component_id');
            $table->foreign('instrument_component_id')->references('id')->on('instrument_components');
            $table->uuid('instrument_first_subcomponent_id');
            $table->foreign('instrument_first_subcomponent_id', 'instrument_first_subcomponent_id_foreign')
                  ->references('id')
                  ->on('instrument_first_subcomponents');
            $table->timestamps();
        });
    }
}
