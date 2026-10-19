<?php

use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/viec-lam/{slug}', [JobController::class, 'show'])->name('jobs.show');
Route::post('/viec-lam/{slug}/ung-tuyen', [ApplicationController::class, 'store'])
    ->middleware('throttle:5,1')->name('applications.store');

Route::get('/lien-he', [ContactController::class, 'create'])->name('contact.create');
Route::post('/lien-he', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')->name('contact.store');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'store'])->name('admin.login.store');
});

Route::post('/admin/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')->name('admin.logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,editor'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('jobs', AdminJobController::class)->except('show');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', AdminUserController::class)->except(['show', 'destroy']);
    Route::get('applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::patch('applications/{application}', [AdminApplicationController::class, 'update'])->name('applications.update');
    Route::get('applications/{application}/download', [AdminApplicationController::class, 'download'])->name('applications.download');
});
