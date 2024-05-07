<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccreditationSimulationContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accreditation_simulation_contents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('aspect');
            $table->text('statement')->nullable();
            $table->tinyInteger('value')->unsigned()->nullable();
            $table->string('file')->nullable();
            $table->enum('type', ['choice', 'proof', 'video'])->index();
            $table->string('url')->nullable();
            $table->string('aspectable_type')->nullable();
            $table->uuid('aspectable_id')->nullable();
            $table->uuid('instrument_aspect_point_id')->nullable()->index('instrument_aspect_point_id_index');
            $table->uuid('accreditation_simulation_id');
            $table->foreign('accreditation_simulation_id', 'accreditation_simulation_id_foreign')->references('id')->on('accreditation_simulations');
            $table->uuid('main_component_id')->nullable();
            $table->foreign('main_component_id')->references('id')->on('instrument_components');
            $table->timestamps();
            $table->index(['aspectable_type', 'aspectable_id'], 'aspectable_type_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accreditation_simulation_contents');
    }
}
