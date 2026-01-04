<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PredictController;

Route::post('/predict', [PredictController::class, 'predict']);

Route::get('/', function () {
    return view('predict');
});
