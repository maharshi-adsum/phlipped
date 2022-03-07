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
        Route::post('uniqueValidation', 'AuthController@uniqueValidation');

        //password
        Route::post('forgetpassword', 'AuthController@forgetPassword');
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
                Route::post('userAddressUpdate','UserController@userAddressUpdate');

                //buyer product
                Route::post('buyerPostProduct','BuyerProductController@buyerPostProduct');
                Route::post('buyerGetProduct','BuyerProductController@buyerGetProduct');

                //got one product
                Route::post('gotOneAllProduct','CommonController@gotOneAllProduct');
                Route::post('gotOneSingleProduct','CommonController@gotOneSingleProduct');

                //seller product
                Route::post('sellerPostProduct','SellerProductController@sellerPostProduct');
                Route::post('sellerGetProduct','SellerProductController@sellerGetProduct');

                //approved seller product
                Route::post('approvedSellerAllProductList','CommonController@approvedSellerAllProductList');
                Route::post('sellerOpenProductList','CommonController@sellerOpenProductList');
                Route::post('approvedSellerOneProductList','CommonController@approvedSellerOneProductList');

                //delete product list
                Route::post('buyerProductDelete','CommonController@buyerProductDelete');
                Route::post('sellerProductDelete','CommonController@sellerProductDelete');

                //wishlist
                Route::post('wishlistAddRemoveSellerProduct','CommonController@wishlistAddRemoveSellerProduct');
                Route::post('getWishlistProduct','CommonController@getWishlistProduct');

                //payment
                Route::post('payment','CommonController@payment');

                //history
                Route::post('productBuyHistory','CommonController@productBuyHistory');
                Route::post('productSellHistory','CommonController@productSellHistory');

                //notification
                Route::post('getNotificationList','CommonController@getNotificationList');
                Route::post('clearNotificationList','CommonController@clearNotificationList');
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
