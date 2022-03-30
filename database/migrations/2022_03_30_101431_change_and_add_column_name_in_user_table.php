<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAndAddColumnNameInUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('fullname','first_name');
            $table->string('last_name')->after('fullname')->nullable();
            $table->date('dob')->after('password')->nullable();
            $table->string('ssn_last_4')->after('dob')->nullable();
            $table->string('routing_number')->after('ssn_last_4')->nullable();
            $table->string('account_number')->after('routing_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            //
        });
    }
}
