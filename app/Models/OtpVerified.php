<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerified extends Model
{
    protected $fillable = [
        'user_id', 'country_code', 'phone_number', 'otp', 'status',
    ];

    protected $table="otp_verifieds";

 	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
