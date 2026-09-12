<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('welcome');
});

Route::get('/latihan-soal', function () {
    return view('latihan-soal');
});

Route::get('/papan-skor', function () {
    return view('papan-skor');
});

Route::get('/asisten-ai', function () {
    return view('asisten-ai');
});

Route::get('/profil', function () {
    return view('profil');
});
