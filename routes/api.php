<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\API\VentaController;

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
Route::get('/products', [ProductController::class, 'index'])->middleware('auth:sanctum');        // Listar todos los productos
Route::post('/products', [ProductController::class, 'store'])->middleware('auth:sanctum');       // Crear un nuevo producto
Route::put('/products/{product}', [ProductController::class, 'update'])->middleware('auth:sanctum'); // Actualizar un producto
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->middleware('auth:sanctum'); // Eliminar un producto

Route::resource('products', ProductController::class)->middleware('auth:sanctum');

Route::post('/ventas', [VentaController::class, 'store'])->middleware('auth:sanctum');   // Crear una nueva venta
Route::get('/ventas', [VentaController::class, 'index'])->middleware('auth:sanctum'); //listar ventas
 
Route::post('register', [AuthenticationController::class, 'register']);
Route::post('login', [AuthenticationController::class, 'login']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
