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
     * Swagger defination Get user profile or address details By user_id
     *
     * @OA\Post(
     *     tags={"User Profile & Address"},
     *     path="/userProfileGet",
     *     description="Get user profile or address details By user_id",
     *     summary="Get user profile or address details By user_id",
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

            // if($input['user_id'] != Auth::user()->id)
            // {
            //     return response()->json(['status' => "false",'data' => "", 'messages' => array('Unauthorized access')]);
            // }

            $user = User::select('id','first_name','last_name','email','country_code','phone_number','user_image','dob','ssn_last_4','routing_number','account_number','street','city','state','country','pincode','device_token')->where('id',$input['user_id'])->where('is_active',1)->first();
            if($user)
            {
                return response()->json(['status' => "true",'data' => $user->toArray(), 'messages' => array('User profile found')]);
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
     *     tags={"User Profile & Address"},
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
     *     property="first_name",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="last_name",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="email",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="dob",
     *     type="string",
     *     description="YYYY-MM-DD"
     *     ),
     * @OA\Property(
     *     property="ssn_last_4",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="routing_number",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="account_number",
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
            $user->first_name = $input['first_name'] ?? $user->first_name;
            $user->last_name = $input['last_name'] ?? $user->last_name;
            $user->email = $input['email'] ?? $user->email;
            $user->dob = $input['dob'] ?? $user->dob;
            $user->ssn_last_4 = $input['ssn_last_4'] ?? $user->ssn_last_4;
            $user->routing_number = $input['routing_number'] ?? $user->routing_number;
            $user->account_number = $input['account_number'] ?? $user->account_number;
            
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
                $checkProfile = ($user->first_name && $user->last_name && $user->email && $user->phone_number && $user->customer_id && $user->dob && $user->ssn_last_4 && $user->routing_number && $user->account_number && $user->street && $user->city && $user->state && $user->country && $user->pincode);
                $user->is_verified = $checkProfile ? 1 : 0;
                $user->save();

                unset($user['is_verified']);
                unset($user['email_verified_at']);
                unset($user['device_token']);
                unset($user['device_type']);
                unset($user['is_active']);
                unset($user['created_at']);
                unset($user['updated_at']);
                return response()->json(['status' => "true",'data' => $user->toArray(), 'messages' => array('User profile successfully saved')]);
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

    /**
     * Swagger defination user address add update
     *
     * @OA\Post(
     *     tags={"User Profile & Address"},
     *     path="/userAddressUpdate",
     *     description="user address add update",
     *     summary="user address add update",
     *     operationId="userAddressUpdate",
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
     *     property="street",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="city",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="state",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="country",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="pincode",
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

    public function userAddressUpdate(Request $request)
    {
        try{
            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('user_address_update');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) 
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            if($input['user_id'] != Auth::user()->id)
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('Unauthorized access')]);
            }

            $addUpdateAddress = User::where('id',$input['user_id'])->first();
            
            if($addUpdateAddress)
            {
                $addUpdateAddress->street = $input['street'];
                $addUpdateAddress->city = $input['city'];
                $addUpdateAddress->state = $input['state'];
                $addUpdateAddress->country = $input['country'];
                $addUpdateAddress->pincode = $input['pincode'];
                $addUpdateAddress->save();

                $checkProfile = ($addUpdateAddress->first_name && $addUpdateAddress->last_name && $addUpdateAddress->email && $addUpdateAddress->phone_number && $addUpdateAddress->customer_id && $addUpdateAddress->dob && $addUpdateAddress->ssn_last_4 && $addUpdateAddress->routing_number && $addUpdateAddress->account_number && $addUpdateAddress->street && $addUpdateAddress->city && $addUpdateAddress->state && $addUpdateAddress->country && $addUpdateAddress->pincode);
                $addUpdateAddress->is_verified = $checkProfile ? 1 : 0;
                $addUpdateAddress->save();

                unset($addUpdateAddress['is_verified']);
                unset($addUpdateAddress['email_verified_at']);
                unset($addUpdateAddress['device_token']);
                unset($addUpdateAddress['device_type']);
                unset($addUpdateAddress['is_active']);
                unset($addUpdateAddress['created_at']);
                unset($addUpdateAddress['updated_at']);

                return response()->json(['status' => "true",'data' => $addUpdateAddress->toArray(), 'messages' => array('Address successfully saved')]);
            }
            else
            {
                return response()->json(['status' => "false",'data' => "", 'messages' => array('Something went wrong!')]);
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
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required',
                ];
                break;
            case 'user_profile_get':
                $params = [
                    'user_id' => 'required|exists:users,id',
                ];
                break;
            case 'user_address_update':
                $params = [
                    'user_id' => 'required|exists:users,id',
                    'street' => 'required',
                    'city' => 'required',
                    'state' => 'required',
                    'country' => 'required',
                    'pincode' => 'required',
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }
}
