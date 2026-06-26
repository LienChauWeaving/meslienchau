<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAuthController; // Thêm dòng này
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;

// ================= CỔNG USER (NHÂN VIÊN) =================
Route::get('/', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [LeaveRequestController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveRequestController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/{id}/workflow', [LeaveRequestController::class, 'workflow'])->name('leaves.workflow');
    Route::post('/leaves/{id}/confirm', [LeaveRequestController::class, 'confirmLeave'])->name('leaves.confirm');
    Route::get('/leaves-all', [LeaveRequestController::class, 'allLeaves'])->name('leaves.all');
    
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/{id}/process', [ApprovalController::class, 'process'])->name('approvals.process');
    Route::post('/leaves/{id}/resubmit', [LeaveRequestController::class, 'resubmit'])->name('leaves.resubmit');
    Route::post('/leaves/{id}/cancel', [LeaveRequestController::class, 'requestCancel'])->name('leaves.cancel');

    Route::get('/profile/delegate', [ProfileController::class, 'delegate'])->name('profile.delegate');
    Route::post('/profile/delegate', [ProfileController::class, 'updateDelegate'])->name('profile.delegate.update');
    
    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// ================= CỔNG ADMIN (QUẢN TRỊ VIÊN) =================
// ================= CỔNG ADMIN (QUẢN TRỊ VIÊN) =================
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Khách (Guest) truy cập trang Admin
    Route::middleware('guest:admin')->group(function () {
        Route::get('/', [AdminAuthController::class, 'showLogin']); 
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    // Admin đã đăng nhập
    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/password', [AdminController::class, 'password'])->name('password');
        Route::post('/password', [AdminController::class, 'updatePassword'])->name('password.update');

        Route::get('/users/template', [AdminController::class, 'downloadTemplate'])->name('users.template');
        Route::post('/users/import', [AdminController::class, 'importUsers'])->name('users.import');

        Route::get('/permissions', [AdminController::class, 'indexPermissions'])->name('permissions.index');
        Route::post('/permissions', [AdminController::class, 'storePermissions'])->name('permissions.store');
        Route::delete('/permissions/{id}', [AdminController::class, 'destroyPermissions'])->name('permissions.destroy');

        // Các route quản trị Users & Approvers giữ nguyên...
        Route::get('/users', [AdminController::class, 'indexUsers'])->name('users.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users/store', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::post('/users/{id}/update', [AdminController::class, 'updateUser'])->name('users.update');

        Route::get('/approvers', [AdminController::class, 'indexApprovers'])->name('approvers.index');
        Route::post('/approvers/store', [AdminController::class, 'storeApprover'])->name('approvers.store');
        Route::get('/approvers/{id}/edit', [AdminController::class, 'editApprover'])->name('approvers.edit');
        Route::post('/approvers/{id}/update', [AdminController::class, 'updateApprover'])->name('approvers.update');
        Route::delete('/approvers/{id}/delete', [AdminController::class, 'destroyApprover'])->name('approvers.delete');
    });
});