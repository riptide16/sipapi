<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyConstraintsToInstitutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('subdistrict_id')->references('id')->on('subdistricts');
            $table->foreign('village_id')->references('id')->on('villages');
        });

        Schema::table('institution_requests', function (Blueprint $table) {
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('subdistrict_id')->references('id')->on('subdistricts');
            $table->foreign('village_id')->references('id')->on('villages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropForeign('institutions_village_id_foreign');
            $table->dropForeign('institutions_subdistrict_id_foreign');
            $table->dropForeign('institutions_city_id_foreign');
            $table->dropForeign('institutions_province_id_foreign');
        });

        Schema::table('institution_requests', function (Blueprint $table) {
            $table->dropForeign('institution_requests_village_id_foreign');
            $table->dropForeign('institution_requests_subdistrict_id_foreign');
            $table->dropForeign('institution_requests_city_id_foreign');
            $table->dropForeign('institution_requests_province_id_foreign');
        });
    }
}
