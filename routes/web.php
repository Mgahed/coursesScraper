<?php

use App\Http\Controllers\ScrapController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test/{url}', [ScrapController::class, 'getImg'])->name('test');

Route::get('/scrape-udemy', [ScrapController::class, 'udemy'])->name('scrape.udemy');
Route::get('/scrape-eduonix', [ScrapController::class, 'eduonix'])->name('scrape.eduonix');
Route::get('/scrape-alison', [ScrapController::class, 'alison'])->name('scrape.alison');

\PWA::routes();

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
