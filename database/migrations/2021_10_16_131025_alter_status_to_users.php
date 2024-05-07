<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStatusToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create temporary column because enum doesn't support editing values
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status_temp', ['inactive', 'active', 'failed', 'awaiting_verification'])
                  ->default('inactive')
                  ->after('status');
        });

        // Fill the new temporary column with old column's values
        DB::statement("UPDATE users SET status_temp = 'inactive' WHERE status = 'inactive'");
        DB::statement("UPDATE users SET status_temp = 'active' WHERE status = 'active'");
        DB::statement("UPDATE users SET status_temp = 'failed' WHERE status = 'failed'");

        // Drop old column
        DB::statement("ALTER TABLE users DROP status");

        // Rename the temporary column to original column
        DB::statement("ALTER TABLE users CHANGE status_temp status ENUM('inactive','active','failed','awaiting_verification') DEFAULT 'inactive' NOT NULL");

        Schema::table('users', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status_temp', ['inactive', 'active', 'failed', 'valid'])
                  ->default('inactive')
                  ->after('status');
        });
        DB::statement("UPDATE users SET status_temp = 'inactive' WHERE status = 'inactive'");
        DB::statement("UPDATE users SET status_temp = 'active' WHERE status = 'active'");
        DB::statement("UPDATE users SET status_temp = 'failed' WHERE status = 'failed'");
        DB::statement("ALTER TABLE users DROP status");
        DB::statement("ALTER TABLE users CHANGE status_temp status ENUM('inactive','active','failed','valid') DEFAULT 'inactive' NOT NULL");
        Schema::table('users', function (Blueprint $table) {
            $table->index('status');
        });
    }
}
