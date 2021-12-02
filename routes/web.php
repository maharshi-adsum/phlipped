<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect("/api-view", "public/swagger-ui");

Route::group(['middleware' => 'adminAuth'], function () 
{
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::get('/admin', function () {
        return view('auth.login');
    })->name('admin');
    
    Route::post('/login','LoginController@login')->name('admin-login'); 
    Route::get('/logout','LoginController@logout')->name('admin-logout'); 

    Route::get('/', function () {
        return redirect()->route('admin');
    });

    Route::get('index','DashboardController@admin')->name('index');

    Route::group(['prefix' => '/manage_users'], function(){
        Route::get('listUsersIndex','UserController@listUsersIndex')->name('listUsersIndex');
        Route::post('usersList','UserController@usersList')->name('usersList');
    });

    Route::group(['prefix' => '/manage_setting'], function(){
        Route::get('setting','LoginController@adminSetting')->name('setting');
        Route::post('addUpdateAdminProfile','LoginController@addUpdateAdminProfile')->name('addUpdateAdminProfile');
        Route::post('addUpdateAdminPassword','LoginController@addUpdateAdminPassword')->name('addUpdateAdminPassword');
    });

    Route::group(['prefix' => '/manage_buyer_product'], function(){
        Route::get('buyerProductIndex','BuyerProductController@buyerProductIndex')->name('buyerProductIndex');
        Route::post('buyerProductList','BuyerProductController@buyerProductList')->name('buyerProductList');
        Route::get('productView','BuyerProductController@productView')->name('productView');
        Route::post('productApprove','BuyerProductController@productApprove')->name('productApprove');
        Route::post('productDisapprove','BuyerProductController@productDisapprove')->name('productDisapprove');
    });
});