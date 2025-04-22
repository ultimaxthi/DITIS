<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MeetingRoomController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SelectOptionsController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'reset']);


Route::get('/meeting-rooms', [MeetingRoomController::class, 'index']);
//Route::get('/users/index', [AuthController::class, 'index']);


Route::prefix('selects')->group(function () {
    Route::get('/users', [SelectOptionsController::class, 'getUsers']);
    Route::get('/rooms', [SelectOptionsController::class, 'getRooms']);
    Route::get('/meeting-statuses', [SelectOptionsController::class, 'getMeetingStatuses']);
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/update/{id}', [AuthController::class, 'update']);
    Route::delete('/delete/{id}', [AuthController::class, 'delete']);

    Route::prefix('meeting-rooms')->group(function () {
        Route::get('/', [MeetingRoomController::class, 'index']);
        Route::get('/{id}', [MeetingRoomController::class, 'show']);
        Route::middleware(['role:admin'])->group(function () {
            Route::post('/', [MeetingRoomController::class, 'store']);
            Route::get('/{roomId}/occupancies/day/{date}', [MeetingRoomController::class, 'getOccupiedHours']);
            Route::put('/{id}', [MeetingRoomController::class, 'update']);
            Route::delete('/{id}', [MeetingRoomController::class, 'destroy']);
            Route::get('/occupancies', [MeetingRoomController::class, 'getOccupancyData']);
        });
    });

    Route::prefix('meetings')->group(function () {
        Route::get('/', [MeetingController::class, 'index']);
        Route::get('/day/{date}', [MeetingController::class, 'getMeetingsByDay']);
        Route::get('/my-meetings', [MeetingController::class, 'getMyMeetings']);
        Route::post('/', [MeetingController::class, 'store']);
        Route::get('/{id}', [MeetingController::class, 'show']);
        Route::put('/{id}', [MeetingController::class, 'update']);
        Route::delete('/{id}', [MeetingController::class, 'destroy']);
        Route::put('/{id}/status', [MeetingController::class, 'updateStatus']);
        Route::get('/room-occupaprecncy/{date}', [MeetingController::class, 'getRoomOccupancy']);
        Route::get('/reservations-by-day/{id}', [MeetingController::class, 'getReservationsByDay']);
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class,'update']);
    });

    Route::middleware(['role:admin'])->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'updateWithRole']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/summary-data', [UserController::class, 'summary']);
        Route::get('/{id}', [UserController::class, 'show']);
    });

});
