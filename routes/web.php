<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\CountyController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// County routes
Route::resource('counties', CountyController::class);

// City routes
Route::resource('cities', CityController::class);

// City filtering and export routes
Route::post('cities/filter', [CityController::class, 'filter'])->name('cities.filter');
Route::get('cities/export/csv', [CityController::class, 'exportCsv'])->name('cities.export.csv');
Route::get('cities/export/pdf', [CityController::class, 'exportPdf'])->name('cities.export.pdf');