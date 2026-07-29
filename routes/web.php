<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EventController as EventAdminController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\ReviewController;

use App\Http\Controllers\OrganizerRegistrationController;
use App\Http\Controllers\Organizer\DashboardController as OrganizerDashboardController;
use App\Http\Controllers\Admin\SuperadminController;

Route::get('/fix-db', function () {
    try {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE users ALTER COLUMN role TYPE VARCHAR(50)");
        return "SUCCESS: Database constraint users_role_check removed!";
    } catch (\Throwable $e) {
        return "ERROR: " . $e->getMessage();
    }
});

// Rute Pendaftaran, Login & Dashboard Multi-Tenant Kepanitiaan/HIMA
Route::get('/organizer/register', [OrganizerRegistrationController::class, 'showRegistrationForm'])->name('organizer.register');
Route::post('/organizer/register', [OrganizerRegistrationController::class, 'register'])->name('organizer.register.post');
Route::get('/organizer/login', [OrganizerRegistrationController::class, 'showLoginForm'])->name('organizer.login');
Route::post('/organizer/login', [OrganizerRegistrationController::class, 'login'])->name('organizer.login.post');
Route::get('/organizer/dashboard', [OrganizerDashboardController::class, 'index'])
    ->middleware(['auth', \App\Http\Middleware\OrganizerMiddleware::class])
    ->name('organizer.dashboard');

// Rute Rating & Review Pasca-Acara & Profil Penyelenggara
Route::post('/events/{event}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::get('/organizer/{organizer_name?}', [ReviewController::class, 'showOrganizer'])->name('organizer.show');



// --- Rute User Area ---
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/checkout', [EventController::class, 'checkout'])->name('checkout');
Route::get('/detail-event/{id?}', [EventController::class, 'show'])->name('detail-event');
Route::get('/ticket', [TicketController::class, 'ticket'])->name('ticket');

// Rute Tambahan Pertemuan 2
Route::get('/katalog', [EventController::class, 'index'])->name('katalog');
Route::get('/tentang', function () {
    return view('about'); })->name('about');
Route::post('/midtrans/callback', [\App\Http\Controllers\MidtransWebhookController::class, 'handle']);

// Rute Socialite (Google SSO) & User Auth
Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::post('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/')->with('success', 'Anda telah keluar.');
})->name('logout');


Route::get('/login', function () {
    return redirect()->route('organizer.login');
})->name('login');

Route::get('/checkout/{event}', [\App\Http\Controllers\CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/payment/{order_id}', [\App\Http\Controllers\CheckoutController::class, 'payment'])->name('checkout.payment');
Route::get('/payment/{order_id}/check', [\App\Http\Controllers\CheckoutController::class, 'checkStatusApi'])->name('checkout.check');
Route::get('/success/{order_id}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');

// --- Rute Admin Area & Superadmin ---
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.post');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('events', EventAdminController::class);
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        
        // Pengawasan Kelayakan Organisasi oleh Superadmin
        Route::get('organizers', [SuperadminController::class, 'index'])->name('organizers.index');
        Route::post('organizers/{organizer}/status', [SuperadminController::class, 'updateStatus'])->name('organizers.update-status');
    });
});
