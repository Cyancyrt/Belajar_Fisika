<?php
// filepath: routes/web.php

use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SimulationController;
use Illuminate\Support\Facades\Route;

// Web routes dengan CSRF protection tetap aktif (jika diperlukan)
Route::get('/', [ModuleController::class, 'index']);
Route::get('/{questionId}', [SimulationController::class, 'index'])->name('simulation.index');
Route::post('/submit-answer', [SimulationController::class, 'submitAnswer'])->name('simulation.submitAnswer');
