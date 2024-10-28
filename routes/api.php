<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Login/Register Api
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// Protected API routes here
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::post('/forgot-password', [LoginController::class, 'changePassword']);

    //Users Api 
    Route::get('/getUser', [UserController::class, 'show']);
    Route::put('/userUpdate', [UserController::class, 'update']);

    //wishlist Apis 
    Route::post('/addWishlist', [CartController::class, 'addToWishlist']);
    Route::get('/getWishlist', [CartController::class, 'getWishlist']);
    Route::delete('/wishlist/{productId}', [CartController::class, 'removeFromWishlist']);
    Route::post('/wishlist/move-to-cart', [CartController::class, 'moveToCart']);

    //OrdersApi 

    Route::get('/my-orders-list/{user_id}', [OrderController::class, 'getOrdersList']);
});
Route::get('/my-orders-details/{order_id}', [OrderController::class, 'getOrdersDetails']);

Route::get('/my-account/{user_id}', [UserController::class, 'getUserAccountDetails']);
//password reset
Route::post('/forgot-password', [LoginController::class, 'forgotPassword']);
Route::post('/reset-password', [LoginController::class, 'resetPassword']);


//categories Apis
Route::get('/categories', [CategoryController::class, 'getCategories']);
Route::get('/categories/featured', [CategoryController::class, 'getFeaturedCategories']);
Route::get('/category/{id}', [CategoryController::class, 'getCategory']);
Route::get('/subcategory/{id}', [CategoryController::class, 'getSubcategory']);

//product Apis
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/product/{id}', [ProductController::class, 'getProduct']);

//cart Apis
Route::get('/cart/{id}', [CartController::class, 'getCart']);
Route::post('/addtocart', [CartController::class, 'addToCart']);
Route::put('/cart/updateItem/{itemId}', [CartController::class, 'updateCartItem']);
Route::delete('/cart/removeItem/{itemId}', [CartController::class, 'removeCartItem']);
Route::delete('/clearCart', [CartController::class, 'clearCart']);

Route::post('/cart/item/select/{cart_itemId}', [CartController::class, 'toggleSelectItem']);
Route::post('/cart/items/select/{cart_id}', [CartController::class, 'toggleSelectAllItems']);
Route::delete('/cart/items/remove/{cart_id}', [CartController::class, 'removeAllItemsFromCart']);


// Customer Addresss apis 
Route::get('/addresses/{customer_id}', [CartController::class, 'index']);
Route::get('/getAddresses/{id}', [CartController::class, 'show']);

Route::post('/storeAddresses', [CartController::class, 'store']);


Route::put('/updateAddresses/{id}', [CartController::class, 'update']);
Route::delete('/deleteAddresses/{id}', [CartController::class, 'destroy']);
Route::post('/cart/attach-address', [CartController::class, 'attachAddressToCart']);

//paymentApis 
Route::post('/order/create', [PaymentController::class, 'createOrder']);
Route::post('/order/create-cod-order', [PaymentController::class, 'createCodOrder']);
Route::post('/order/payment-success', [PaymentController::class, 'handlePaymentCallback']);
Route::post('/order/payment-failed', [PaymentController::class, 'paymentFailed']);
Route::post('/confirm-cod-payment', [PaymentController::class, 'confirmCodPayment']);

//ContactApi 
Route::post('/contact/save', [ContactController::class, 'saveContactInfo']);

// reviews api
Route::post('/reviews', [ContactController::class, 'store']);
// Get latest reviews for a product
Route::get('/products/{productId}/reviews', [ContactController::class, 'getProductReviews']);
