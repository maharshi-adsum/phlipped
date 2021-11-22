<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function admin(Request $request)
    {
        $user_count = User::count();
        return view('admin.home',compact('user_count'));
    }
}
