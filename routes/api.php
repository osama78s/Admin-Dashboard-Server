<?php

use App\Http\Controllers\Admin\GetUsersController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SubcategoriesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifyCodeController;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Users
Route::group(['prefix' => 'users'], function() {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('/forget-password', [UserController::class, 'ForgetPassword']);
    Route::group(['middleware' => 'auth:sanctum'], function (){
        Route::delete('delete', [UserController::class, 'delete']);
        Route::get('/subcategories-brands', [UserController::class, 'getSubcategoriesAndBrands']);
        Route::post('update', [UserController::class, 'update']);
        Route::get('logout', [AuthController::class, 'logout']);
        Route::post('send-code', [VerifyCodeController::class, 'sendCode']);
        Route::post('check-code', [VerifyCodeController::class, 'checkCode']);
        Route::get('/search/product', [UserController::class, 'searchProduct']);
        Route::get('/search/brand', [UserController::class, 'searchBrand']);
        Route::get('/search/category', [UserController::class, 'searchCategory']);
        Route::get('/search/subcategory', [UserController::class, 'searchSubcategory']);
        Route::get('/search/user', [UserController::class, 'searchUser'])->middleware('ckeckUser');
    });
});

// admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum', 'ckeckUser']], function() {
    Route::group(['prefix' => 'updateRole'], function(){
        Route::post('/{id}', [AdminController::class, 'updateUserRole']);
    });
    Route::group(['prefix' => 'users'], function() {
        Route::get('/usersLength', [AdminController::class, 'getAllUsersWithoutPagination']);
        Route::get('/', [AdminController::class, 'getAllUsers']);
        Route::delete('/delete/{id}', [AdminController::class, 'deleteUser']);
    });
});

// products 
Route::group(['prefix' => 'products', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [ProductsController::class, 'read']);
    Route::get('/{id}', [ProductsController::class, 'getProduct']);
    Route::post('/create', [ProductsController::class, 'create']);
    Route::post('/update/{id}', [ProductsController::class, 'update']);
    Route::delete('/delete/{id}', [ProductsController::class, 'delete']);
});

// subcategories 
Route::group(['prefix' => 'subcategories', 'middleware' => 'auth:sanctum'], function() {
    Route::get('/', [SubcategoriesController::class, 'read']);
    Route::get('/{id}', [SubcategoriesController::class, 'show']);
    Route::post('/create', [SubcategoriesController::class, 'create']);
    Route::post('/update/{id}', [SubcategoriesController::class, 'update']);
    Route::delete('/delete/{id}', [SubcategoriesController::class, 'delete']);
});

// brands
Route::group(['prefix' => 'brands', 'middleware' => 'auth:sanctum'], function() {
    Route::get('/', [BrandsController::class, 'read']);
    Route::get('/{id}', [BrandsController::class, 'show']);
    Route::post('/create', [BrandsController::class, 'create']);
    Route::post('/update/{id}', [BrandsController::class, 'update']);
    Route::delete('/delete/{id}', [BrandsController::class, 'delete']);
});

// categories
Route::group(['prefix' => 'categories', 'middleware' => 'auth:sanctum'], function() {
    Route::get('/', [CategoriesController::class, 'read']);
    Route::get('/{id}', [CategoriesController::class, 'show']);
    Route::post('/create', [CategoriesController::class, 'create']);
    Route::post('/update/{id}', [CategoriesController::class, 'update']);
    Route::delete('/delete/{id}', [CategoriesController::class, 'delete']);
});



