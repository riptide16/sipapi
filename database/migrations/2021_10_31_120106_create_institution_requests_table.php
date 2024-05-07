<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstitutionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institution_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category')->nullable();
            $table->uuid('region_id')->nullable();
            $table->foreign('region_id')->references('id')->on('regions');
            $table->string('library_name')->nullable();
            $table->string('npp')->nullable();
            $table->string('agency_name')->nullable();
            $table->char('typology', 1)->nullable();
            $table->text('address')->nullable();
            $table->uuid('province_id')->nullable();
            $table->uuid('city_id')->nullable();
            $table->uuid('subdistrict_id')->nullable();
            $table->uuid('village_id')->nullable();
            $table->string('institution_head_name')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone_number')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('library_head_name')->nullable();
            $table->json('library_worker_name')->nullable();
            $table->string('registration_form_file')->nullable();
            $table->integer('title_count')->unsigned()->nullable();
            $table->uuid('user_id');
            $table->enum('status', ['tidak_valid', 'valid'])
                  ->default('tidak_valid')
                  ->index();
            $table->string('last_predicate')->nullable();
            $table->date('last_certification_date')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->enum('type', ['create', 'update']);
            $table->timestamp('validated_at')->nullable();
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
        Schema::dropIfExists('institution_requests');
    }
}
