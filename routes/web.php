<?php

use Illuminate\Support\Facades\Route;

Route::get('/', action: function () {
    return view('Dashboard.index');
});
Route::get('/jungkatjungkit', action: function () {
    return view('Dashboard.jungkat_jungkit');
});
Route::get('/create_friction', function(){
    return view('Dashboard.module.friction.create_simulation');
});

