<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\magentoProductSyncController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'API'], function() {
	Route::get('/syncMgaeProducts/{id}', [ApiController::class, 'syncMgaeProducts']);
	Route::get('/syncWordpressProducts/{id}', [ApiController::class, 'syncWordpressProducts']);
	Route::any('/magentoProductSyncController', [magentoProductSyncController::class, 'magentoProducts']);
});


