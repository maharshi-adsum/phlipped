<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    protected $fillable = [
        'user_id', 'buyer_product_name', 'buyer_product_images', 'buyer_product_description', 'is_active', 'is_expired',
    ];

    protected $table="buyers";

 	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
