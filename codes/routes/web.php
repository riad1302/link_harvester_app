<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UrlController::class, 'index'])->name('urls.index');
Route::get('/url', [UrlController::class, 'create'])->name('urls.create');
Route::post('/url', [UrlController::class, 'store'])->name('urls.store');

//Route::get('/', function () {
//    return view('welcome');
//});
