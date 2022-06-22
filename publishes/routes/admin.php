<?php

use Illuminate\Support\Facades\Route;
use Admin\Http\Controllers\NavController;
use Admin\Http\Controllers\HomeController;
use Admin\Http\Controllers\MenuController;
use Admin\Http\Controllers\PageController;
use Admin\Http\Controllers\UserController;
use Admin\Http\Controllers\MediaController;
use Admin\Http\Middleware\AuthenticateAdmin;
use Admin\Http\Controllers\SettingController;
use Admin\Http\Controllers\MenuItemController;
use Admin\Http\Controllers\UserProfileController;
use Admin\Http\Controllers\MediaCollectionController;
use Admin\Http\Controllers\Auth\NewPasswordController;
use Admin\Http\Controllers\Auth\PasswordResetLinkController;
use Admin\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Admin\Http\Controllers\LinkController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::group([
    'middleware' => [
        'api',
        AuthenticateAdmin::class,
        EnsureFrontendRequestsAreStateful::class,
    ],
    'prefix' => 'api',
], function () {

    // settings
    // Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');

    // profile
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile');
    Route::post('/profile/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');    

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
    // users
    Route::get('/users', [UserController::class, 'items'])->name('user.items');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('user.delete');

    // links
    Route::get('/links', LinkController::class)->name('links');

    // media
    Route::get('/media/items', [MediaController::class, 'items'])->name('media.items');
    Route::get('/media/{file}', [MediaController::class, 'item'])->name('media.item');
    Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');
    Route::post('/media/delete', [MediaController::class, 'destroy'])->name('media.destroy');

    // media collections
    Route::get('/media-collections', [MediaCollectionController::class, 'items'])->name('media-collections.items');
    Route::get('/media-collections/{collection}', [MediaCollectionController::class, 'show'])->name('media-collections.show');
    Route::post('/media-collections', [MediaCollectionController::class, 'store'])->name('media-collections.show');
    Route::post('/media-collections/{collection}/upload', [MediaCollectionController::class, 'upload'])->name('media-collections.upload');
    Route::post('/media-collections/{collection}/remove', [MediaCollectionController::class, 'remove'])->name('media-collections.remove');
    Route::post('/media-collections/{collection}/add', [MediaCollectionController::class, 'add'])->name('media-collections.add');
    Route::delete('/media-collections/{collection}', [MediaCollectionController::class, 'destroy'])->name('media-collections.destroy');

    // pages
    Route::get('/pages', [PageController::class, 'items'])->name('pages.items');
    Route::get('/pages/tree', [PageController::class, 'tree'])->name('pages.tree');
    Route::get('/pages/{page}', [PageController::class, 'item'])->name('pages.item');
    Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
    Route::post('/pages/order', [PageController::class, 'order'])->name('pages.order');
    Route::post('/pages/{page}/meta', [PageController::class, 'meta'])->name('pages.meta');
    Route::post('/pages/{page}/upload', [PageController::class, 'upload'])->name('pages.upload');
    Route::post('/pages/{page}/duplicate', [PageController::class, 'duplicate'])->name('pages.duplicate');
    Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');

    // menus
    Route::get('/menus', [MenuController::class, 'items'])->name('menus.items');

    // menu items
    Route::get('/menus/{menu}/items/tree', [MenuItemController::class, 'show'])->name('menus.items.tree');
    Route::post('/menus/{menu}/items/order', [MenuItemController::class, 'order'])->name('menus.items.order');
    Route::post('/menus/{menu}/items', [MenuItemController::class, 'store'])->name('menus.items.store');
    Route::put('/menus/{menu}/items/{item}', [MenuItemController::class, 'update'])->name('menus.items.update');
    Route::delete('/menus/{menu}/items/{item}', [MenuItemController::class, 'destroy'])->name('menus.items.destroy');
});

Route::group([
    'middleware' => ['web', 'guest']
], function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});