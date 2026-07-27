<?php

use Illuminate\Support\Facades\Route;

// Serve SPA for all routes - Vue.js handles routing on frontend
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::get('/register', fn () => view('auth.register'))->name('register');

Route::get('/admin/{any?}', fn () => view('admin.app'))
    ->where('any', '.*')
    ->name('admin.app');

Route::get('/student/{any?}', fn () => view('student.app'))
    ->where('any', '.*')
    ->name('student.app');

Route::get('/teacher/{any?}', fn () => view('teacher.app'))
    ->where('any', '.*')
    ->name('teacher.app');

Route::get('/', fn () => redirect()->route('login'));
