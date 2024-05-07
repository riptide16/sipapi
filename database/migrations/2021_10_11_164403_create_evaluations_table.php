<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('document_file')->nullable();
            $table->uuid('accreditation_id');
            $table->foreign('accreditation_id')->references('id')->on('accreditations');
            $table->uuid('institution_id');
            $table->foreign('institution_id')->references('id')->on('institutions');
            $table->uuid('assessor_id');
            $table->foreign('assessor_id')->references('id')->on('users');
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
        Schema::dropIfExists('accreditation_evaluations');
    }
}
