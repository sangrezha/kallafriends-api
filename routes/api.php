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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'Auth\UserAuthController@login');
    Route::post('register', 'Auth\UserAuthController@register');
    Route::post('login-member', 'Auth\MemberAuthController@login');
});

// Route::group([
//     'middleware' => 'auth:api-member'
// ], function() {
//     Route::get('/get-point', function () {
//         echo "www";exit;
//     })->name('get-point');    
// });
Route::group([
    'middleware' => 'auth:api'
], function() {
    Route::get('/get-point', function () {
        echo "www";exit;
    })->name('get-point');    
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
