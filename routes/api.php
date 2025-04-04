<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;



use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\UserGoalController;
use App\Http\Controllers\Api\FoodScanController;
use App\Http\Controllers\Api\DailyDataController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('user', [UserController::class, 'store']);

// Protected routes (Require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Route::apiResource('daily-data', DailyDataController::class);
    Route::post('daily-data', [DailyDataController::class, 'store']);
    Route::get('daily-data', [DailyDataController::class, 'show']);

    Route::apiResource('user', UserController::class)->except(['store']);
    Route::get('user-goal', [UserGoalController::class, 'index']);
    Route::post('user-goal', [UserGoalController::class, 'store']);
    Route::get('user-goal/{id}', [UserGoalController::class, 'show']);
    Route::post('scan', [FoodScanController::class, 'scan']);
    Route::get('scan', [FoodScanController::class, 'allScans']);
    Route::delete('scan/{id}', [FoodScanController::class, 'deleteScan']);
    Route::post("/logout",function(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(
            ["success" => ['message' => 'Logout Successfully']], 200);
    });

});

Route::middleware('guest')->group(function () {
    Route::post('/login', [UserController::class, 'login']);

    Route::post('/reset-password/send-otp', [PasswordResetController::class, 'sendOtp']);
    Route::post('/reset-password/verify-otp', [PasswordResetController::class, 'verifyOtp']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
});

// Route::apiResource('daily-data', DailyDataController::class);

// Route::middleware('auth:sanctum')->get('/daily-data', [DailyDataController::class, 'index']);
// Route::middleware('auth:sanctum')->post('/daily-data', [DailyDataController::class, 'store']);
// Route::middleware('auth:sanctum')->get('/daily-data/show', [DailyDataController::class, 'show']);
// Route::middleware('auth:sanctum')->put('/daily-data/{id}', [DailyDataController::class, 'update']);
// Route::middleware('auth:sanctum')->delete('/daily-data/{id}', [DailyDataController::class, 'destroy']);


/**
 * POST            api/login .......................................................................
 * GET|HEAD        api/user .................................. user.index › Api\UserController@index
 * POST            api/user .................................. user.store › Api\UserController@store
 * GET|HEAD        api/user/{user} ............................. user.show › Api\UserController@show
 *PUT|PATCH       api/user/{user} ......................... user.update › Api\UserController@update
 *DELETE          api/user/{user} ....................... user.destroy › Api\UserController@destroy
 */
