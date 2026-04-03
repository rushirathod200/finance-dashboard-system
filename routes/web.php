<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\FinancialRecordController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('app.dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('app.login.store');
});

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('app.logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('app.dashboard');

    Route::prefix('financial-records')
        ->name('app.financial-records.')
        ->group(function () {
            Route::get('/', [FinancialRecordController::class, 'index'])->name('index');
            Route::get('/create', [FinancialRecordController::class, 'create'])->name('create');
            Route::post('/', [FinancialRecordController::class, 'store'])->name('store');
            Route::get('/{financialRecord}', [FinancialRecordController::class, 'show'])->name('show');
            Route::get('/{financialRecord}/edit', [FinancialRecordController::class, 'edit'])->name('edit');
            Route::put('/{financialRecord}', [FinancialRecordController::class, 'update'])->name('update');
            Route::delete('/{financialRecord}', [FinancialRecordController::class, 'destroy'])->name('destroy');
        });

    Route::middleware('role:admin')
        ->prefix('users')
        ->name('app.users.')
        ->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
});
