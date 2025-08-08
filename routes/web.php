<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Post Listing Page
Route::get('/posts', function () {
    return view('pages.posts.index');
});
// routes/web.php
Route::get('/posts/suggestions', [PostController::class, 'suggestions'])->name('posts.suggestions');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [PostController::class, 'create'])->middleware('auth')->name('posts.create');
Route::post('/posts', [PostController::class, 'store'])->middleware('auth')->name('posts.store');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->middleware('auth')->name('posts.edit');
Route::post('/posts/{post}', [PostController::class, 'update'])->middleware('auth')->name('posts.update');
Route::post('/posts/{post}/delete', [PostController::class, 'destroy'])->middleware('auth')->name('posts.destroy');
// Admin Category CRUD Page (admin only)
Route::middleware(['auth', 'is_admin'])->prefix('admin/categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/create', [CategoryController::class, 'store'])->name('categories.store');
    Route::post('/{category}/edit', [CategoryController::class, 'update'])->name('categories.update');
    Route::post('/{category}/delete', [CategoryController::class, 'destroy'])->name('categories.destroy');
    // Admin Dashboard
    Route::get('/admin/dashboard', function () {
        return view('pages.admin.dashboard');
    
    });

    Route::post('/posts/{post}/hide', [PostController::class, 'hide'])->name('posts.hide');
    Route::post('/posts/{post}/restore', [PostController::class, 'restore'])->name('posts.restore');
});


// Admin Dashboard
Route::get('/admin/dashboard', function () {
    return view('pages.admin.dashboard');
});

// User Dashboard
Route::get('/user/dashboard', function () {
    return view('pages.user.dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/user/posts', [PostController::class, 'userPosts'])->name('user.posts');
});
