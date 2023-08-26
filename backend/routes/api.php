<?php

use App\Http\Controllers\Api\AuthController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});



Route::group(['middleware' => 'auth:api_admin'], function () {
    




});

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::middleware(['throttle:login'])->group(function () {
        Route::post('login', 'login');
    });
    Route::post('request-password', 'requestPassword')->name('password.request');
}); //END AUTHENTICATION
