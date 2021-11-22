<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin(Request $request)
    {
        return view('admin.home');
    }
}
