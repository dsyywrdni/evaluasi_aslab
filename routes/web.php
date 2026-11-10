<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\PenilaianController as AdminPenilaian;
use App\Http\Controllers\Admin\AsistenController;
use App\Http\Controllers\Admin\KriteriaController;
use App\Http\Controllers\Admin\SubKriteriaController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Kalab\DashboardController as KalabDashboard;
use App\Http\Controllers\Kalab\PenilaianController as KalabPenilaian;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// 'semester' = middleware SetSemester
Route::middleware(['auth', 'semester'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ================= ROLE 1: ADMIN =================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Data asisten
        Route::resource('asisten', AsistenController::class)->except('show');
        Route::patch('asisten/{id}/status', [AsistenController::class, 'updateStatus'])->name('asisten.status');

        // Kriteria & sub-kriteria
        Route::resource('kriteria', KriteriaController::class);
        Route::post('kriteria/{kriteria_id}/sub', [SubKriteriaController::class, 'store'])->name('sub_kriteria.store');
        Route::put('sub/{id}', [SubKriteriaController::class, 'update'])->name('sub_kriteria.update');
        Route::delete('sub/{id}', [SubKriteriaController::class, 'destroy'])->name('sub_kriteria.destroy');
        Route::post('sub/{sub_id}/rubrik', [SubKriteriaController::class, 'storeRubrik'])->name('sub_kriteria.rubrik.store');

        // Hasil Perankingan & Laporan (Admin tidak menilai, hanya melihat hasil & mencetak laporan)
        Route::get('/penilaian', [AdminPenilaian::class, 'index'])->name('penilaian.index');
        Route::get('/penilaian/hasil', [AdminPenilaian::class, 'hasil'])->name('penilaian.hasil');
        Route::get('/penilaian/cetak', [AdminPenilaian::class, 'cetak'])->name('penilaian.cetak');
        Route::get('/penilaian/detail/{aslab_id}', [AdminPenilaian::class, 'detail'])->name('penilaian.detail');

        // Semester
        Route::resource('semester', SemesterController::class)->only(['index', 'store', 'destroy']);
        Route::post('semester/{id}/aktifkan', [SemesterController::class, 'aktifkan'])->name('semester.aktifkan');
    });

    // ================= ROLE 2: KALAB =================
    Route::middleware('role:kalab')->prefix('kalab')->name('kalab.')->group(function () {
        Route::get('/dashboard', [KalabDashboard::class, 'index'])->name('dashboard');

        Route::get('/penilaian', [KalabPenilaian::class, 'index'])->name('penilaian.index');
        Route::get('/penilaian/create/{aslab_id}', [KalabPenilaian::class, 'create'])->name('penilaian.create');
        Route::post('/penilaian/store', [KalabPenilaian::class, 'store'])->name('penilaian.store');
        Route::delete('/penilaian/{aslab_id}', [KalabPenilaian::class, 'destroy'])->name('penilaian.destroy');
        Route::get('/penilaian/hasil', [KalabPenilaian::class, 'hasil'])->name('penilaian.hasil');
        Route::get('/penilaian/detail/{aslab_id}', [KalabPenilaian::class, 'detail'])->name('penilaian.detail');
    });
});
