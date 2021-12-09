<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Traits\UtilityTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Auth;

class UserController extends Controller
{
    use ResponseTrait, UtilityTrait;

    /**
     * Swagger defination Get user profile details
     *
     * @OA\Post(
     *     tags={"User Profile"},
     *     path="/userProfileGet",
     *     description="Get user profile details By user_id",
     *     summary="Get user profile details",
     *     operationId="User Profile",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */

    public function userProfileGet(Request $request)
    {
        try{
            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('user_profile_get');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return response()->json(['status' => "false",'data' => "", 'messages' => array('Unauthorized access')]);
            }

            $user = User::select('id','fullname','email','country_code','phone_number','user_image')->where('id',$input['user_id'])->where('is_active',1)->first();
            if($user)
            {
                return response()->json(['status' => "true",'data' => $user->toArray(), 'messages' => array('User profile found.')]);
            }
            else
            {
                return response()->json(['status' => "false",'data' => "", 'messages' => array('User does not exist')]);
            }

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Swagger defination User Profile Update
     *
     * @OA\Post(
     *     tags={"User Profile"},
     *     path="/userProfileUpdate",
     *     description="User Profile Update",
     *     summary="User Profile Update",
     *     operationId="userProfileUpdate",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
     * @OA\RequestBody(
     *     required=true,
     * @OA\MediaType(
     *     mediaType="multipart/form-data",
     * @OA\JsonContent(
     * @OA\Property(
     *     property="user_id",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="fullname",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="country_code",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="phone_number",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="email",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="user_image",
     *     type="file"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(
     *     response=200,
     *     description="User response",@OA\JsonContent
     *     (ref="#/components/schemas/SuccessResponse")
     * ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header",@OA\
     *     JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error",@OA\JsonContent
     *     (ref="#/components/schemas/ErrorResponse")
     * ),
     * security={
     *     {"API-Key": {}}
     * }
     * )
     */

    public function userProfileUpdate(Request $request)
    {
        try{
            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('user_profile_get');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) 
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return response()->json(['status' => "false",'data' => "", 'messages' => array('Unauthorized access')]);
            }

            $requiredParams = $this->requiredRequestParams('user_profile_update',$input['user_id']);
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) 
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            $user = User::where('id',$input['user_id'])->first();
            $user->fullname = $input['fullname'];
            $user->country_code = $input['country_code'];
            $user->phone_number = $input['phone_number'];
            $user->email = $input['email'];
            if($request->hasfile('user_image'))
            {
                if($user)
                {
                    if($user->user_image)
                    {
                        $parts = explode('/',$user->user_image);
                        $last = end($parts);
                        if (file_exists(public_path('/upload/user_image/'.$last))) {
                            @unlink(public_path('/upload/user_image/'.$last));
                        }
                    }
                }

                $file = $request->file('user_image');
                $image_name = 'user_image_' . rand(111111,999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('upload/user_image'), $image_name);
                $user->user_image = $image_name;
            }
            $user->save();

            if($user)
            {
                unset($user['email_verified_at']);
                unset($user['device_token']);
                unset($user['device_type']);
                unset($user['is_active']);
                unset($user['created_at']);
                unset($user['updated_at']);
                return response()->json(['status' => "true",'data' => $user->toArray(), 'messages' => array('User profile successfully saved.')]);
            }
            else
            {
                return response()->json(['status' => 'false', 'messages' => array('Something went wrong!')]);
            }

        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }
    
    public function requiredRequestParams(string $action, $id = '')
    {
        switch ($action) {
            case 'user_profile_update':
                $params = [
                    'user_id' => 'required|exists:users,id',
                    'fullname' => 'required',
                    'email' => 'required',
                    'country_code' => 'required',
                    'phone_number' => 'required|unique:users,phone_number,'.$id,
                    'user_image' => 'required',
                ];
                break;
            case 'user_profile_get':
                $params = [
                    'user_id' => 'required|exists:users,id',
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }
}
