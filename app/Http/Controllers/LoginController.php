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
                $req->session()->put('user', ucfirst($user->username));
                $req->session()->put('email',$user->email);
                $req->session()->put('profile_image',$user->profile_image);
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

    public function adminSetting()
    {
        $profile = Admin::first();
        return view('admin.setting',compact('profile'));
    }

    public function addUpdateAdminProfile(Request $request)
    {
        try {
            $profile = Admin::first();
            if($profile)
            {
                $profile->username = $request->username;
                $profile->email = $request->email;
                if($request->password)
                {
                    $profile->password = Hash::make($request->password);
                }
                $profile->save();
            }
            else
            {
                if($request->password)
                {
                    $input['password'] = Hash::make($request->password);
                }
                $profile = Admin::create($input);
            }

            if($request->hasfile('profile_image'))
            {
                if($profile)
                {
                    if($profile->profile_image)
                    {
                        $parts = explode('/',$profile->profile_image);
                        $last = end($parts);
                        if (file_exists(public_path('/upload/profile_image/'.$last))) {
                            @unlink(public_path('/upload/profile_image/'.$last));
                        }
                    }
                }
                
                $file = $request->file('profile_image');
                $image_name = 'profile_image' . rand(111111,999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('upload/profile_image'), $image_name);
                $profileImage = Admin::where('id',$profile->id)->update(['profile_image' => $image_name]);
            }

            $profile_data_put_session = Admin::where('id',$profile->id)->first();
            $request->session()->put('user', ucfirst($profile_data_put_session->username));
            $request->session()->put('email',$profile_data_put_session->email);
            $request->session()->put('profile_image',$profile_data_put_session->profile_image);

            if($profile)
            {
                return response()->json(['status' => 1, 'messages' => 'Admin profile successfully saved.']);
            }
            else 
            {
                return response()->json(['status' => 0, 'messages' => 'Something went wrong!']);
            }

        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function addUpdateAdminPassword(Request $request)
    {
        try {
            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('changePassword');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            $admin = Admin::where('id',session('id'))->first();
            if(Hash::check($input['old_password'], $admin->password))
            {
                if($input['new_password'] == $input['confirm_new_password'])
                {
                    $admin->password = Hash::make($input['new_password']);
                    $admin->save();
                    $data['status'] = 1;
                    $data['messages'] = 'Your password successfully saved';
                    return response()->json($data);
                }
                else
                {
                    $data['status'] = 0;
                    $data['messages'] = 'The password confirmation does not match.';
                    return response()->json($data);
                }
            }
            else
            {
                $data['status'] = 0;
                $data['messages'] = 'The old password does not match.';
                return response()->json($data);
            }

        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function requiredRequestParams(string $action, $id = null)
    {
        switch ($action) {
            case 'changePassword':
                $params = [
                    'old_password' => 'required',
                    'new_password' => 'required',
                    'confirm_new_password' => 'required',
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }
}
