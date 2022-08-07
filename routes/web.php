<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

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

Route::name('website.')->group(function () {
	Route::get('/', function() {
		return redirect()->route('website.transactionQuery');
	});

	Route::any('/transaction-query', [PageController::class, 'transactionQuery'])->name('transactionQuery');
	Route::any('/get-transaction', [PageController::class, 'getTransaction'])->name('getTransaction');
	Route::any('/get-client', [PageController::class, 'getClient'])->name('getClient');
});
