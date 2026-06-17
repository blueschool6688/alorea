<?php

use Illuminate\Support\Facades\Route;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

Route::group(['prefix' => 'v1', ''], function () {
    Route::group(['controller' => \App\Http\Controllers\Api\AuthController::class], function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
    });

    Route::group(['controller' => \App\Http\Controllers\Api\GeneralController::class], function () {
        Route::get('get-general', 'getGeneral');
        Route::get('get-data-homepage', 'getDataHomepage');
        Route::post('send-contact', 'sendContact');
    });
    Route::post('store-order', [\App\Http\Controllers\Api\OrderController::class, 'storeOrder']);
    Route::get('guest/get-order/{orderId}', [\App\Http\Controllers\Api\OrderController::class, 'getGuestOrder']);

    Route::get('get-collections', [\App\Http\Controllers\Api\GeneralController::class, 'getCollection']);
    Route::post('webhook-service', [\App\Http\Controllers\Api\OrderController::class, 'webhookSepay'])->middleware(\App\Http\Middleware\SepayMiddleware::class);
    Route::get('get-products', [\App\Http\Controllers\Api\GeneralController::class, 'getProductsData']);
    Route::get('get-blogs', [\App\Http\Controllers\Api\GeneralController::class, 'getBlogs']);
    Route::get('get-blogs/detail/{slug}', [\App\Http\Controllers\Api\GeneralController::class, 'getBlogsDetail']);
    Route::get('detail-product/{slug}', [\App\Http\Controllers\Api\GeneralController::class, 'detailProduct']);

    Route::get('stream/video/{mediaId}', function ($mediaId) {
        $media = Media::findOrFail($mediaId);
        return streamMediaVideo($media);
    })
        ->name('api.stream.video');
    Route::get('get-provinces', [\App\Http\Controllers\Api\LocationController::class, 'getProvinces']);
    Route::get('get-wards/{provinceCode}', [\App\Http\Controllers\Api\LocationController::class, 'getWardsByProvince']);
    Route::get('guest-orders/search', [\App\Http\Controllers\Api\GeneralController::class, 'searchOrder']);

    Route::get('payment-status/{order}', [\App\Http\Controllers\Api\OrderController::class, 'paymentStatus']);
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::group(['controller' => \App\Http\Controllers\Api\AuthController::class], function () {
            Route::get('user', 'getUser');
            Route::post('logout', 'logout');
            Route::post('update-profile', 'updateProfile');
            Route::post('change-password', 'changePassword');
        });

        Route::group(['controller' => \App\Http\Controllers\Api\GeneralController::class], function () {
            Route::post('products/{product}/reviews', 'submitReview');
            Route::get('get-paymentUrl/{order}', 'getPaymentURL');
        });

        Route::get('check-flashsale', [\App\Http\Controllers\Api\GeneralController::class, 'checkFlashSale']);



        //order

        Route::get('get-orders', [\App\Http\Controllers\Api\OrderController::class, 'getOrders']);
        Route::get('get-order/{orderId}', [\App\Http\Controllers\Api\OrderController::class, 'getOrder']);
    });
});
