<?php

use App\Http\Controllers\PartnersController;
use Illuminate\Http\Request;
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

// partners manipulation
Route::prefix('partners')->controller(PartnersController::class)->group(function () {
    Route::post('new', 'create')->name('create');
    Route::get('get{id?}', 'getAll')->name('getAll');
    Route::put('edit', 'update')->name('update');
    Route::delete('delete', 'delete')->name('delete');
});
