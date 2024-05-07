<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPageIdToPublicMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('public_menus', function (Blueprint $table) {
            $table->uuid('page_id')->nullable();
            $table->foreign('page_id')->references('id')->on('pages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('public_menus', function (Blueprint $table) {
            $table->dropForeign('public_menus_page_id_foreign');
            $table->dropColumn('page_id');
        });
    }
}
