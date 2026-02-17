<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('file-manager');
});

Route::get('/welcome', function () {
    return view('welcome');
});
