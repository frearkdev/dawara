<?php

use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\BarberController as AdminBarberController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// ── Publieke pagina's ─────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// ── Booking ───────────────────────────────────────────────────────────────────
Route::prefix('boeken')->name('booking.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::post('/', [BookingController::class, 'store'])->name('store');
    Route::get('/bevestiging/{appointment}', [BookingController::class, 'confirmation'])->name('confirmation');
    Route::get('/beschikbaarheid', [BookingController::class, 'availability'])->name('availability');
});

// ── Payment (Mollie) ────────────────────────────────────────────────────────
Route::get('/payment/{appointment}', [PaymentController::class, 'create'])->name('payment.create');
Route::get('/payment/success/{appointment}', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

// ── Customer Booking Actions ──────────────────────────────────────────────
Route::post('/afspraken/{appointment}/annuleren', [BookingController::class, 'cancel'])->name('booking.cancel');
Route::get('/afspraken/{appointment}/herboeken', [BookingController::class, 'rescheduleForm'])->name('booking.reschedule.form');
Route::post('/afspraken/{appointment}/herboeken', [BookingController::class, 'reschedule'])->name('booking.reschedule');

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/afspraken', [AdminAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/afspraken/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('/afspraken/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::post('/afspraken', [AdminAppointmentController::class, 'store'])->name('appointments.store');

    Route::get('/diensten', [AdminServiceController::class, 'index'])->name('services.index');
    Route::post('/diensten', [AdminServiceController::class, 'store'])->name('services.store');
    Route::patch('/diensten/{service}', [AdminServiceController::class, 'update'])->name('services.update');
    Route::delete('/diensten/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');

    Route::get('/barbers', [AdminBarberController::class, 'index'])->name('barbers.index');
    Route::patch('/barbers/{barber}', [AdminBarberController::class, 'update'])->name('barbers.update');
    Route::patch('/barbers/{barber}/diensten', [AdminBarberController::class, 'updateServices'])->name('barbers.services');
    Route::patch('/barbers/{barber}/beschikbaarheid', [AdminBarberController::class, 'updateAvailability'])->name('barbers.availability');

    Route::get('/klanten', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/klanten/{user}', [CustomerController::class, 'show'])->name('customers.show');

    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews', [AdminReviewController::class, 'store'])->name('reviews.store');
    Route::patch('/reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');
    Route::post('/reviews/import-google', [AdminReviewController::class, 'importGoogle'])->name('reviews.import-google');
    Route::post('/reviews/import-pasted', [AdminReviewController::class, 'importPasted'])->name('reviews.import-pasted');

    Route::get('/instellingen', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/instellingen', [SettingController::class, 'store'])->name('settings.store');
});
