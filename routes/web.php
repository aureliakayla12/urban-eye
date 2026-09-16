<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\MasyarakatController;
use App\Http\Controllers\Admin\PetugasController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Masyarakat\HelpController as MasyarakatHelpController;
use App\Http\Controllers\Masyarakat\LeaderboardController;
use App\Http\Controllers\Masyarakat\ReportController as MasyarakatReportController;
use App\Http\Controllers\Masyarakat\RewardController as MasyarakatRewardController;
use App\Http\Controllers\Masyarakat\SettingsController as MasyarakatSettingsController;
use App\Http\Controllers\Petugas\HelpController as PetugasHelpController;
use App\Http\Controllers\Petugas\HistoryController;
use App\Http\Controllers\Petugas\MapController as PetugasMapController;
use App\Http\Controllers\Petugas\SettingsController as PetugasSettingsController;
use App\Http\Controllers\Petugas\StatisticsController as PetugasStatisticsController;
use App\Http\Controllers\Petugas\TaskController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


/* Google OAuth Routes */

Route::get('/auth/google', [GoogleController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])
    ->name('google.callback');

require __DIR__.'/auth.php';


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


   /* ADMIN */

    Route::prefix('admin')->middleware('role:admin')->group(function () {

        // Kelola Laporan
        Route::prefix('reports')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('admin.reports.index');
            Route::get('/{id}', [ReportController::class, 'show'])->name('admin.reports.show');
            Route::get('/{id}/edit', [ReportController::class, 'edit'])->name('admin.reports.edit');
            Route::put('/{id}', [ReportController::class, 'update'])->name('admin.reports.update');
            Route::delete('/{id}', [ReportController::class, 'destroy'])->name('admin.reports.destroy');
        });


        // Kelola Pengguna - Masyarakat
        Route::get('/users/masyarakat', [MasyarakatController::class, 'index'])->name('admin.users.masyarakat.index');


        // Kelola Pengguna - Petugas
        Route::prefix('users/petugas')->group(function () {
            Route::get('/', [PetugasController::class, 'index'])->name('admin.users.petugas.index');
            Route::get('/create', [PetugasController::class, 'create'])->name('admin.users.petugas.create');
            Route::post('/', [PetugasController::class, 'store'])->name('admin.users.petugas.store');
            Route::get('/{id}/edit', [PetugasController::class, 'edit'])->name('admin.users.petugas.edit');
            Route::put('/{id}', [PetugasController::class, 'update'])->name('admin.users.petugas.update');
            Route::delete('/{id}', [PetugasController::class, 'destroy'])->name('admin.users.petugas.destroy');
        });


        // Statistik & Grafik
        Route::get('/statistics', [StatisticsController::class, 'index'])->name('admin.statistics.index');


        // Peta Monitoring
        Route::get('/map', [MapController::class, 'index'])->name('admin.map.index');


        // Reward & Leaderboard
        Route::prefix('gamification')->group(function () {
            Route::get('/points', [RewardController::class, 'points'])->name('admin.gamification.points');
            Route::get('/leaderboard', [RewardController::class, 'leaderboard'])->name('admin.gamification.leaderboard');
            Route::get('/rewards', [RewardController::class, 'index'])->name('admin.gamification.rewards');
            Route::get('/rewards/create', [RewardController::class, 'create'])->name('admin.gamification.rewards.create');
            Route::post('/rewards', [RewardController::class, 'store'])->name('admin.gamification.rewards.store');
            Route::get('/rewards/{id}/edit', [RewardController::class, 'edit'])->name('admin.gamification.rewards.edit');
            Route::put('/rewards/{id}', [RewardController::class, 'update'])->name('admin.gamification.rewards.update');
            Route::delete('/rewards/{id}', [RewardController::class, 'destroy'])
                ->name('admin.gamification.rewards.destroy');
        });


        // Master Data
        Route::prefix('master')->group(function () {

            // Categories
            Route::prefix('categories')->group(function () {
                Route::get('/', [CategoryController::class, 'index'])->name('admin.master.categories.index');
                Route::get('/create', [CategoryController::class, 'create'])->name('admin.master.categories.create');
                Route::post('/', [CategoryController::class, 'store'])->name('admin.master.categories.store');
                Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('admin.master.categories.edit');
                Route::put('/{id}', [CategoryController::class, 'update'])->name('admin.master.categories.update');
                Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('admin.master.categories.destroy');
            });


            // Status Laporan
            Route::get('/statuses', [StatusController::class, 'index'])->name('admin.master.statuses.index');
        });


        // Pengaturan Sistem
        Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');


        // Laporan & Export
        Route::get('/export', [ExportController::class, 'index'])->name('admin.export.index');
    });


    /* PETUGAS */

    Route::prefix('petugas')->middleware('role:petugas')->group(function () {

        // Tugas Saya
        Route::prefix('tasks')->group(function () {
            Route::get('/', [TaskController::class, 'index'])->name('petugas.tasks.index');
            Route::get('/{id}', [TaskController::class, 'show'])->name('petugas.tasks.show');
            Route::put('/{id}', [TaskController::class, 'update'])->name('petugas.tasks.update');
        });


        // Peta Laporan
        Route::get('/map', [PetugasMapController::class, 'index'])->name('petugas.map.index');


        // Riwayat Penanganan
        Route::get('/history', [HistoryController::class, 'index'])->name('petugas.history.index');


        // Statistik
        Route::get('/statistics', [PetugasStatisticsController::class, 'index'])->name('petugas.statistics.index');


        // Pengaturan
        Route::get('/settings', [PetugasSettingsController::class, 'index'])->name('petugas.settings.index');


        // Bantuan
        Route::get('/help', [PetugasHelpController::class, 'index'])->name('petugas.help.index');
    });


    /* MASYARAKAT */

    Route::prefix('masyarakat')->middleware('role:masyarakat')->group(function () {

        // Laporan
        Route::prefix('reports')->group(function () {
            Route::get('/', [MasyarakatReportController::class, 'index'])->name('masyarakat.reports.index');
            Route::get('/create', [MasyarakatReportController::class, 'create'])->name('masyarakat.reports.create');
            Route::post('/', [MasyarakatReportController::class, 'store'])->name('masyarakat.reports.store');
            Route::get('/{report}', [MasyarakatReportController::class, 'show'])->name('masyarakat.reports.show');
        });


        // Leaderboard
        Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('masyarakat.leaderboard.index');


        // Reward Saya
        Route::get('/rewards', [MasyarakatRewardController::class, 'index'])->name('masyarakat.rewards.index');


        // Pengaturan
        Route::get('/settings', [MasyarakatSettingsController::class, 'index'])->name('masyarakat.settings.index');


        // Bantuan
        Route::get('/help', [MasyarakatHelpController::class, 'index'])->name('masyarakat.help.index');
    });
});