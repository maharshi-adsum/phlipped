<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuyerProducts extends Model
{
    protected $fillable = [
        'user_id', 'buyer_product_name', 'buyer_product_images', 'buyer_product_description', 'buyer_product_status',
    ];

    protected $table="buyer_products";

 	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
