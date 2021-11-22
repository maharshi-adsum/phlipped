<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->renameColumn('name', 'fullname');
            $table->string('country_code')->after('email')->nullable();
            $table->string('phone_number')->after('country_code')->nullable();
            $table->text('device_token')->after('password')->nullable();
            $table->string('device_type')->after('device_token')->nullable();
            $table->integer('is_active')->after('device_type')->default(1);
            $table->string('user_image')->after('phone_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
