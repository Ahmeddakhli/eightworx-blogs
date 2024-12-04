<?php

use Illuminate\Support\Facades\Route;
use eightworx\Blogs\Http\Controllers\Api\V1\BlogsController;
use eightworx\Blogs\Http\Controllers\Api\V1\Front\BlogsController as FrontBlogsController;
/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::group(['prefix' => 'v1'], function () {
    Route::group(['middleware' => ['localization']], function () {
        Route::group(['prefix' => 'blogs'], function () {
            Route::group(['middleware' => ['auth:sanctum','role:user_roles_admin']], function () {
                Route::get('blog', [BlogsController::class, 'index'])->middleware('limitedLength:50');
                Route::resource('blog', BlogsController::class)->except(['index']);
                Route::post('autocomplete', [BlogsController::class, 'autocomplete'])->middleware('limitedLength:50');
            });
            Route::group(['prefix' => 'front'], function () {
                Route::post('/', [FrontBlogsController::class, 'index'])->middleware('limitedLength:50');
                Route::get('show', [FrontBlogsController::class, 'show']);
                Route::post('paginated-blogs', [FrontBlogsController::class, 'paginatedBlogs'])->middleware('limitedLength:50');
            });
        });
    });
});
