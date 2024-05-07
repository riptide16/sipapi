<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('scheduled_date');
            $table->string('method');
            $table->uuid('accreditation_id');
            $table->foreign('accreditation_id')->references('id')->on('accreditations');
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
        Schema::dropIfExists('evaluation_assignments');
    }
}
