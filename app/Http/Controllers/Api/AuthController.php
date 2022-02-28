<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Traits\RequestTrait;
use App\Models\User;
use App\Models\Admin;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth as Login;
use App\Models\Authenticator;
use App\Models\OauthAccessToken;
use App\Traits\UtilityTrait;
use GuzzleHttp\Exception\GuzzleException;
use Auth;
use DB;

class AuthController extends Controller
{
    use ResponseTrait, RequestTrait, UtilityTrait;

    private $authenticator;
    protected $authy;
    protected $sid;
    protected $authToken;
    /**
     * Constructor
     *
     * @param Authenticator $authenticator authenticator
     * @param Request       $request       request
     */
    public function __construct(Authenticator $authenticator, Request $request)
    {
        $this->authenticator = $authenticator;
        $this->request = $request;
    }

    /**
     * Swagger definition for signup
     *
     * @OA\Post(
     *     tags={"Authentication"},
     *     path="/signup",
     *     description="User Sign Up",
     *     summary="Sign Up",
     *     operationId="Sign Up",
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
     *     property="fullname",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="email",
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
     *     property="password",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="device_token",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="device_type",
     *     type="string"
     *     )
     *    )
     *   ),
     *  ),
     * @OA\Response(response=200,description="Response",
     * @OA\JsonContent(@OA\Property(property="token",type="string"))
     *     ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response="401",
     *     description="Not Authorized Invalid or missing Authorization header"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     */
    public function signup(Request $request)
    {
        try{
            $input = $this->objectToArray($request->input());
            
            $requiredParams = $this->requiredRequestParams('signup');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }
            $input['password'] = Hash::make($input['password']);
            
            $user_create = User::create($input);
            $user = User::find($user_create->id);
            if($user)
            {
                $credentials = array_values(
                    $this->request->only('country_code','phone_number','password')
                );
                $credentials['country_code'] = $input['country_code'];
                $credentials['phone_number'] = $input['phone_number'];
                $credentials['password'] = $input['password'];

                if($user = $this->authenticator->attemptSignUp($credentials))
                {
                    
                    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                    $customer = \Stripe\Customer::create(array(
                        'email' => $input['email'],
                        'name' => $input['fullname'],
                    ));

                    $update = User::where('id',$user['id'])->update(['customer_id' => $customer->id, 'device_token' => $input['device_token'], 'device_type' => $input['device_type']]);
                    $success = $this->loginfunction($input,$user);
                    return $this->successResponse(
                        $success,'You have successfully registered and otp sent to your phone number.'
                    );
                }
                else
                {
                    return response()->json(['status' => "false", 'data' => "", 'messages' => array('These credentials do not match our records.')]);
                }
            }
            else
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('Something went wrong. Please try again.')]);
            }
        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Swagger definition for unique validation
     *
     * @OA\Post(
     *     tags={"Authentication"},
     *     path="/uniqueValidation",
     *     description="User Unique Validation",
     *     summary="User Unique Validation",
     *     operationId="User Unique Validation",
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
     *     property="email",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="phone_number",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(response=200,description="Response",
     * @OA\JsonContent(@OA\Property(property="token",type="string"))
     *     ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response="401",
     *     description="Not Authorized Invalid or missing Authorization header"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     */
    public function uniqueValidation(Request $request)
    {
        try{
            $input = $this->objectToArray($request->input());
            
            $requiredParams = $this->requiredRequestParams('uniqueValidation');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }
            return response()->json(['status' => "true", 'data' => "", 'messages' => array('validate successfully.')]);
        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    /**
     * Swagger definition for login
     *
     * @OA\Post(
     *     tags={"Authentication"},
     *     path="/login",
     *     description="login",
     *     summary="login",
     *     operationId="login",
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
     *     property="country_code",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="phone_number",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="password",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="device_token",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="device_type",
     *     type="string"
     *     )
     *    )
     *   ),
     *  ),
     * @OA\Response(response=200,description="Response",
     * @OA\JsonContent(@OA\Property(property="token",type="string"))
     *     ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response="401",
     *     description="Not Authorized Invalid or missing Authorization header"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     */
    public function login(Request $request)
    {
        try{
            $input = $this->objectToArray($request->input());

            $requiredParams = $this->requiredRequestParams('login');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }

            $credentials = array_values(
                $this->request->only('country_code','phone_number','password')
            );
            
            $credentials['country_code'] = $input['country_code'];
            $credentials['phone_number'] = $input['phone_number'];
            $credentials['password'] = $input['password'];

            if($user = $this->authenticator->attemptLogin($credentials))
            {
                $update = User::where('id',$user['id'])->update(['device_token'=>$input['device_token'],'device_type'=>$input['device_type']]);
                $success = $this->loginfunction($input,$user);
                return $this->successResponse(
                    $success,'You Have Successfully Logged in to phlipped.'
                );
            }
            else
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('These credentials do not match our records.')]);
            }
        } catch (Exception $e) {
            return $this->sendErrorResponse($e);
        } catch (RequestException $e) {
            return $this->sendErrorResponse($e);
        }
    }

    public function loginfunction($input,$user)
    {
        $user = User::where('id',$user->id)->where('is_active','1')->first();
        $tokenResult = $user->createToken('phlipped');
        $token = $tokenResult->token;
        $input['remember_me'] = $this->arrayGet('remember_me', $input, 0);
        if (((int)$input['remember_me'] === 1)) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }
        $token->save();
        $success['token'] = 'Bearer '.$tokenResult->accessToken;
        $success['expires_at'] = Carbon::parse(
            $tokenResult->token->expires_at
        )->toDateTimeString();
        $success['user'] = $user;
        return $success;
        // return $this->successResponse(
        //     $success,'You Have Successfully Logged in to phlipped.'
        // );
    }

    /**
     * Swagger definition for forgetpassword
     *
     * @OA\Post(
     *     tags={"Authentication"},
     *     path="/forgetpassword",
     *     description="forgetPassword",
     *     summary="forgetPassword",
     *     operationId="forgetPassword",
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
     *     property="country_code",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="phone_number",
     *     type="string"
     *     )
     *    )
     *   ),
     *  ),
     * @OA\Response(response=200,description="Response",
     * @OA\JsonContent(@OA\Property(property="token",type="string"))
     *     ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response="401",
     *     description="Not Authorized Invalid or missing Authorization header"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     */
    /**
     * This function will send reset password link to email
     *
     * @param Request $request Request
     *
     * @return JsonResponse   JsonResponse
     */
    public function forgetPassword(Request $request)
    {
        try {
            $input = $request->all();

            // $requiredParams = $this->requiredRequestParams('forgetPassword');
            // $validator = Validator::make($input, $requiredParams);
            // if ($validator->fails()) {
            //     return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            // }

            $user = User::select('id','fullname','email','country_code','phone_number')->where('country_code',$request->country_code)->where('phone_number',$request->phone_number)->first();
            if($user)
            {
                return $this->successResponse($user->toArray(),('Otp sent to your phone number.'));
            }
            else
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('Your phone number not found')]);
            }
        } catch (NotFoundHttpException $ex) {
            return $this->notFoundRequest($ex);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    /**
     * Swagger definition for resetPassword
     *
     * @OA\Post(
     *     tags={"Authentication"},
     *     path="/resetPassword",
     *     description="resetPassword",
     *     summary="resetPassword",
     *     operationId="resetPassword",
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
     *     property="country_code",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="phone_number",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="password",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="password_confirmation",
     *     type="string"
     *     ),
     *    )
     *   ),
     *  ),
     * @OA\Response(response=200,description="Response",
     * @OA\JsonContent(@OA\Property(property="token",type="string"))
     *     ),
     * @OA\Response(
     *     response="400",
     *     description="Validation error"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response="401",
     *     description="Not Authorized Invalid or missing Authorization header"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response="403",
     *     description="Not Authorized Invalid or missing Authorization header"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * @OA\Response(
     *     response=500,
     *     description="Unexpected error"
     *     ,@OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     * )
     */

    public function resetPassword(Request $request)
    {
        try {

            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('resetPassword');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }
            
            $user = User::select('id','fullname','email','country_code','phone_number')->where('country_code',$request->country_code)->where('phone_number',$request->phone_number)->first();
            if(!$user)
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('User does not exist')]);
            }

            $user->password = Hash::make($input['password']);
            $user->save();
            
            return $this->successResponse($user->toArray(),('Your password changed successfully'));

        } catch (NotFoundHttpException $ex) {
            return $this->notFoundRequest($ex);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }

    /**
     * Swagger defination changePassword
     *
     * @OA\Post(
     *     tags={"Authentication"},
     *     path="/changePassword",
     *     description="changePassword",
     *     summary="changePassword",
     *     operationId="changePassword",
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
     *     property="country_code",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="phone_number",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="old_password",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="new_password",
     *     type="string"
     *     ),
     * @OA\Property(
     *     property="confirm_new_password",
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

    public function changePassword(Request $request)
    {
        try {

            $input = $request->all();

            $requiredParams = $this->requiredRequestParams('changePassword');
            $validator = Validator::make($input, $requiredParams);
            if ($validator->fails()) {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array(implode(', ', $validator->errors()->all()))]);
            }
            
            $user = User::select('id','fullname','email','country_code','phone_number','password')->where('country_code',$request->country_code)->where('phone_number',$request->phone_number)->first();
            if(!$user)
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('User does not exist')]);
            }

            if(Hash::check($input['old_password'], $user->password))
            {
                if($input['new_password'] == $input['confirm_new_password'])
                {
                    $user->password = Hash::make($input['new_password']);
                    $user->save();
                    unset($user['updated_at']);
                    return $this->successResponse($user->toArray(),('Your password changed successfully'));
                }
                else
                {
                    return response()->json(['status' => "false", 'data' => "", 'messages' => array('The password confirmation does not match')]);
                }
            }
            else
            {
                return response()->json(['status' => "false", 'data' => "", 'messages' => array('The old password does not match')]);
            }

        } catch (NotFoundHttpException $ex) {
            return $this->notFoundRequest($ex);
        } catch (\Exception $ex) {
            return $this->sendErrorResponse($ex);
        }
    }


    /**
     * Swagger defination for logout
     *
     * @OA\Post(
     *     tags={"Authentication"},
     *     path="/logout",
     *     description="
     *  User logout",
     *     summary="User logout",
     *     operationId="UserWebAppLogout",
     * @OA\Parameter(
     *     name="Content-Language",
     *     in="header",
     *     description="Content-Language",
     *     required=false,@OA\Schema(type="string")
     *     ),
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

    /**
     * Logout endpoint
     *
     * @return json response
     */
    public function logout()
    {
        if (Auth::check()) {
            $accessToken = $this->request->user()->token();
            $res = OauthAccessToken::where('id', $accessToken->id)->delete();
            if ($res) {
                return $this->successResponse([], 'Logout Success');
            }
            return $this->sendBadRequest();
        }
        return $this->sendUnauthorised();
    }

    public function requiredRequestParams(string $action)
    {
        switch ($action) {
            case 'uniqueValidation':
                $params = [
                    'email' => 'required|email|unique:users',
                    'phone_number' => 'required|numeric|unique:users',
                ];
                break;
            case 'signup':
                $params = [
                    'fullname' => 'required|min:3',
                    'email' => 'required|email|unique:users',
                    'country_code' => 'required',
                    'phone_number' => 'required|numeric|unique:users',
                    'password' => 'required|min:8',
                ];
                break;
            case 'login':
                $params = [
                    'country_code' => 'required',
                    'phone_number' => 'required',
                    'password' => 'required|min:8',
                ];
                break;
            case 'forgetPassword':
                $params = [
                    'country_code' => 'required|exists:users,country_code',
                    'phone_number' => 'required|exists:users,phone_number',
                ];
                break;
            case 'resetPassword':
                $params = [
                    'country_code' => 'required|exists:users,country_code',
                    'phone_number' => 'required|exists:users,phone_number',
                    'password' => 'required|confirmed|min:8',
                ];
                break;
            case 'changePassword':
                $params = [
                    'country_code' => 'required|exists:users,country_code',
                    'phone_number' => 'required|exists:users,phone_number',
                    'old_password' => 'required|min:8',
                    'new_password' => 'required|min:8',
                    'confirm_new_password' => 'required|min:8',
                ];
                break;
            default:
                $params = [];
                break;
        }
        return $params;
    }
}
