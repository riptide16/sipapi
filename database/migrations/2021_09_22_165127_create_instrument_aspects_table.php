<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstrumentAspectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instrument_aspects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('aspect');
            $table->uuid('instrument_id');
            $table->foreign('instrument_id')->references('id')->on('instruments');
            $table->uuid('instrument_component_id');
            $table->foreign('instrument_component_id')->references('id')->on('instrument_components');
            $table->enum('type', ['choice', 'proof'])->index();
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
        Schema::dropIfExists('instrument_aspects');
    }
}
