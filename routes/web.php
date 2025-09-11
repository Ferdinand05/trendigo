<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Products\CategoryController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\ProfileUserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('products/category/{slug}', [HomeController::class, 'productInCategory'])->name('product.in.category');
Route::get('product/{slug}', [HomeController::class, 'detailProduct'])->name('detail.product');
Route::middleware(['auth', 'verified'])->group(function () {

    // profile user
    Route::get('user/profile', [ProfileUserController::class, 'index'])->name('profile.user.index');
    Route::patch('user/profile', [ProfileUserController::class, 'update'])->name('profile.user.save');
    Route::get('user/profile/orders', [ProfileUserController::class, 'showOrder'])->name('profile.show.order');
    Route::get('user/profile/setting', [ProfileUserController::class, 'showSetting'])->name('profile.show.setting');

    // add product to cart, must login
    Route::post('/product/add-to-cart', [CartController::class, 'addToCart'])->name('add.to.cart');

    // reduce quantity cart
    Route::put('/cart/reduce-quantity', [CartController::class, 'reduceQuantity'])->name('reduce.qty');
    // add quantity cart
    Route::put('/cart/add-quantity', [CartController::class, 'addQuantity'])->name('add.qty');
    // delete cart item
    Route::delete('/cart/delete-cart-item', [CartController::class, 'deleteCartItem'])->name('delete.cart.item');

    Route::get('checkout', [CheckoutController::class, 'index'])->name('checkout.index');


    // Shipping controller, check province,city, district,sub disctrict, and calculate cost
    Route::get('get-provinces', [ShippingController::class, 'getProvinces'])->name('get.provinces');
    Route::get('get-cities/{province_id}', [ShippingController::class, 'getCities'])->name('get.cities');
    Route::get('get-districts/{city_id}', [ShippingController::class, 'getDistricts'])->name('get.districts');
    Route::get('get-sub-disctricts/{district_id}', [ShippingController::class, 'getSubDistricts'])->name('get.sub.districts');
    Route::post('calculate-domestic-cost', [ShippingController::class, 'calculateDomesticCost'])->name('calculate.cost');
    // Route::post('save-address', [ShippingController::class, 'saveAddress'])->name('save.address');

    // create charge midtrans / snap pay
    Route::post('/checkout/create-charge', [CheckoutController::class, 'createCharge'])->name('create.charge');





    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // products
    Route::resource('/products', ProductController::class);
    Route::post('/products/update-status', [ProductController::class, 'updateStatus'])->name('product.update.status');

    // dashboard users route
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');


    // dashboard roles
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');




    //dashboard category
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');


    // dashboard Order
    Route::get('dashboard/orders', [OrderController::class, 'index'])->name('orders.index');
});




require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
