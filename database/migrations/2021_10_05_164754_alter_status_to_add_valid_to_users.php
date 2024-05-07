<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStatusToAddValidToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Raw query because altering enum is not supported
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('inactive','active','failed','valid') DEFAULT 'inactive' NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('inactive','active','failed') DEFAULT 'inactive' NOT NULL");
    }
}
