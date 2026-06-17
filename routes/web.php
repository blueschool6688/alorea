<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\CollectionsController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PdfController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ScentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        if (auth('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('admin.login');
    })->name('home');
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});


Route::group(['middleware' => 'admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {


    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('clear-cache', function () {
        opcache_reset();
        \Artisan::call('optimize:clear');
    })->name('clear-cache');

    Route::post('/ckeditor/upload', [App\Http\Controllers\Admin\CKEditorController::class, 'upload'])
        ->name('ckeditor.upload');

    Route::post('/tinymce/upload', [App\Http\Controllers\Admin\TinyMCEController::class, 'upload'])
        ->name('tinymce.upload');

    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoriesController::class);
    Route::resource('users', UsersController::class);
    Route::resource('collections', CollectionsController::class);
    Route::resource('flash-sales', FlashSaleController::class);
    Route::resource('blogs', BlogController::class);
    Route::resource('blog-categories', BlogCategoryController::class);
    Route::resource('scents', ScentController::class);
    Route::resource('orders', OrderController::class);


    //PDF


    Route::get('/orders/{order}/download-pdf', [OrderController::class, 'downloadPDF'])
        ->name('orders.download-pdf');

    Route::get('/admin/orders/export-excel', [OrderController::class, 'exportExcel'])
        ->name('orders.export-excel');

    // Additional order routes
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

    Route::post('users/{user}/toggle-status', [UsersController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/ban', [UsersController::class, 'ban'])->name('users.ban');
    Route::post('users/{user}/unban', [UsersController::class, 'unban'])->name('users.unban');

    Route::delete('products/{product}/gallery/remove', [ProductController::class, 'removeGalleryImage'])
        ->name('products.gallery.remove');

    Route::delete('settings/banner/remove', [SettingController::class, 'deleteBanner'])
        ->name('settings.banner.remove');
    Route::get('settings', [SettingController::class, 'show'])->name('settings.show');
    Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
    Route::post('settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');


    Route::delete('collections/{collection}/video', [CollectionsController::class, 'removeVideo'])->name('collections.remove-video');
    Route::post('collections/{collection}/products', [CollectionsController::class, 'addProduct'])->name('collections.add-product');
    Route::delete('collections/{collection}/products/{product}', [CollectionsController::class, 'removeProduct'])->name('collections.remove-product');


    // Flash Sales additional routes
    Route::post('flash-sales/{flashSale}/toggle-status', [FlashSaleController::class, 'toggleStatus'])->name('flash-sales.toggle-status');
    Route::post('flash-sales/{flashSale}/toggle-featured', [FlashSaleController::class, 'toggleFeatured'])->name('flash-sales.toggle-featured');
    Route::delete('flash-sales/{flashSale}/banner', [FlashSaleController::class, 'removeBanner'])->name('flash-sales.remove-banner');

    Route::patch('categories/{category}/toggle-status', [CategoriesController::class, 'toggleStatus'])->name('categories.toggle-status');

    Route::post('blog-categories/{blogCategory}/toggle-status', [BlogCategoryController::class, 'toggleStatus'])->name('blog-categories.toggle-status');
    Route::post('blogs/{blog}/toggle-featured', [BlogController::class, 'toggleFeatured'])->name('blogs.toggle-featured');

    // Scent additional routes
    Route::post('scents/{scent}/toggle-status', [ScentController::class, 'toggleStatus'])->name('scents.toggle-status');
    Route::post('scents/{scent}/toggle-popular', [ScentController::class, 'togglePopular'])->name('scents.toggle-popular');
    Route::post('scents/bulk-action', [ScentController::class, 'bulkAction'])->name('scents.bulk-action');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::resource('contacts', \App\Http\Controllers\Admin\ContactController::class)->except(['create', 'store', 'edit', 'update']);
    Route::post('contacts/{contact}/reply', [\App\Http\Controllers\Admin\ContactController::class, 'reply'])->name('contacts.reply');
    Route::patch('contacts/{contact}/status', [\App\Http\Controllers\Admin\ContactController::class, 'updateStatus'])->name('contacts.update-status');
});







Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
