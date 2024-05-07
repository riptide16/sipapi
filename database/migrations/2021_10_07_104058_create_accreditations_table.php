<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccreditationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accreditations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('status', [
                'diajukan', 'dinilai', 'ditinjau', 'penilaian_rapat', 'terakreditasi'
            ])->default('diajukan')->index();
            $table->text('notes')->nullable();
            $table->timestamp('accredited_at')->nullable();
            $table->uuid('institution_id');
            $table->foreign('institution_id')->references('id')->on('institutions');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('accreditations');
    }
}
