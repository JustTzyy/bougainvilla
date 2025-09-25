<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StayController;
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
    Route::get('/dashboard', [StayController::class, 'reports'])->name('adminPages.dashboard');

    // Reports pages
    Route::get('/reports/payments', [App\Http\Controllers\ReportController::class, 'payments'])->name('reports.payments');
    Route::get('/reports/guests', [App\Http\Controllers\ReportController::class, 'guests'])->name('reports.guests');
    Route::get('/reports/transactions', [App\Http\Controllers\ReportController::class, 'transactionReports'])->name('reports.transactions');
    Route::get('/reports/all-transactions', [App\Http\Controllers\ReportController::class, 'allTransactions'])->name('reports.all-transactions');
    Route::get('/reports/logs', [App\Http\Controllers\ReportController::class, 'logs'])->name('reports.logs');
    Route::get('/reports/data', [App\Http\Controllers\ReportController::class, 'data'])->name('reports.data');

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
    Route::get('/levels/{id}/rooms', [LevelController::class, 'getRooms'])->name('levels.rooms');

    // Accommodation CRUD
    Route::get('/accommodations', [AccommodationController::class, 'index'])->name('adminPages.accommodations');
    Route::post('/accommodations', [AccommodationController::class, 'store'])->name('adminPages.accommodations.post');
    Route::post('/accommodations/update/{id}', [AccommodationController::class, 'update'])->name('accommodations.update');
    Route::delete('/accommodations/delete/{id}', [AccommodationController::class, 'destroy'])->name('accommodations.destroy');
    Route::patch('/accommodations/restore/{id}', [AccommodationController::class, 'restore'])->name('accommodations.restore');
    Route::get('/accommodations/archive', [AccommodationController::class, 'archived'])->name('accommodations.archive');
    Route::get('/accommodations/{id}/rooms', [AccommodationController::class, 'getRooms'])->name('accommodations.rooms');
    Route::get('/accommodations/{id}/rates', [AccommodationController::class, 'getRates'])->name('accommodations.rates');

    // Rate Crud
    Route::get('/rates', [RateController::class, 'index'])->name('adminPages.rates');
    Route::post('/rates', [RateController::class, 'store'])->name('adminPages.rates.post');
    Route::post('/rates/update/{id}', [RateController::class, 'update'])->name('rates.update');
    Route::delete('/rates/delete/{id}', [RateController::class, 'destroy'])->name('rates.destroy');
    Route::patch('/rates/restore/{id}', [RateController::class, 'restore'])->name('rates.restore');
    Route::get('/rates/archive', [RateController::class, 'archived'])->name('rates.archive');
    Route::get('/rates/{id}/accommodations', [RateController::class, 'getAccommodationsByRate'])->name('rates.accommodations');

    // Room Crud
    Route::get('/rooms', [RoomController::class, 'index'])->name('adminPages.rooms');
    Route::post('/rooms', [RoomController::class, 'store'])->name('adminPages.rooms.post');
    Route::post('/rooms/update/{id}', [RoomController::class, 'update'])->name('rooms.update');
    Route::post('/rooms/archive/{id}', [RoomController::class, 'archive'])->name('rooms.archive');
    Route::delete('/rooms/delete/{id}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    Route::patch('/rooms/restore/{id}', [RoomController::class, 'restore'])->name('rooms.restore');
    Route::get('/rooms/archive', [RoomController::class, 'archived'])->name('rooms.archive');
    Route::get('/rooms/details/{id}', [RoomController::class, 'getRoomDetails'])->name('rooms.details');
    Route::get('/rooms/{id}/accommodations', [RoomController::class, 'getAccommodations'])->name('rooms.accommodations');

    // Room Dashboard Routes (integrated into transactions)
    Route::get('/stays/rates/{accommodationId}', [StayController::class, 'getRatesForAccommodation'])->name('stays.rates');
    Route::post('/stays/calculate', [StayController::class, 'calculateTotal'])->name('stays.calculate');
    Route::post('/stays/process', [StayController::class, 'processStay'])->name('stays.process');
    Route::post('/stays/end/{id}', [StayController::class, 'endStay'])->name('stays.end');
    Route::post('/stays/extend/{id}', [StayController::class, 'extend'])->name('stays.extend');
    Route::post('/stays/delete/{id}', [StayController::class, 'delete'])->name('stays.delete');
    Route::post('/stays/restore/{id}', [StayController::class, 'restore'])->name('stays.restore');
    Route::get('/stays/active', [StayController::class, 'getActiveStays'])->name('stays.active');

    // Transaction Management Routes (Stays)
    Route::get('/transactions', [StayController::class, 'index'])->name('adminPages.transactions');
    Route::post('/transactions', [StayController::class, 'store'])->name('adminPages.transactions.post');
    Route::post('/transactions/update/{id}', [StayController::class, 'update'])->name('transactions.update');
    Route::post('/transactions/archive/{id}', [StayController::class, 'archive'])->name('transactions.archive');
    Route::patch('/transactions/restore/{id}', [StayController::class, 'restore'])->name('transactions.restore');
    Route::get('/archivetransactions', [StayController::class, 'archived'])->name('adminPages.archivetransactions');
    Route::get('/transactionreports', [App\Http\Controllers\ReportController::class, 'transactionReports'])->name('adminPages.transactionreports');
    
    // Activity Logs
    Route::get('/auditlogs', [App\Http\Controllers\ReportController::class, 'auditLogs'])->name('adminPages.auditlogs');

});

// Support the UI call fetch('/AdminPages/admin/{id}')




