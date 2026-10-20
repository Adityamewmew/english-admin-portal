<?php

use App\Http\Controllers\Admin\CurriculumModuleController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GradebookController;
use App\Http\Controllers\Admin\QuestionBankController;
use App\Http\Controllers\Admin\SidebarMenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});
Route::get('/admin', function () {
    return redirect()->route('admin.dashboard');
});

// Auth Routes (Login Admin di /admin/login)
Route::get('/admin/login', [AuthController::class, 'login'])->name('login');
Route::post('/admin/login', [AuthController::class, 'doLogin'])->name('login.post');
Route::get('/login', fn () => redirect()->route('login'));
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::middleware('access_type:1')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/add', [UserController::class, 'add'])->name('add');
        Route::post('/create', [UserController::class, 'doCreate'])->name('create');
        Route::get('/detail/{id}', [UserController::class, 'detail'])->name('detail');
        Route::get('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::post('/update/{id}', [UserController::class, 'doUpdate'])->name('doUpdate');
        Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('delete');
        Route::post('/reset-password/{id}', [UserController::class, 'resetPassword'])->name('resetPassword');
    });

    // Gradebook (Rekap Nilai Siswa) Routes
    Route::middleware('access_type:1')->prefix('gradebook')->name('gradebook.')->group(function () {
        Route::get('/', [GradebookController::class, 'index'])->name('index');
        Route::get('/student/{id}', [GradebookController::class, 'detail'])->name('detail');
    });

    // Modules Routes
    Route::middleware('access_type:1')->prefix('modules')->name('modules.')->group(function () {
        Route::get('/', [CurriculumModuleController::class, 'index'])->name('index');
        Route::get('/add', [CurriculumModuleController::class, 'add'])->name('add');
        Route::post('/create', [CurriculumModuleController::class, 'doCreate'])->name('create');
        Route::get('/detail/{id}', [CurriculumModuleController::class, 'detail'])->name('detail');
        Route::get('/update/{id}', [CurriculumModuleController::class, 'update'])->name('update');
        Route::post('/update/{id}', [CurriculumModuleController::class, 'doUpdate'])->name('doUpdate');
        Route::delete('/delete/{id}', [CurriculumModuleController::class, 'delete'])->name('delete');
    });

    // Sub Modules Routes
    Route::middleware('access_type:1')->prefix('sub-modules')->name('sub_modules.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SubModuleController::class, 'index'])->name('index');
        Route::get('/add', [\App\Http\Controllers\Admin\SubModuleController::class, 'add'])->name('add');
        Route::post('/create', [\App\Http\Controllers\Admin\SubModuleController::class, 'doCreate'])->name('create');
        Route::get('/detail/{id}', [\App\Http\Controllers\Admin\SubModuleController::class, 'detail'])->name('detail');
        Route::get('/update/{id}', [\App\Http\Controllers\Admin\SubModuleController::class, 'update'])->name('update');
        Route::post('/update/{id}', [\App\Http\Controllers\Admin\SubModuleController::class, 'doUpdate'])->name('doUpdate');
        Route::delete('/delete/{id}', [\App\Http\Controllers\Admin\SubModuleController::class, 'delete'])->name('delete');
    });

    // Questions (Placement Test) Routes
    Route::middleware('access_type:1')->prefix('questions')->name('questions.')->group(function () {
        Route::get('/', [QuestionBankController::class, 'index'])->name('index');
        Route::get('/add', [QuestionBankController::class, 'add'])->name('add');
        Route::post('/create', [QuestionBankController::class, 'doCreate'])->name('create');
        Route::get('/detail/{id}', [QuestionBankController::class, 'detail'])->name('detail');
        Route::get('/update/{id}', [QuestionBankController::class, 'update'])->name('update');
        Route::post('/update/{id}', [QuestionBankController::class, 'doUpdate'])->name('doUpdate');
        Route::delete('/delete/{id}', [QuestionBankController::class, 'delete'])->name('delete');
    });

    Route::middleware('access_type:1')->prefix('sidebar-menu')->name('sidebar_menu.')->group(function () {
        Route::get('/', [SidebarMenuController::class, 'index'])->name('index');
        Route::get('/refresh-cache', [SidebarMenuController::class, 'refreshCache'])->name('refresh_cache');
        Route::get('/add', [SidebarMenuController::class, 'add'])->name('add');
        Route::post('/create', [SidebarMenuController::class, 'doCreate'])->name('create');
        Route::get('/update/{id}', [SidebarMenuController::class, 'update'])->name('update');
        Route::post('/update/{id}', [SidebarMenuController::class, 'doUpdate'])->name('doUpdate');
        Route::delete('/delete/{id}', [SidebarMenuController::class, 'delete'])->name('delete');
        Route::get('/{id}/access', [SidebarMenuController::class, 'access'])->name('access');
        Route::post('/{id}/access', [SidebarMenuController::class, 'doAccess'])->name('doAccess');
        Route::get('/role-access/{accessType}', [SidebarMenuController::class, 'roleAccess'])->name('role_access');
        Route::post('/role-access/{accessType}', [SidebarMenuController::class, 'doRoleAccess'])->name('doRoleAccess');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/change-password', [UserController::class, 'changePassword'])->name('change_password');
        Route::post('/change-password', [UserController::class, 'doChangePassword'])->name('do_change_password');
    });
});
