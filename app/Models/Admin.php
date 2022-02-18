<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UtilityTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    Use UtilityTrait;

    protected $fillable = [
        'username','first_name','last_name','email','phone_number','profile_image','buyer_days','seller_days',
     ];
     protected $table="admins";
 	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $guard = 'admin';

    public function getprofileImageAttribute($value)
	{
        if($value){
    		return asset('public/upload/profile_image/'.$value);
        } else {
            return asset('public/assets/images/user-noimage.png');
        }
	}
}
