<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewCommentController;
use App\Http\Controllers\ReviewVoteController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])->name('teachers.show');

/*
|--------------------------------------------------------------------------
| Invitation Acceptance Routes (public, token-based)
|--------------------------------------------------------------------------
*/

Route::get('/invitations/accept/{token}', [InvitationController::class, 'showAcceptForm'])->name('invitations.accept');
Route::post('/invitations/accept/{token}', [InvitationController::class, 'accept'])->name('invitations.accept.process');

/*
|--------------------------------------------------------------------------
| Authenticated Student Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Reviews
    Route::get('/teachers/{teacher}/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/teachers/{teacher}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Review comments (teacher responses)
    Route::post('/reviews/{review}/comments', [ReviewCommentController::class, 'store'])->name('reviews.comments.store');

    // Helpful votes
    Route::post('/reviews/{review}/vote', [ReviewVoteController::class, 'toggle'])->name('reviews.vote');

    // Reports
    Route::post('/reviews/{review}/report', [ReportController::class, 'store'])->name('reviews.report');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');

    // User profile
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Teacher/Admin Invitation Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'teacher_or_admin'])->prefix('manage')->name('invitations.')->group(function () {
    Route::get('/invitations', [InvitationController::class, 'index'])->name('index');
    Route::get('/invitations/create', [InvitationController::class, 'create'])->name('create');
    Route::post('/invitations', [InvitationController::class, 'store'])->name('store');
    Route::post('/invitations/{invitation}/cancel', [InvitationController::class, 'cancel'])->name('cancel');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Teacher management
    Route::resource('teachers', AdminTeacherController::class);

    // User management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}/toggle-suspend', [AdminUserController::class, 'toggleSuspend'])->name('users.toggle-suspend');

    // Review moderation
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Report management
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::put('/reports/{report}/resolve', [AdminReportController::class, 'resolve'])->name('reports.resolve');
    Route::put('/reports/{report}/dismiss', [AdminReportController::class, 'dismiss'])->name('reports.dismiss');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Laravel Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
