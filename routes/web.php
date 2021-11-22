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

    Route::group(['prefix' => '/manage_stock'], function(){
        Route::get('stockIndex','StockController@stockIndex')->name('stockIndex');
        Route::post('listStock','StockController@listStock')->name('listStock');
        Route::post('addUpdateStock','StockController@addUpdateStock')->name('addUpdateStock');
        Route::get('editStock','StockController@editStock')->name('editStock');
        Route::post('deleteStock','StockController@deleteStock')->name('deleteStock');
        Route::post('stockChangeActiveStatus','StockController@stockChangeActiveStatus')->name('stockChangeActiveStatus');
    });
});