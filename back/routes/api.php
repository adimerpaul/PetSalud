<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MascotaController;
use App\Http\Controllers\VeterinariaController;
use App\Http\Controllers\HistorialClinicoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\TratamientoController;
use App\Http\Controllers\TratamientoProductoController;

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
    Route::put('/historiales/{id}', [HistorialClinicoController::class, 'update']);
//    delete
    Route::delete('/historiales/{id}', [HistorialClinicoController::class, 'destroy']);

    Route::get('productos', [ProductoController::class, 'index']);
    Route::get('productosAll', [ProductoController::class, 'all']);
    Route::post('productos', [ProductoController::class, 'store']);
    Route::put('productos/{id}', [ProductoController::class, 'update']);
    Route::delete('productos/{id}', [ProductoController::class, 'destroy']);
    Route::post('productos/{id}/imagen', [ProductoController::class, 'imagen']);

    Route::get('/historiales/{historialId}/tratamientos', [TratamientoController::class, 'indexByHistorial']);

// CRUD tratamientos (con productos incluidos)
    Route::post('/tratamientos', [TratamientoController::class, 'store']);
    Route::put('/tratamientos/{id}', [TratamientoController::class, 'update']);
    Route::delete('/tratamientos/{id}', [TratamientoController::class, 'destroy']);

// opcional CRUD productos del tratamiento (si deseas mantenerlo)
    Route::post('/tratamiento-productos', [TratamientoProductoController::class,'store']);
    Route::put('/tratamiento-productos/{id}', [TratamientoProductoController::class,'update']);
    Route::delete('/tratamiento-productos/{id}', [TratamientoProductoController::class,'destroy']);
});
Route::get('/historiales/{id}/pdf', [HistorialClinicoController::class, 'pdf']);
//http://localhost:8000/api/tratamientos/3/pdf
Route::get('/tratamientos/{id}/pdf', [TratamientoController::class, 'pdf']);
Route::get('/mascotas/{id}/pdf', [MascotaController::class, 'pdf']);


