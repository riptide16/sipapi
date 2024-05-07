<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('url')->nullable();
            $table->smallInteger('order')->unsigned()->default(1);
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
        });

        Schema::table('public_menus', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('public_menus');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_menus');
    }
}
