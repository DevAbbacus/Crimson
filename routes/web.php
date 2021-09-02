<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurrentRmsInformatonController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\PackageController;

// Current RMS Setting
Route::put('/user/current-rms-information', [CurrentRmsInformatonController::class, 'update'])
    ->name('current-rms-information.update')
    ->middleware('auth:sanctum');

Route::get('/sync-products', [ProductController::class, 'sync'])
    ->name('products.sync')
    ->middleware(['auth:sanctum', 'verified']);
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::group([
    'middleware' => ['auth:sanctum', 'crms.info', 'verified']
], function () {


    // Products
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', [ProductController::class, 'index'])
        ->name('dashboard');
        //->middleware(['remember']);

    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    // Route::post('/products/{product}', [ProductController::class, 'update'])->name('products.update');

    Route::get('/productslist', [ProductController::class, 'list'])->name('products.list');


    // Members
    Route::get('/members', [MemberController::class, 'index'])->name('members');

    // Opportunities
    Route::get('/opportunities', [OpportunityController::class, 'index'])->name('opportunities');
    //Route::any('/rental_opportunities', [OpportunityController::class, 'Rental_opportunitie'])->name('opportunities');

    //Subcategory
    Route::resource('/subcategory', SubCategoryController::class);
    Route::get('/categorylist', [SubCategoryController::class, 'ProductGroup']);
    Route::get('/categorywithparent', [SubCategoryController::class, 'ProductGroupWithSub']);
    Route::get('/subcategorylist', [SubCategoryController::class, 'subProductGroup']);

    // Package
    Route::resource('/packages', PackageController::class);
    Route::get('/products/load', [ProductController::class, 'forceProductSync']);
    Route::delete('/products/delete-image', [ProductController::class, 'deleteImage'])->name('products.delete.image');

    Route::get('/settings', [Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController::class, 'show']);
});
