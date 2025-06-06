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
use App\Http\Controllers\Backend\ReportsController;
use App\Http\Controllers\Backend\ReviewsController;
use App\Http\Controllers\Backend\RolesController;
use App\Http\Controllers\Backend\TagsController;
use App\Http\Controllers\Backend\TransactionsController;
use App\Http\Controllers\Backend\UsersController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\Customer\DownloadReceiptController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\QuotationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('test', function () {
    return view('mails.quotation-reply');
});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', AboutController::class)->name('about');

// quotation controller
Route::controller(QuotationController::class)->group(function () {
    Route::get('quotation', 'index')->name('quotation');
    Route::post('quotation', 'store')->name('quotation.store');
});

// contact routes
Route::controller(ContactController::class)->group(function () {
    Route::get('contact', 'index')->name('contact');
    Route::post('contact', 'store')->name('contact.store');
});

Route::controller(\App\Http\Controllers\Frontend\MemorialsController::class)->group(function () {
    Route::get('memorials', 'index')->name('memorials');
    Route::get('memorials/category/{category}', 'byCategory')->name('memorials.byCategory');
    Route::get('memorials/{memorial}', 'show')->name('memorials.show');
});

Route::get('faqs', \App\Http\Controllers\Frontend\FaqsController::class)->name('faqs');

// cart
Route::controller(CartController::class)->group(function () {
    Route::get('cart', 'index')->name('cart');
    Route::post('cart', 'store')->name('cart.store');
    Route::put('cart/{itemId}', 'update')->name('cart.update');
    Route::delete('cart/{itemId}', 'destroy')->name('cart.destroy');
});

// checkout
Route::controller(CheckoutController::class)->middleware('auth')->group(function () {
    Route::post('checkout', 'index')->name('checkout');
    Route::get('checkout/callback', 'callback')->name('checkout.callback');
    Route::get('checkout/success', 'checkoutSuccess')->name('checkout.success');
    Route::get('checkout/error', 'checkoutError')->name('checkout.error');
});

// customers routes

Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {

    Route::controller(AccountController::class)->group(function () {
        Route::get('account', 'index')->name('account');
        Route::put('account', 'update')->name('account.update');
        Route::put('account/password', 'password')->name('account.password');
    });

    Route::controller(\App\Http\Controllers\Customer\OrdersController::class)->group(function () {
        Route::get('orders', 'index')->name('orders');
        Route::get('orders/{order}', 'show')->name('orders.show');
    });

    Route::get('orders/{order}/download', DownloadReceiptController::class)->name('orders.download');

    Route::controller(\App\Http\Controllers\Customer\NotificationsController::class)->group(function () {
        Route::get('notifications', 'index')->name('notifications');
        Route::get('notifications/unread', 'unread')->name('notifications.unread');
        Route::post('notifications/mark-as-read', 'markAsRead')->name('notifications.markAsRead');
    });
});

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

    Route::controller(\App\Http\Controllers\Backend\DownloadReceiptController::class)->group(function () {
        Route::get('orders/{order}/download', 'orderDownload')->name('orders.download');
        Route::get('transactions/{transaction}/download', 'transactionDownload')->name('transactions.download');
    });

    Route::resource('reviews', ReviewsController::class);
    Route::resource('faqs', FaqsController::class);
    Route::controller(NotificationsController::class)->group(function () {
        Route::get('notifications', 'index')->name('notifications');
        Route::get('notifications/unread', 'unread')->name('notifications.unread');
        Route::post('notifications/mark-as-read', 'markAsRead')->name('notifications.markAsRead');
    });

    Route::controller(ReportsController::class)->group(function () {
        Route::get('reports/{resource?}', 'index')->name('reports');
        Route::get('reports/{resource}/generate', 'generate')->name('reports.generate');
    });
});



require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
