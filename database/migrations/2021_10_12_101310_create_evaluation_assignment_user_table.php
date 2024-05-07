<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationAssignmentUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_assignment_user', function (Blueprint $table) {
            $table->uuid('evaluation_assignment_id');
            $table->foreign('evaluation_assignment_id')->references('id')->on('evaluation_assignments');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->primary(['evaluation_assignment_id', 'user_id'], 'evaluation_user_id_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_assignment_user');
    }
}
