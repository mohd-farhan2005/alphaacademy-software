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
        Route::resource('invoices', \App\Http\Controllers\InvoiceController::class);
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
    
    // HA Syllabus Routes
    Route::get('/ha-syllabus', [\App\Http\Controllers\HaSyllabusController::class, 'index'])->name('ha-syllabus.index');
    Route::get('/ha-syllabus/progress', [\App\Http\Controllers\HaSyllabusController::class, 'progress'])->name('ha-syllabus.progress');
    Route::post('/ha-syllabus/paper', [\App\Http\Controllers\HaSyllabusController::class, 'storePaper'])->name('ha-syllabus.storePaper');
    Route::post('/ha-syllabus/module', [\App\Http\Controllers\HaSyllabusController::class, 'storeModule'])->name('ha-syllabus.storeModule');
    Route::post('/ha-syllabus/unit', [\App\Http\Controllers\HaSyllabusController::class, 'storeUnit'])->name('ha-syllabus.storeUnit');
    Route::post('/ha-syllabus/batch', [\App\Http\Controllers\HaSyllabusController::class, 'storeBatch'])->name('ha-syllabus.storeBatch');
    Route::post('/ha-syllabus/unit/toggle', [\App\Http\Controllers\HaSyllabusController::class, 'toggleUnit'])->name('ha-syllabus.toggleUnit');
    Route::post('/ha-syllabus/unit/toggle-form', [\App\Http\Controllers\HaSyllabusController::class, 'toggleUnitForm'])->name('ha-syllabus.toggleUnitForm');

    // DME Syllabus Routes
    Route::get('/dme-syllabus', [\App\Http\Controllers\DmeSyllabusController::class, 'index'])->name('dme-syllabus.index');
    Route::get('/dme-syllabus/progress', [\App\Http\Controllers\DmeSyllabusController::class, 'progress'])->name('dme-syllabus.progress');
    Route::post('/dme-syllabus/batch', [\App\Http\Controllers\DmeSyllabusController::class, 'storeBatch'])->name('dme-syllabus.storeBatch');
    Route::post('/dme-syllabus/sub-unit', [\App\Http\Controllers\DmeSyllabusController::class, 'storeSubUnit'])->name('dme-syllabus.storeSubUnit');
    Route::put('/dme-syllabus/sub-unit/{id}', [\App\Http\Controllers\DmeSyllabusController::class, 'updateSubUnit'])->name('dme-syllabus.updateSubUnit');
    Route::delete('/dme-syllabus/sub-unit/{id}', [\App\Http\Controllers\DmeSyllabusController::class, 'destroySubUnit'])->name('dme-syllabus.destroySubUnit');
    Route::post('/dme-syllabus/sub-module', [\App\Http\Controllers\DmeSyllabusController::class, 'storeSubModule'])->name('dme-syllabus.storeSubModule');
    Route::put('/dme-syllabus/sub-module/{id}', [\App\Http\Controllers\DmeSyllabusController::class, 'updateSubModule'])->name('dme-syllabus.updateSubModule');
    Route::delete('/dme-syllabus/sub-module/{id}', [\App\Http\Controllers\DmeSyllabusController::class, 'destroySubModule'])->name('dme-syllabus.destroySubModule');
    Route::post('/dme-syllabus/sub-module/toggle', [\App\Http\Controllers\DmeSyllabusController::class, 'toggleSubModule'])->name('dme-syllabus.toggleSubModule');
    Route::post('/dme-syllabus/sub-module/toggle-form', [\App\Http\Controllers\DmeSyllabusController::class, 'toggleSubModuleForm'])->name('dme-syllabus.toggleSubModuleForm');
    
    Route::post('/check-password', [PasswordCheckController::class, 'check'])->name('password.check');
    Route::post('/verify-password', [PasswordCheckController::class, 'verify'])->name('password.verify');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
