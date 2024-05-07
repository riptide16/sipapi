<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVillagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->char('postal_code', 5);
            $table->uuid('subdistrict_id');
            $table->foreign('subdistrict_id')->references('id')->on('subdistricts');
            $table->index(['name', 'subdistrict_id', 'postal_code']);
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
        Schema::dropIfExists('villages');
    }
}
