<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyerProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyer_products', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->string('buyer_product_name')->nullable();
            $table->text('buyer_product_images')->nullable();
            $table->text('buyer_product_description')->nullable();
            $table->integer('buyer_product_status')->comment('0 : processing/pendding, 1 : approve, 2 : disapproved')->default(0);
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
        Schema::dropIfExists('buyer_products');
    }
}
