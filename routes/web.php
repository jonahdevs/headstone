<?php

use App\Http\Controllers\Backend\CategoriesController;
use App\Http\Controllers\Backend\CustomersController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\FaqsController;
use App\Http\Controllers\Backend\MaterialsController;
use App\Http\Controllers\Backend\MemorialsController;
use App\Http\Controllers\Backend\NotificationsController;
use App\Http\Controllers\Backend\OrdersController;
use App\Http\Controllers\Backend\PermissionsController;
use App\Http\Controllers\Backend\QuotationsController;
use App\Http\Controllers\Backend\ReviewsController;
use App\Http\Controllers\Backend\RolesController;
use App\Http\Controllers\Backend\TagsController;
use App\Http\Controllers\Backend\TransactionsController;
use App\Http\Controllers\Backend\UsersController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin|manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('dashboard');
    });

    Route::resource('users', UsersController::class);
    Route::resource('customers', CustomersController::class);
    Route::resource('roles', RolesController::class);
    Route::get('permissions', PermissionsController::class)->name('permissions');

    //
    Route::resource('categories', CategoriesController::class);
    Route::resource('memorials', MemorialsController::class);
    Route::resource('materials', MaterialsController::class);
    Route::resource('tags', TagsController::class);

    Route::resource('orders', OrdersController::class);
    Route::resource('transactions', TransactionsController::class);
    Route::resource('quotations', QuotationsController::class);

    Route::resource('reviews', ReviewsController::class);
    Route::resource('faqs', FaqsController::class);
    Route::resource('notifications', controller: NotificationsController::class);
});



require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
