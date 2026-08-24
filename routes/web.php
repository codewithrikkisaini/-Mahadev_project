<?php

use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MemberController as AdminMemberController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Member\AnnouncementController as MemberAnnouncementController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\NotificationController as MemberNotificationController;
use App\Http\Controllers\Member\PaymentController as MemberPaymentController;
use App\Http\Controllers\Member\ProfileController as MemberProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('member.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated common notification routes
Route::middleware('auth')->group(function () {
    Route::get('/notifications/{notification}/read', [MemberNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [MemberNotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
});

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/send-reminders', [AdminDashboardController::class, 'sendReminders'])->name('dashboard.sendReminders');

    // Member Management
    Route::resource('members', AdminMemberController::class);
    Route::post('/members/{member}/toggle-status', [AdminMemberController::class, 'toggleStatus'])->name('members.toggleStatus');
    Route::post('/members/{member}/manual-payment', [AdminMemberController::class, 'recordManualPayment'])->name('members.manualPayment');

    // Payment Management & Verification
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/pending', [AdminPaymentController::class, 'pending'])->name('payments.pending');
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/approve', [AdminPaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');
    Route::get('/payments/{payment}/receipt', [AdminPaymentController::class, 'receipt'])->name('payments.receipt');

    // Reports & Analytics
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/member', [AdminReportController::class, 'memberReport'])->name('reports.member');
    Route::get('/reports/export/monthly-csv', [AdminReportController::class, 'exportMonthlyCsv'])->name('reports.exportMonthlyCsv');
    Route::get('/reports/export/member-matrix-csv', [AdminReportController::class, 'exportMemberMatrixCsv'])->name('reports.exportMemberMatrixCsv');

    // Announcements
    Route::resource('announcements', AdminAnnouncementController::class);

    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| Member Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'member'])->prefix('member')->as('member.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');

    // Payments & QR Pay
    Route::get('/payments', [MemberPaymentController::class, 'index'])->name('payments.index');
    Route::get('/pay', [MemberPaymentController::class, 'create'])->name('payments.create');
    Route::post('/pay', [MemberPaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/receipt', [MemberPaymentController::class, 'receipt'])->name('payments.receipt');

    // Profile
    Route::get('/profile', [MemberProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [MemberProfileController::class, 'update'])->name('profile.update');

    // Announcements & Notifications
    Route::get('/announcements', [MemberAnnouncementController::class, 'index'])->name('announcements');
    Route::get('/notifications', [MemberNotificationController::class, 'index'])->name('notifications');
});
