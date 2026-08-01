<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/preview', function () {
    return view('preview');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ダッシュボード
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // 顧客

    // csvインポート
    Route::get('customers/import', [CustomerController::class, 'showImport'])
        ->name('customers.import');

    Route::post('customers/import', [CustomerController::class, 'import'])
        ->name('customers.import.store');

    // csvエクスポート
    Route::get('customers/export', [CustomerController::class, 'export'])
        ->name('customers.export');

    Route::resource('customers', CustomerController::class)->except(['show']);

    // 商談
    Route::resource('deals', DealController::class)->except(['show']);

    // 活動
    Route::resource('activities', ActivityController::class)
        ->except(['show']);

    // タスク
    Route::resource('tasks', TaskController::class)
        ->except(['show']);
});

// 管理者専用
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('users', UserController::class);
});

require __DIR__ . '/auth.php';
