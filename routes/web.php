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
use App\Http\Controllers\Admin\CleanupController;

Route::get('/', function () {return view('auth.login');});

//views
Route::get('/Authentication/login', [AuthController::class, 'loginInterface'])->name('login');

//Login Processes
Route::post('/Authentication/login', [AuthController::class, 'login'])
    ->middleware('rate.limit.login')
    ->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('adminPages')->middleware('auth')->group(function () {

    //Dashboard
    Route::get('/dashboard', [StayController::class, 'reports'])->name('adminPages.dashboard');

    // Reports pages
    Route::get('/reports/payments', [App\Http\Controllers\ReportController::class, 'payments'])->name('reports.payments');
    Route::get('/reports/guests', [App\Http\Controllers\ReportController::class, 'guests'])->name('reports.guests');
    Route::get('/reports/transactions', [App\Http\Controllers\ReportController::class, 'transactionReports'])->name('reports.transactions');
    Route::get('/reports/all-transactions', [App\Http\Controllers\ReportController::class, 'allTransactions'])->name('reports.all-transactions');
    Route::get('/reports/all-archived-transactions', [App\Http\Controllers\ReportController::class, 'allArchivedTransactions'])->name('reports.all-archived-transactions');
    Route::get('/reports/logs', [App\Http\Controllers\ReportController::class, 'logs'])->name('reports.logs');
    Route::get('/reports/data', [App\Http\Controllers\ReportController::class, 'data'])->name('reports.data');

    //Settings
    Route::get('/settings', function () {return view('adminPages.adminsettings');})->name('adminPages.settings');

    Route::put('/settings/personal', [UserController::class, 'updatePersonal'])->name('adminPages.settings.personal');
    Route::put('/settings/email', [UserController::class, 'updateEmail'])->name('adminPages.settings.email');
    Route::put('/settings/password', [UserController::class, 'updatePassword'])->name('adminPages.settings.password');
    Route::delete('/settings/deactivate', [UserController::class, 'deactivateAccount'])->name('adminPages.settings.deactivate');
    
    // Permission Requests
    Route::get('/permission-requests', [App\Http\Controllers\PermissionRequestController::class, 'index'])->name('adminPages.permission-requests');
    Route::post('/permission-requests/{id}/approve', [App\Http\Controllers\PermissionRequestController::class, 'approve'])->name('adminPages.permission-requests.approve');
    Route::post('/permission-requests/{id}/reject', [App\Http\Controllers\PermissionRequestController::class, 'reject'])->name('adminPages.permission-requests.reject');

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

    //Cleaner Crud
    Route::get('/cleanerrecords', [UserController::class, 'cleaner'])->name('adminPages.cleanerrecords');
    Route::post('/cleanerrecords', [UserController::class, 'store'])->name('adminPages.cleanerrecords.post');
    Route::post('/cleanerrecords/update/{id}', [UserController::class, 'update'])->name('cleanerrecords.update');
    Route::delete('/cleanerrecords/delete/{id}', [UserController::class, 'destroy'])->name('cleanerrecords.destroy');
    Route::patch('/cleanerrecords/restore/{id}', [UserController::class, 'restore'])->name('cleanerrecords.restore');
    Route::get('/cleanerrecords/archive', [App\Http\Controllers\UserController::class, 'archivedCleaners'])->name('cleanerrecords.archive');
    
    // Cleaner API for transaction assignment
    Route::get('/cleaners/list', [UserController::class, 'getCleanersList'])->name('adminPages.cleaners.list');

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
    Route::post('/rooms/mark-ready/{roomId}', [StayController::class, 'markRoomReady'])->name('rooms.mark-ready');
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
    
    // Guest Cleanup Routes
    Route::get('/cleanup', [CleanupController::class, 'index'])->name('reports.cleanup');
    Route::post('/cleanup/run', [CleanupController::class, 'runCleanup'])->name('reports.cleanup.run');
    Route::post('/cleanup/soft-delete/{id}', [CleanupController::class, 'forceSoftDelete'])->name('reports.cleanup.soft-delete');
    Route::post('/cleanup/hard-delete/{id}', [CleanupController::class, 'forceHardDelete'])->name('reports.cleanup.hard-delete');
    
    // Guest details route
    Route::get('/transactions/guest-details/{id}', [StayController::class, 'getGuestDetails'])->name('transactions.guest-details');

});

// FrontDesk Routes
Route::prefix('frontdesk')->middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\FrontDesk\StayController::class, 'reports'])->name('frontdesk.dashboard');
    
    // Transaction Management Routes
    Route::get('/transactions', [App\Http\Controllers\FrontDesk\StayController::class, 'index'])->name('frontdesk.transactions');
    Route::post('/transactions', [App\Http\Controllers\FrontDesk\StayController::class, 'store'])->name('frontdesk.transactions.post');
    Route::post('/transactions/update/{id}', [App\Http\Controllers\FrontDesk\StayController::class, 'update'])->name('frontdesk.transactions.update');
    Route::post('/transactions/archive/{id}', [App\Http\Controllers\FrontDesk\StayController::class, 'archive'])->name('frontdesk.transactions.archive');
    Route::patch('/transactions/restore/{id}', [App\Http\Controllers\FrontDesk\StayController::class, 'restore'])->name('frontdesk.transactions.restore');
    Route::get('/archivetransactions', [App\Http\Controllers\FrontDesk\StayController::class, 'archived'])->name('frontdesk.archivetransactions');
    Route::get('/transactionreports', [App\Http\Controllers\FrontDesk\ReportController::class, 'transactionReports'])->name('frontdesk.transactionreports');
    
    // Settings
    Route::get('/settings', function () {return view('frontdeskPages.settings');})->name('frontdesk.settings');
    
    // Settings routes for frontdesk
    Route::put('/settings/personal', [App\Http\Controllers\UserController::class, 'updatePersonal'])->name('frontdesk.settings.personal');
    Route::put('/settings/email', [App\Http\Controllers\UserController::class, 'updateEmail'])->name('frontdesk.settings.email');
    Route::put('/settings/password', [App\Http\Controllers\UserController::class, 'updatePassword'])->name('frontdesk.settings.password');
    Route::delete('/settings/deactivate', [App\Http\Controllers\UserController::class, 'deactivateAccount'])->name('frontdesk.settings.deactivate');
    
    // Activity Logs
    Route::get('/auditlogs', [App\Http\Controllers\FrontDesk\ReportController::class, 'auditLogs'])->name('frontdesk.auditlogs');
    
    // Guest details route
    Route::get('/transactions/guest-details/{id}', [App\Http\Controllers\FrontDesk\StayController::class, 'getGuestDetails'])->name('frontdesk.transactions.guest-details');
    
    // Room Dashboard Routes (for frontdesk)
    Route::get('/stays/rates/{accommodationId}', [App\Http\Controllers\FrontDesk\StayController::class, 'getRatesForAccommodation'])->name('frontdesk.stays.rates');
    Route::post('/stays/calculate', [App\Http\Controllers\FrontDesk\StayController::class, 'calculateTotal'])->name('frontdesk.stays.calculate');
    Route::post('/stays/process', [App\Http\Controllers\FrontDesk\StayController::class, 'processStay'])->name('frontdesk.stays.process');
    Route::post('/stays/end/{id}', [App\Http\Controllers\FrontDesk\StayController::class, 'endStay'])->name('frontdesk.stays.end');
    Route::post('/stays/extend/{id}', [App\Http\Controllers\FrontDesk\StayController::class, 'extend'])->name('frontdesk.stays.extend');
    Route::post('/rooms/mark-ready/{roomId}', [App\Http\Controllers\FrontDesk\StayController::class, 'markRoomReady'])->name('frontdesk.rooms.mark-ready');
    Route::post('/stays/delete/{id}', [App\Http\Controllers\FrontDesk\StayController::class, 'delete'])->name('frontdesk.stays.delete');
    Route::post('/stays/restore/{id}', [App\Http\Controllers\FrontDesk\StayController::class, 'restore'])->name('frontdesk.stays.restore');
    Route::get('/stays/active', [App\Http\Controllers\FrontDesk\StayController::class, 'getActiveStays'])->name('frontdesk.stays.active');
});





