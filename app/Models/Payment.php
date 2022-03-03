<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id', 'customer_id', 'buyer_product_id', 'seller_product_id', 'amount', 'payment_method_types', 'transaction_id',
    ];

    protected $table = "payments";

 	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
