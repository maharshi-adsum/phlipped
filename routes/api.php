<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::namespace('Api')->group(
    function () {
        Route::get('swagger', 'SwaggerController@listItem');
        Route::post('login', 'AuthController@login');
        Route::post('signup', 'AuthController@signUp');

        //password
        Route::post('forgetpassword', 'AuthController@forgetPassword');
        Route::post('otpVerified','AuthController@otpVerified');
        Route::post('resetPassword','AuthController@resetPassword');
        
        Route::group(
            ['middleware' => ['api', 'auth:admin,api']],
            function () {
                //logout
                Route::post('logout', 'AuthController@logout');

                //password
                Route::post('changePassword','AuthController@changePassword');

                //user profile
                Route::post('userProfileGet','UserController@userProfileGet');
                Route::post('userProfileUpdate','UserController@userProfileUpdate');

                //buyer post product
                Route::post('buyerPostProduct','BuyerController@buyerPostProduct');
            }
        );
    }
);

Route::fallback(
    function () {
        return response()->json(
            [
                'file' => __FILE__,
                'line' => __LINE__,
                'code' => 404,
                'message' => 'Not Found',
                'trace' => null,
                'response' => [
                    __('errors.not_found'),
                ],
            ],
            404,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }
)->name('Api.NotFound');
