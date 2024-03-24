<?php

use App\Http\Controllers\Api\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('reservation/date',[ReservationController::class,'checkDate']);
Route::post('reservation/time',[ReservationController::class,'checkTime']);
Route::post('reservation/sendotpcode',[ReservationController::class,'sendOtpCode']);
Route::post('reservation/confirmationcode',[ReservationController::class,'confirmationCode']);
Route::post('reservation/submit_information',[ReservationController::class,'submitInformation']);
Route::post('reservation',[ReservationController::class,'reservation']);
Route::post('reservation/{user}/delete',[ReservationController::class,'deleteReservation']);
