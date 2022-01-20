<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerProducts extends Model
{
    protected $fillable = [
        'user_id', 'buyer_product_id', 'seller_product_name', 'seller_product_images', 'seller_product_description', 'seller_product_price', 'seller_product_condition', 'seller_product_location', 'seller_product_shipping_charges', 'seller_product_status',
    ];

    protected $table="seller_products";

 	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
