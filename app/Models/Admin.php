<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UtilityTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    Use UtilityTrait;

    protected $fillable = [
        'username','first_name','last_name','email','phone_number','profile_image',
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
}
