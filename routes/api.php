<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ExchangeRateController;

Route::get('/exchange-rates', [ExchangeRateController::class, 'index'])->name('api.exchange-rates');
Route::post('/exchange-rates/refresh', [ExchangeRateController::class, 'refresh'])->name('api.exchange-rates.refresh');
