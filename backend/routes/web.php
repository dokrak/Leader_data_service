<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('file-manager');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/register', function () {
    return view('register');
});

Route::get('/welcome', function () {
    return view('welcome');
});
