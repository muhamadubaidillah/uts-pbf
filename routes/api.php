<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API;

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

## Categories
Route::middleware('jwtAuth')->get('categories', [API\Categories::class, 'read']);
Route::middleware('jwtAuth:admin')->post('categories', [API\Categories::class, 'create']);
Route::middleware('jwtAuth:admin')->put('categories/{id}', [API\Categories::class, 'update']);
Route::middleware('jwtAuth:admin')->delete('categories/{id}', [API\Categories::class, 'delete']);

## Products
Route::middleware('jwtAuth')->get('products', [API\Products::class, 'read']);
Route::middleware('jwtAuth')->post('products', [API\Products::class, 'create']);
Route::middleware('jwtAuth')->put('products/{id}', [API\Products::class, 'update']);
Route::middleware('jwtAuth')->delete('products/{id}', [API\Products::class, 'delete']);

## Register
Route::post('register', [API\Register::class, 'create']);

## Login
Route::post('login', [API\Login::class, 'login']);

## Login with Google
Route::middleware(['web'])->get('oauth/register', [API\Login::class, 'redirectGoogle']);
Route::middleware(['web'])->get('oauth/register/callback', [API\Login::class, 'handleCallback']);
