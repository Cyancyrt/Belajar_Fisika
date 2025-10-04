<?php
// filepath: routes/web.php

use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SimulationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ModuleController::class, 'index']);
Route::get('/{questionId}', [SimulationController::class, 'index'])->name('simulation.index');
Route::post('/submit-answer', [SimulationController::class, 'submitAnswer'])->name('simulation.submitAnswer');

Route::get('/', function () {
    return view('Dashboard.index');
});

Route::get('/simulation', function () {
    return view('simulation');
});

Route::get('/jungkat-jungkit', function () {
    return view('Dashboard.jungkat_jungkit');
});

Route::get('/friction', function () {
    return view('Dashboard.module.friction.create_simulation');
});

