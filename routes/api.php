<?php

use App\Http\Controllers\CaterogyController;
use App\Http\Controllers\ExchangesController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\NbuController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// setting manipulation
Route::prefix('settings')->controller(SettingController::class)->group(function () {
    Route::get('get', 'getSettings')->name('getSettings');
});

// nbu manipulation
Route::get('nbu/save', [NbuController::class, 'save']);

// expenses manipulation
Route::prefix('expenses')->controller(ExpensesController::class)->group(function () {
    Route::get('get', 'getExpenses')->name('getExpenses');
});

// products manipulation
Route::prefix('products')->controller(ProductsController::class)->group(function () {
    Route::post('new', 'createProduct')->name('createProduct');
    Route::post('get{caterogy_id?}', 'getProducts')->name('getProducts');
    Route::put('edit', 'updateProduct')->name('updateProduct');
    Route::post('make', 'MakeProduct')->name('MakeProduct');
    Route::get('getPriceLog', 'getProductsPriceLog')->name('getProductsPriceLog');
    Route::get('getProductsInput', 'getProductsInput')->name('getProductsInput');
});

// caterogies manipulation
Route::prefix('caterogies')->controller(CaterogyController::class)->group(function () {
    Route::post('new', 'createCaterogy')->name('createCaterogy');
    Route::get('get', 'getCaterogies')->name('getCaterogies');
    Route::put('edit', 'updateCaterogy')->name('updateCaterogy');
});

// partners manipulation
Route::prefix('partners')->controller(PartnersController::class)->group(function () {
    Route::post('new', 'create')->name('create');
    Route::get('get', 'get')->name('get');
    Route::put('edit', 'update')->name('update');
    Route::delete('delete', 'delete')->name('delete');
});


// exchanges manipulation
Route::prefix('exchanges')->controller(ExchangesController::class)->group(function () {
    Route::post('new', 'create')->name('create');
    Route::get('get', 'getExchanges')->name('getExchanges');
    Route::put('edit', 'update')->name('update');
    Route::delete('delete', 'delete')->name('delete');
    Route::get('downpdf', 'downpdf')->name('downpdf');
});
