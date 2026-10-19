<?php

use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/gioi-thieu', 'pages.about')->name('about');
Route::get('/tin-tuc', [NewsController::class, 'index'])->name('news');
Route::get('/tin-tuc/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/ngon-ngu/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/viec-lam/{slug}', [JobController::class, 'show'])->name('jobs.show');
Route::post('/viec-lam/{slug}/ung-tuyen', [ApplicationController::class, 'store'])
    ->middleware('throttle:5,1')->name('applications.store');

Route::get('/lien-he', [ContactController::class, 'create'])->name('contact.create');
Route::post('/lien-he', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')->name('contact.store');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'store'])->middleware('throttle:admin-login')->name('admin.login.store');
});

Route::post('/admin/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')->name('admin.logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,editor'])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::resource('jobs', AdminJobController::class)->except('show')->middleware('permission:jobs');
    Route::resource('news', AdminNewsController::class)->except('show')->middleware('permission:news');
    Route::get('noi-dung', [ContentController::class, 'index'])->middleware('permission:content')->name('content.index');
    Route::get('noi-dung/{section}', [ContentController::class, 'edit'])->middleware('permission:content')->name('content.edit');
    Route::put('noi-dung/{section}', [ContentController::class, 'update'])->middleware('permission:content')->name('content.update');
    Route::middleware('permission:applications')->group(function () {
        Route::get('applications', [AdminApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
        Route::patch('applications/{application}', [AdminApplicationController::class, 'update'])->name('applications.update');
        Route::get('applications/{application}/download', [AdminApplicationController::class, 'download'])->name('applications.download');
    });
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SiteSettingController::class, 'update'])->name('settings.update');
    Route::resource('users', AdminUserController::class)->except(['show', 'destroy']);
});
