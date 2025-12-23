<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\VeterinariaController;
use App\Http\Controllers\HistorialClinicoController;
use App\Http\Controllers\ProductoController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateMe']);
    Route::put('/me/password', [AuthController::class, 'updateMyPassword']);
    Route::post('/me/avatar', [AuthController::class, 'updateMyAvatar']);

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::post('/users/{user}/avatar', [UserController::class, 'updateAvatar']);


    Route::get('/veterinaria', [VeterinariaController::class, 'showMine']);
    Route::put('/veterinaria', [VeterinariaController::class, 'updateMine']);

    // uploads
    Route::post('/veterinaria/logo', [VeterinariaController::class, 'uploadLogo']);
    Route::post('/veterinaria/imagen', [VeterinariaController::class, 'uploadImagen']);

    Route::get('/mascotas', [MascotaController::class, 'index']);
    Route::post('/mascotas', [MascotaController::class, 'store']);
    Route::get('/mascotas/{mascota}', [MascotaController::class, 'show']);
    Route::post('/mascotas/{mascota}', [MascotaController::class, 'update']); // para multipart (foto)
    Route::delete('/mascotas/{mascota}', [MascotaController::class, 'destroy']);

    Route::get('/mascotas/{id}/historiales', [HistorialClinicoController::class, 'index']);
    Route::post('/historiales', [HistorialClinicoController::class, 'store']);
    Route::get('/historiales/{id}/pdf', [HistorialClinicoController::class, 'pdf']);

    Route::get('productos', [ProductoController::class, 'index']);
    Route::post('productos', [ProductoController::class, 'store']);
    Route::put('productos/{id}', [ProductoController::class, 'update']);
    Route::delete('productos/{id}', [ProductoController::class, 'destroy']);
    Route::post('productos/{id}/imagen', [ProductoController::class, 'imagen']);
});

