<?php

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
     Route::get('/dashboard', function () {return view('adminPages.dashboard');})->name('adminPages.dashboard');

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

});

// Support the UI call fetch('/AdminPages/admin/{id}')




