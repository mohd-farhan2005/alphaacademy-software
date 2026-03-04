<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Auth\PasswordCheckController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:super_admin')->group(function () {
        Route::resource('departments', DepartmentController::class);
    });

    Route::middleware('role:super_admin,ha_head')->group(function () {
        Route::get('credentials/export/excel', [\App\Http\Controllers\CredentialController::class, 'exportExcel'])->name('credentials.export.excel');
        Route::get('credentials/export/pdf', [\App\Http\Controllers\CredentialController::class, 'exportPdf'])->name('credentials.export.pdf');
        Route::resource('credentials', \App\Http\Controllers\CredentialController::class);
    });

    Route::middleware('role:super_admin,dme_head,ha_head,creatives_head')->group(function () {
        Route::resource('employees', EmployeeController::class)->except(['show']);
    });
    
    // The show method is protected by the UserPolicy Gate inside the controller, 
    // so employees can view their own profile.
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');

    Route::resource('tasks', TaskController::class);
    
    Route::resource('calendar', \App\Http\Controllers\CalendarController::class)->only(['index', 'store', 'destroy']);
    
    Route::post('/check-password', [PasswordCheckController::class, 'check'])->name('password.check');
    Route::post('/verify-password', [PasswordCheckController::class, 'verify'])->name('password.verify');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
