<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BuyerProducts;
use App\Models\SellerProducts;

class DashboardController extends Controller
{
    public function admin(Request $request)
    {
        $user_count = User::count();
        $buyer_product_count = BuyerProducts::count();
        $seller_product_count = SellerProducts::count();
        return view('admin.home',compact('user_count','buyer_product_count','seller_product_count'));
    }
}
