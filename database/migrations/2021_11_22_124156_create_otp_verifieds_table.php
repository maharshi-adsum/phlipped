<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpVerifiedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp_verifieds', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->string('country_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->integer('otp')->default(0);
            $table->integer('status')->comment('0 : pending, 1 : verified, 2 : expired')->default(0);
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
        Schema::dropIfExists('otp_verifieds');
    }
}
