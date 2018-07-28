<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user()->account;
});
Route::get('/', function (){
    echo "Live and Grow";
});
//Username check
Route::post('/name_available', function (Request $request){
	$users = \App\User::where('username', $request->name)->count();
	return response(["status" => 200, "message" =>"Username available", "value" => ($users == 0)], 200);
});
//Auth routes
Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginControler@logout');
//Card edit routes
Route::prefix('card')->group(function (){
	Route::post('/edit', 'CardController@editCard');
	Route::post('/create', 'CardController@createCard');
});
Route::group(['middleware' => 'auth:api'], function (){
    //Unit recharge routes
    Route::prefix('recharge')->group(function (){
        Route::post('/make', [
            'uses' => 'RechargeController@redirectToGateway',
            'as' => 'pay'
        ]);
        Route::post('/otp/callback', 'RechargeController@otpCallback');
        Route::get('recharge/callback', 'RechargeController@handleGatewayCallback');
    });

//Purchase routes
    Route::post('pay/{reciever}', 'PurchaseController@makePurchase');
    Route::get('qr-code', function (){
//     $img = QRCode::text('QR Code Generator for Laravel!')->png();
    });
});
