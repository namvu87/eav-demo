<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EavController;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['web', 'eav.access'])->group(function () {
    Route::get('/eav', [EavController::class, 'index'])->name('eav.index');
});
