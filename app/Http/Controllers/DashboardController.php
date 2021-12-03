<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BuyerProducts;

class DashboardController extends Controller
{
    public function admin(Request $request)
    {
        $user_count = User::count();
        $buyer_product_count = BuyerProducts::count();
        return view('admin.home',compact('user_count','buyer_product_count'));
    }
}
