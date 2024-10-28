<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AttributeController;


Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('attributes')
        : redirect()->route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    //Category Routes
    Route::get('/admin/category/{id}/form/{parent_id?}', [CategoryController::class, 'addEditCategory'])->name('category.form');
    Route::post('/admin/category/save', [CategoryController::class, 'saveCategory'])->name('category.save');
    Route::get('/admin/subcategories/{parentCategory}', [CategoryController::class, 'fetchSubcategories']);
    Route::get('/admin/categories/{id?}', function () {
        return view('admin.category-list');
    })->name('categories');
    Route::delete('/category/{id}', [CategoryController::class, 'deleteCategory'])->name('category.delete');


    //Product Routes
    Route::get('/admin/product/{id}/form', [ProductController::class, 'addEditProduct'])->name('product.form');
    Route::delete('/product/{id}/delete', [ProductController::class, 'deleteProduct'])->name('product.delete');
    Route::post('/admin/product/save', [ProductController::class, 'saveProduct'])->name('product.save');
    Route::get('/admin/products', function () {
        return view('admin.product-list');
    })->name('products');

    //Attributes Routes
    Route::get('/admin/attributes', function () {
        return view('admin.attribute-list');
    })->name('attributes');
    Route::get('/admin/attribute/{id}/form', [AttributeController::class, 'addEditAttribute'])->name('attribute.form');
    Route::post('/admin/attribute/save', [AttributeController::class, 'saveAttribute'])->name('attribute.save');
    Route::delete('admin/attribute/{id}/delete', [AttributeController::class, 'deleteAttribute'])->name('attribute.delete');


    //Users Routes
    Route::get('/admin/users', function () {
        return view('admin.user-list');
    })->name('users');
    Route::delete('/user/{id}/delete', [RegisterController::class, 'deleteUser'])->name('user.delete');
    Route::get('/admin/user/{id}/form', [RegisterController::class, 'addEditUser'])->name('user.form');
    Route::post('/admin/user/save', [RegisterController::class, 'saveUser'])->name('user.save');


    //Orders Routes
    Route::get('/admin/orders', function () {
        return view('admin.order-list');
    })->name('orders');
    Route::get('/admin/order/{id}/view', [OrderController::class, 'viewOrder'])->name('order.view');
    Route::delete('/admin/order/{id}/delete', [OrderController::class, 'deleteOrder'])->name('order.delete');

});
