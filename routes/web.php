<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\RateController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LevelController;

Route::get('/', function () {
    return view('auth.login');
});

//views
Route::get('/Authentication/login', [AuthController::class, 'loginInterface'])->name('login');

//Login Processes
Route::post('/Authentication/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');




Route::prefix('adminPages')->middleware('auth')->group(function () {

    //Dashboard
    Route::get('/dashboard', function () {
        return view('adminPages.dashboard');
    })->name('adminPages.dashboard');

    //Settings
    Route::get('/settings', function () {
        return view('adminPages.adminsettings');
    })->name('adminPages.settings');

    Route::put('/settings/personal', [UserController::class, 'updatePersonal'])->name('adminPages.settings.personal');
    Route::put('/settings/email', [UserController::class, 'updateEmail'])->name('adminPages.settings.email');
    Route::put('/settings/password', [UserController::class, 'updatePassword'])->name('adminPages.settings.password');
    Route::delete('/settings/deactivate', [UserController::class, 'deactivateAccount'])->name('adminPages.settings.deactivate');

    //Admin Crud
    Route::get('/adminrecords', [UserController::class, 'admin'])->name('adminPages.adminrecords');
    Route::post('/adminrecords', [UserController::class, 'store'])->name('adminPages.adminrecords.post');
    Route::post('/adminrecords/update/{id}', [UserController::class, 'update'])->name('adminrecords.update');
    Route::delete('/adminrecords/delete/{id}', [UserController::class, 'destroy'])->name('adminrecords.destroy');
    Route::patch('/adminrecords/restore/{id}', [UserController::class, 'restore'])->name('adminrecords.restore');
    Route::get('/adminrecords/archive', [App\Http\Controllers\UserController::class, 'archivedAdmins'])->name('adminrecords.archive');

    //Operator Crud
    Route::get('/frontdeskrecords', [UserController::class, 'frontdesk'])->name('adminPages.frontdeskrecords');
    Route::post('/frontdeskrecords', [UserController::class, 'store'])->name('adminPages.frontdeskrecords.post');
    Route::post('/frontdeskrecords/update/{id}', [UserController::class, 'update'])->name('frontdeskrecords.update');
    Route::delete('/frontdeskrecords/delete/{id}', [UserController::class, 'destroy'])->name('frontdeskrecords.destroy');
    Route::patch('/frontdeskrecords/restore/{id}', [UserController::class, 'restore'])->name('adminrecords.restore');
    Route::get('/frontdeskrecords/archive', [App\Http\Controllers\UserController::class, 'archivedFrontdesk'])->name('frontdeskrecords.archive');

    // Level Crud
    Route::get('/levels', [LevelController::class, 'index'])->name('adminPages.levels');
    Route::post('/levels', [LevelController::class, 'store'])->name('adminPages.levels.post');
    Route::post('/levels/update/{id}', [LevelController::class, 'update'])->name('levels.update');
    Route::delete('/levels/delete/{id}', [LevelController::class, 'destroy'])->name('levels.destroy');
    Route::patch('/levels/restore/{id}', [LevelController::class, 'restore'])->name('levels.restore');
    Route::get('/levels/archive', [LevelController::class, 'archived'])->name('levels.archive');

    // Accommodation CRUD
    Route::get('/accommodations', [AccommodationController::class, 'index'])->name('adminPages.accommodations');
    Route::post('/accommodations', [AccommodationController::class, 'store'])->name('adminPages.accommodations.post');
    Route::post('/accommodations/update/{id}', [AccommodationController::class, 'update'])->name('accommodations.update');
    Route::delete('/accommodations/delete/{id}', [AccommodationController::class, 'destroy'])->name('accommodations.destroy');
    Route::patch('/accommodations/restore/{id}', [AccommodationController::class, 'restore'])->name('accommodations.restore');
    Route::get('/accommodations/archive', [AccommodationController::class, 'archived'])->name('accommodations.archive');

    // Rate Crud
    Route::get('/rates', [RateController::class, 'index'])->name('adminPages.rates');
    Route::post('/rates', [RateController::class, 'store'])->name('adminPages.rates.post');
    Route::post('/rates/update/{id}', [RateController::class, 'update'])->name('rates.update');
    Route::delete('/rates/delete/{id}', [RateController::class, 'destroy'])->name('rates.destroy');
    Route::patch('/rates/restore/{id}', [RateController::class, 'restore'])->name('rates.restore');
    Route::get('/rates/archive', [RateController::class, 'archived'])->name('rates.archive');




});

// Support the UI call fetch('/AdminPages/admin/{id}')




