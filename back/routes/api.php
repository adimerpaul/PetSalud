<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VeterinariaController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::get('/veterinarias', [VeterinariaController::class, 'index']);
    Route::post('/veterinarias', [VeterinariaController::class, 'store']);
    Route::put('/veterinarias/{veterinaria}', [VeterinariaController::class, 'update']);
    Route::delete('/veterinarias/{veterinaria}', [VeterinariaController::class, 'destroy']);
});

