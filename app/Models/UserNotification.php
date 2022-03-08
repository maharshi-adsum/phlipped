<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'notification_type', 'buyer_product_id', 'seller_product_id', 'is_read', 'is_delete',
    ];

    protected $table = "user_notifications";

 	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
    ];   
}
