<?php

use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\ReservasiController;
use App\Http\Controllers\Api\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get("/stuff", function() {
    return ["a" => 1];
});

Route::prefix('/user')->group(function () {
    Route::post('/register', RegisterController::class)->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth.jwt')->group(function (){
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/current-user', [AuthController::class, 'me']);
        Route::patch('/update', [UserController::class, 'updateProfile']);

        Route::get('/reservations', [ReservasiController::class, 'getUserReservationHistory']);
        Route::post('/reservation/new', [ReservasiController::class, 'newReservation']);
    });
});

Route::prefix('/admin')->middleware('auth.jwt')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::prefix('/car')->group(function () {
            Route::post('/add', [CarController::class, 'addNew']);
            Route::patch('/update/{id}', [CarController::class, 'update']);
            Route::delete('/delete/{id}', [CarController::class, 'deleteCar']);
        });

        Route::prefix('/reservations')->group(function () {
            Route::get('/all', [ReservasiController::class, 'getAllReservations']);
        });
    });
});

Route::prefix('/car')->middleware('auth.jwt')->group(function() {
    Route::get('/all', [CarController::class, 'getAll']);
    Route::get('/{id}', [CarController::class, 'getByID']);
    Route::get('/category/{id_kategori}', [CarController::class, 'getCarsByCategory']);
    Route::get('/status/{id_status}', [CarController::class, 'getCarsByStatusID']);
});
