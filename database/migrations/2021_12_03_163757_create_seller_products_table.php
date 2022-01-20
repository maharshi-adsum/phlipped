<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seller_products', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default(0);
            $table->integer('buyer_product_id')->default(0);
            $table->string('seller_product_name')->nullable();
            $table->text('seller_product_images')->nullable();
            $table->text('seller_product_description')->nullable();
            $table->integer('seller_product_price')->default(0);
            $table->text('seller_product_condition')->nullable();
            $table->text('seller_product_location')->nullable();
            $table->integer('seller_product_shipping_charges')->default(0);
            $table->integer('seller_product_status')->comment('0 : processing/pendding, 1 : approve, 2 : disapproved')->default(0);
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
        Schema::dropIfExists('seller_products');
    }
}
