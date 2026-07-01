<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isSuperAdmin()) return redirect()->route('super-admin.dashboard');
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isTeacher()) return redirect()->route('teacher.dashboard');
    }
    return redirect()->route('login');
});

// Theme persistence
Route::post('/persist-theme', function (Illuminate\Http\Request $request) {
    session(['theme' => $request->input('theme', 'dark-theme')]);
    return response()->json(['success' => true]);
});

// Authentication Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Auth-only Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Shared AJAX / API actions (e.g. marking notifications read, search)
    Route::post('/notifications/{id}/read', function ($id) {
        $notif = \App\Models\Notification::where('user_id', auth()->id())->findOrFail($id);
        $notif->update(['is_read' => true]);
        return response()->json(['success' => true]);
    })->name('notifications.read');

    // ----------------------------------------------------
    // SUPER ADMIN PORTAL
    // ----------------------------------------------------
    Route::middleware('role:super_admin')->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
        
        // Campus Management
        Route::get('/campuses', [SuperAdminController::class, 'campuses'])->name('campuses');
        Route::post('/campuses', [SuperAdminController::class, 'addCampus'])->name('campuses.add');
        Route::post('/campuses/{id}/edit', [SuperAdminController::class, 'editCampus'])->name('campuses.edit');
        Route::post('/campuses/{id}/delete', [SuperAdminController::class, 'deleteCampus'])->name('campuses.delete');
        Route::get('/campuses/{id}', [SuperAdminController::class, 'viewCampus'])->name('campuses.view');

        // Admin Management
        Route::get('/admins', [SuperAdminController::class, 'admins'])->name('admins');
        Route::post('/admins', [SuperAdminController::class, 'addAdmin'])->name('admins.add');
        Route::post('/admins/{id}/edit', [SuperAdminController::class, 'editAdmin'])->name('admins.edit');
        Route::post('/admins/{id}/delete', [SuperAdminController::class, 'deleteAdmin'])->name('admins.delete');

        // Teacher Management
        Route::get('/teachers', [SuperAdminController::class, 'teachers'])->name('teachers');
        Route::post('/teachers', [SuperAdminController::class, 'addTeacher'])->name('teachers.add');
        Route::post('/teachers/{id}/edit', [SuperAdminController::class, 'editTeacher'])->name('teachers.edit');
        Route::post('/teachers/{id}/delete', [SuperAdminController::class, 'deleteTeacher'])->name('teachers.delete');

        // Class Management
        Route::get('/classes', [SuperAdminController::class, 'classes'])->name('classes');
        Route::post('/classes', [SuperAdminController::class, 'addClass'])->name('classes.add');
        Route::post('/classes/{id}/edit', [SuperAdminController::class, 'editClass'])->name('classes.edit');
        Route::post('/classes/{id}/delete', [SuperAdminController::class, 'deleteClass'])->name('classes.delete');

        // Inspection Configuration (Criteria, Sub-criteria, Questions)
        Route::get('/inspection-configuration', [SuperAdminController::class, 'inspectionConfig'])->name('inspection-config');
        Route::post('/criteria', [SuperAdminController::class, 'addCriteria'])->name('criteria.add');
        Route::post('/criteria/{id}/edit', [SuperAdminController::class, 'editCriteria'])->name('criteria.edit');
        Route::post('/sub-criteria', [SuperAdminController::class, 'addSubCriteria'])->name('sub-criteria.add');
        Route::post('/sub-criteria/{id}/edit', [SuperAdminController::class, 'editSubCriteria'])->name('sub-criteria.edit');
        Route::post('/questions', [SuperAdminController::class, 'addQuestion'])->name('questions.add');
        Route::post('/questions/{id}/edit', [SuperAdminController::class, 'editQuestion'])->name('questions.edit');
        Route::post('/questions/reorder', [SuperAdminController::class, 'reorderQuestions'])->name('questions.reorder');
        Route::post('/questions/{id}/delete', [SuperAdminController::class, 'deleteQuestion'])->name('questions.delete');

        // Admin Inspections
        Route::get('/admin-inspection', [SuperAdminController::class, 'adminInspectionForm'])->name('admin-inspection');
        Route::post('/admin-inspection/submit', [SuperAdminController::class, 'submitAdminInspection'])->name('admin-inspection.submit');

        // Monitoring & Analytics
        Route::get('/monitoring', [SuperAdminController::class, 'monitoring'])->name('monitoring');
        Route::get('/inspections/{id}', [SuperAdminController::class, 'viewInspection'])->name('inspections.view');
        Route::get('/reports', [SuperAdminController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [SuperAdminController::class, 'exportReport'])->name('reports.export');
        
        // Settings
        Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
        Route::post('/settings/profile', [SuperAdminController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [SuperAdminController::class, 'changePassword'])->name('settings.password');
    });

    // ----------------------------------------------------
    // CAMPUS ADMIN PORTAL
    // ----------------------------------------------------
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Teacher Management (Restricted to assigned campus)
        Route::get('/teachers', [AdminController::class, 'teachers'])->name('teachers');
        Route::post('/teachers', [AdminController::class, 'addTeacher'])->name('teachers.add');
        Route::post('/teachers/{id}/edit', [AdminController::class, 'editTeacher'])->name('teachers.edit');
        Route::post('/teachers/{id}/delete', [AdminController::class, 'deleteTeacher'])->name('teachers.delete');
        Route::get('/teachers/{id}', [AdminController::class, 'teacherProfile'])->name('teachers.profile');

        // Teacher Inspection
        Route::get('/teacher-inspection', [AdminController::class, 'teacherInspectionForm'])->name('teacher-inspection');
        Route::post('/teacher-inspection/submit', [AdminController::class, 'submitTeacherInspection'])->name('teacher-inspection.submit');

        // Remarks Management
        Route::post('/teacher-remarks', [AdminController::class, 'addRemark'])->name('remarks.add');

        // Campus Inspection
        Route::get('/campus-inspection', [AdminController::class, 'campusInspectionForm'])->name('campus-inspection');
        Route::post('/campus-inspection/submit', [AdminController::class, 'submitCampusInspection'])->name('campus-inspection.submit');

        // Personal Performance Evaluation by Super Admin
        Route::get('/performance', [AdminController::class, 'personalPerformance'])->name('performance');

        // Settings
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings/profile', [AdminController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [AdminController::class, 'changePassword'])->name('settings.password');

        // Reports
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/reports/export', [AdminController::class, 'exportReport'])->name('reports.export');

        // Inspection Details
        Route::get('/inspections/{id}', [AdminController::class, 'viewInspection'])->name('inspections.view');
    });

    // ----------------------------------------------------
    // TEACHER PORTAL
    // ----------------------------------------------------
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [TeacherController::class, 'profile'])->name('profile');
        Route::get('/scores', [TeacherController::class, 'scores'])->name('scores');
        Route::get('/inspections/{id}', [TeacherController::class, 'viewInspection'])->name('inspections.view');
        
        // Settings
        Route::get('/settings', [TeacherController::class, 'settings'])->name('settings');
        Route::post('/settings/profile', [TeacherController::class, 'updateProfile'])->name('settings.profile');
        Route::post('/settings/password', [TeacherController::class, 'changePassword'])->name('settings.password');
    });
});
