<?php

use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\QuestionsController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\WorkingHoursController;
use App\Http\Controllers\ServiceController;
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
Route::post('reservation/table',[ReservationController::class,'table']);
Route::delete('reservation/table/{reservation}/delete',[ReservationController::class,'delete']);
Route::post('reservation/table/{reservation}/visited',[ReservationController::class,'visited']);


Route::post('working_hours',[WorkingHoursController::class,'workingHours']);
Route::post('closed_days',[WorkingHoursController::class,'closedDays']);

Route::post('questions/create',[QuestionsController::class,'create']);
Route::get('questions/edit_user',[QuestionsController::class,'editUser']);


Route::post('comments/services',[CommentController::class,'serviceComment']);
Route::get('/comments/services/show',[CommentController::class,'serviceShow']);
Route::post('comments/clinic',[CommentController::class,'clinicComment']);
Route::get('/comments/clinic/show',[CommentController::class,'clinicShow']);
Route::post('comments/product',[CommentController::class,'productComment']);
Route::get('/comments/product/show',[CommentController::class,'productShow']);
Route::post('comments/article',[CommentController::class,'articleComment']);
Route::get('/comments/article/show',[CommentController::class,'articleShow']);
Route::get('comments/table',[CommentController::class,'table']);
Route::delete('comments/table/{comment}/delete',[CommentController::class,'delete']);
Route::put('comments/table/{comment}/confirmation',[CommentController::class,'confirmation']);
