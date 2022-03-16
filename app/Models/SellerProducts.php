<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class SellerProducts extends Model
{
    protected $fillable = [
        'user_id', 'buyer_product_id', 'seller_product_name', 'seller_product_images', 'seller_product_description', 'seller_product_price', 'seller_product_condition', 'seller_product_location', 'seller_product_latitude', 'seller_product_longitude', 'return_policy', 'seller_product_shipping_charges', 'seller_product_status', 'is_purchased',
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

    public function buyerProduct()
    {
        return $this->hasOne('App\Models\BuyerProducts','id','buyer_product_id')->where('is_active',1);
    }

    public function wishlist()
    {
        return $this->hasOne('App\Models\Wishlist','seller_product_id','id')->where('status',1)->where('user_id',Auth::user()->id);
    }
}
