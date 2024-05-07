<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvinceRegionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('province_region', function (Blueprint $table) {
            $table->uuid('province_id');
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->uuid('region_id');
            $table->foreign('region_id')->references('id')->on('regions');
            $table->primary(['province_id', 'region_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('province_region');
    }
}
