<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Datatables;

class UserController extends Controller
{
    use ResponseTrait, UtilityTrait;

    public function listUsersIndex(Request $request)
    {
        $user_count = User::count();
        return view('admin.user.index',compact('user_count'));
    }

    public function usersList(Request $request)
    {
        try {
            $data = User::get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('country_code_with_phone_number', function($row){
                    return $row->country_code . ' - ' . $row->phone_number;
                })
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" class="delete btn btn-danger btn-sm" data-id="'. $row->id .'"><span class="fa fa-trash"></span></a>';
                    return $btn;
                })
                ->filter(function ($instance) use ($request) {
                    
                    if (!empty($request->get('user_search'))) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            if (Str::contains(Str::lower($row['fullname']), Str::lower($request->get('user_search')))){
                                return true;
                            }else if (Str::contains(Str::lower($row['email']), Str::lower($request->get('user_search')))){
                                return true;
                            }else if (Str::contains(Str::lower($row['phone_number']), Str::lower($request->get('user_search')))){
                                return true;
                            }else {
                                return false;
                            }
                        });
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    public function userDelete(Request $request)
    {
        $user_delete = User::where('id',$request->get('id'))->first();
        if($user_delete) 
        {
            $user_delete->delete();
            return response()->json(['status' => true, 'msg' => 'User successfully deleted.']);
        }
        else 
        {
            return response()->json(['status' => false, 'msg' => 'Something went wrong!']);
        }
    }
}
