<?php
// filepath: routes/web.php

use Illuminate\Support\Facades\Route;

// Web routes dengan CSRF protection tetap aktif (jika diperlukan)
Route::get('/', function () {
    return view('Dashboard.index');
});