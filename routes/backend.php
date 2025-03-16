<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ColorController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\InventoryController;
use App\Http\Controllers\Backend\InvoiceController;
use App\Http\Controllers\Backend\ModuleMakerController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SizeController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

//don't remove this comment from route namespace

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LoginController::class, 'loginPage'])->name('home')->middleware('AuthCheck');

Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    Artisan::call('optimize:clear');
    Artisan::call('storage:link');
    Artisan::call('optimize');
    session()->flash('message', 'System Updated Successfully.');

    return redirect()->route('home');
});

Route::group(['as' => 'auth.'], function () {
    Route::get('/login', [LoginController::class, 'loginPage'])->name('login2')->middleware('AuthCheck');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::group(['middleware' => 'AdminAuth'], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('admin', AdminController::class);
    Route::get('admin/{id}/status/{status}/change', [AdminController::class, 'changeStatus'])->name('admin.status.change');

    // for category
    Route::resource('Category', CategoryController::class);
    Route::get('Category/{id}/status/{status}/change', [CategoryController::class, 'changeStatus'])->name('Category.status.change');
    
    // for product
    Route::resource('product', ProductController::class);
    Route::get('product/{id}/status/{status}/change', [ProductController::class, 'changeStatus'])->name('product.status.change');
    Route::get('/product-download-pdf', [ProductController::class, 'downloadPdf'])->name('product.downloadPdf');
    Route::get('/product/category-wise-sizes/{categoryId}', [ProductController::class, 'categoryWiseSizes'])->name('product.categoryWiseSize');

    // for role
    Route::resource('role', RoleController::class);

    // for permission entry
    Route::resource('permission', PermissionController::class);

    // for inventory
    Route::resource('inventory', InventoryController::class);
    Route::get('inventory/{id}/status/{status}/change', [InventoryController::class, 'changeStatus'])->name('inventory.status.change');
 
    // for size
    Route::resource('Size', SizeController::class);
    Route::get('Size/{id}/status/{status}/change', [SizeController::class, 'changeStatus'])->name('Size.status.change');
    Route::get('/sizes/edit-by-category/{category}', [SizeController::class, 'editByCategory'])->name('Size.editByCategory');
    Route::get('/size/delete/{sizeId}', [SizeController::class, 'delete'])->name('Size.delete');

    // for Color
    Route::resource('Color', ColorController::class);
    Route::get('Color/{id}/status/{status}/change', [ColorController::class, 'changeStatus'])->name('Color.status.change');
    
    // for Invoice
    Route::resource('invoice', InvoiceController::class);
    Route::get('invoice/{id}/status/{status}/change', [InvoiceController::class, 'changeStatus'])->name('invoice.status.change');
    Route::get('/product-details/{product_no}', [InvoiceController::class, 'productDetails'])->name('product.details');

    
    //don't remove this comment from route body
});
