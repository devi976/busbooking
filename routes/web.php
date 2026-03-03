<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BusController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (NO LOGIN REQUIRED)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/features', function () {
    return view('features');
});

Route::get('/contact', function () {
    return view('contact');
});

Route::get('/payment/{bus}', [BookingController::class,'payment']);
Route::get('/booking-success/{booking}', 
    [BookingController::class,'bookingSuccess']);
Route::post('/payment-success/{bus}', [BookingController::class, 'paymentSuccess']);

Route::get('/download-ticket/{booking}',
    [BookingController::class,'downloadTicket'])->name('ticket.download');

// 🔥 Dismiss Announcement (Public)
Route::post('/announcement/{id}/dismiss', [BusController::class, 'dismissAnnouncement']);
/*
|--------------------------------------------------------------------------
| BUS SEARCH (GUEST ACCESS)
|--------------------------------------------------------------------------
*/

Route::get('/search', [BusController::class, 'searchForm']);
Route::post('/search', [BusController::class, 'search']);
Route::get('/search-ajax', [BusController::class, 'searchAjax']);

// 📍 Live Tracking Routes
Route::get('/bus/{bus}/live-tracking', [BusController::class, 'liveTracking']);
Route::get('/bus/{bus}/get-location', [BusController::class, 'getLocation']);
Route::post('/bus/{bus}/update-location', [BusController::class, 'updateLocation'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');




/*
|--------------------------------------------------------------------------
| OPERATOR ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'operator'])->group(function () {
    Route::get('/operator/dashboard', function () {
        return view('operator.dashboard');
    });

    Route::get('/operator/buses', [BusController::class, 'operatorBuses']);
    Route::get('/operator/bus/create', [BusController::class, 'create']);
    Route::post('/operator/bus/store', [BusController::class, 'store']);
    Route::get('/operator/bus/{bus}/edit', [BusController::class, 'edit']);
    Route::post('/operator/bus/{bus}/update', [BusController::class, 'update']);
    
    // Booking Management
    Route::get('/operator/bus/{bus}/bookings', [BusController::class, 'operatorBusBookings']);
    Route::get('/operator/bus/{bus}/bookings/{date}', [BusController::class, 'operatorBusBookingsByDate']);
});

Route::middleware(['auth','operator'])->group(function () {
    Route::get('/operator/change-password', [AuthController::class, 'changePasswordForm']);
    Route::post('/operator/change-password', [AuthController::class, 'changePassword']);
});

/*
/*
|--------------------------------------------------------------------------
| TICKET BOOKING ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Show seat layout
    Route::get('/select-seat/{bus}/{date}', [BookingController::class, 'selectSeats']);

    // Handle seat selection submit
    Route::post('/confirm-seats/{bus}/{date}', [BookingController::class, 'confirmSeats']);

    // Payment & Success
    Route::post('/payment-success/{bus}/{date}', [BookingController::class, 'paymentSuccess']);
    
    Route::get('/booking-success/{booking}', [BookingController::class, 'bookingSuccess']);
    Route::get('/download-ticket/{booking}', [BookingController::class, 'downloadTicket'])->name('ticket.download');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
});

Route::middleware(['auth','admin'])->group(function () {

    Route::get('/admin/dashboard', 
        [AdminController::class, 'dashboard']);
    Route::get('/admin/operators', [AdminController::class, 'operators']);
    Route::get('/admin/operator/create', [AdminController::class, 'createOperator']);
    Route::post('/admin/operator/store', [AdminController::class, 'storeOperator']);
    Route::get('/admin/announcement',
    [AdminController::class, 'announcementForm']);
    Route::post('/admin/announcement',
    [AdminController::class, 'storeAnnouncement']);
    Route::get('/admin/buses', [AdminController::class, 'allBuses']);
    Route::post('/admin/bus/{bus}/toggle', [AdminController::class, 'toggleBus']);
    Route::delete('/admin/bus/{bus}', [AdminController::class, 'deleteBus']);
    Route::delete('/admin/operator/{user}', 
        [AdminController::class, 'deleteOperator']);

    // 🔥 Enhanced Booking Details
    Route::get('/admin/bus/{bus}/bookings', [AdminController::class, 'busBookings']);
    Route::get('/admin/bus/{bus}/bookings/{date}', [AdminController::class, 'busBookingsByDate']);
});

