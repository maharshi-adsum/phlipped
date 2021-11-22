<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Admin;
use Session;
use Crypt;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;
use App\Traits\RequestTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    use ResponseTrait, RequestTrait, UtilityTrait;

    /**
     * [login description]
     * @param  Request $req [description]
     * @return [type]       [description]
     */
    function login(Request $req)
    {
        $req->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = Admin::where(["email"=>$req->input('email')])->first();
        if($user)
        {
            if(Hash::check($req->password, $user->password))
            {
                $req->session()->put('id',$user->id);
                $req->session()->put('first_name',$user->first_name);
                $req->session()->put('last_name',$user->last_name);
                $req->session()->put('user', ucfirst($user->username));
                $req->session()->put('email',$user->email);
                return redirect('/index');
            }
            else
            {   $response['status'] = 0;
                $response['message'] = 'The email and password that you entered did not match our records.Please double-check and try again';
                session()->flash('response', $response);
                return redirect()->back();
            }
        }
        else
        {
            $response['status'] = 0;
            $response['message'] = 'The email and password that you entered did not match our records.Please double-check and try again';
            session()->flash('response', $response);
            return redirect()->back();
        }
    }

    /**
     * [logout description]
     * @return [type] [description]
     */
    function logout()
    {
        Session::flush();
        return redirect('login');
    }
}
