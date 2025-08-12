<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v2\AuthController;
use App\Http\Controllers\Api\v2\CategoryController;
use App\Http\Controllers\Api\v2\PostController;
use App\Http\Controllers\Api\v2\PostDownloadController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are loaded by the RouteServiceProvider within a group
| assigned the "api" middleware group.
|--------------------------------------------------------------------------
*/

// ====================
// Public Auth Routes
// ====================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ====================
// Protected Routes (Sanctum Auth Required)
// ====================
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {

    // ----------- Auth User Info & Logout -----------
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    //Route::post('/logout', [AuthController::class, 'logout']);

    // ====================
    // Post Routes
    // ====================
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::post('/posts', [PostController::class, 'store']); // Uses PostStoreRequest
    Route::put('/posts/{post}', [PostController::class, 'update']); // Uses PostUpdateRequest
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    // ----------- Admin Only: Trashed / Restore / Force Delete -----------
    Route::middleware('role:admin')->group(function () {
        Route::get('/posts-trashed', [PostController::class, 'trashed']);
        Route::post('/posts/{id}/restore', [PostController::class, 'restore']);
        Route::delete('/posts/{id}/force-delete', [PostController::class, 'forceDelete']);
        

        Route::post('/posts/download', [PostDownloadController::class, 'download']);

    });

    // ====================
    // Category Routes
    // ====================
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    // ----------- Admin Only: Category Management -----------
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);   // Uses CategoryStoreRequest
        Route::put('/categories/{category}', [CategoryController::class, 'update']); // Uses CategoryUpdateRequest
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });
});
