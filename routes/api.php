<?php

use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Company API
Route::prefix('company')
    ->middleware('auth:sanctum')
    ->name('company.')
    ->group(function () {
        Route::get('', [CompanyController::class, 'fetch'])->name('fetch');
        Route::post('', [CompanyController::class, 'create'])->name('create');
        Route::post('update/{id}', [CompanyController::class, 'update'])->name('update');
});

// Auth API
Route::name('auth.')
    ->group(function(){
        Route::post('login', [UserController::class, 'login'])->name('login');
        Route::post('register', [UserController::class, 'register'])->name('register');

        Route::middleware('auth:sanctum')
            ->group(function () {
                Route::get('logout', [UserController::class, 'logout'])->name('logout');
                Route::get('user', [UserController::class, 'fetch'])->name('fetch');
            });
    });

