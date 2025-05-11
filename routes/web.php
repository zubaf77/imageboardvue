<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BoardController::class, 'index'])->name('home');

Route::prefix('board')->group(function ()
{
    Route::get('/', [BoardController::class, 'index'])->name('board.index');
    Route::get('/{slug}', [ThreadController::class, 'index'])->name('board.show');
});

Route::prefix('threads')->group(function () {
    Route::post('/store', [ThreadController::class, 'store'])->name('threads.store')->middleware('ban');
});

Route::prefix('posts')->group(function () {
    Route::get('/thread/{thread_id}', [PostController::class, 'index'])->name('posts.index');
    Route::post('/store', [PostController::class, 'store'])->name('posts.store')->middleware('ban');
});

Route::view('/admin/login', 'admin.login')->name('login')->middleware('ban');

Route::prefix('admin')->name('admin.')->group(function () {

    Route::post('login', [AdminController::class, 'login'])->name('do_login')->middleware('ban');

    Route::middleware(['auth:admin', 'admin', 'ban'])->group(function () {

        Route::prefix('boards')->name('boards.')->group(function () {
            Route::get('/', [AdminController::class, 'boardIndex'])->name('index');
            Route::post('store', [BoardController::class, 'store'])->name('store');
            Route::get('create', [BoardController::class, 'create'])->name('create');
            Route::put('{id}', [BoardController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [BoardController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('posts')->name('posts.')->group(function () {
            Route::get('/', [AdminController::class, 'adminPosts'])->name('index');
            Route::delete('{id}', [PostController::class, 'destroy'])->name('delete');
            Route::post('ban/{post}', [AdminController::class, 'banByPost'])->name('ban');
        });

        Route::prefix('threads')->name('threads.')->group(function () {
            Route::get('/', [AdminController::class, 'adminThreads'])->name('index');
            Route::delete('{id}', [ThreadController::class, 'destroy'])->name('delete');
            Route::post('ban/{thread}', [AdminController::class, 'banByThread'])->name('ban');
        });

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'showUsers'])->name('index');
            Route::post('/', [AdminController::class, 'addUser'])->name('add');
            Route::delete('{id}', [AdminController::class, 'userDelete'])->name('destroy');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::view('/', 'admin.profile')->name('index');
            Route::post('/', [AdminController::class, 'profileUpdate'])->name('update');
        });

        Route::post('unban', [AdminController::class, 'unbanIP'])->name('unban.ip');
        Route::post('logout', [AdminController::class, 'logout'])->name('logout');
    });
});

