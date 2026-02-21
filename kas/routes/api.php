<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\TransparencyReportController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Koneksi Database Mobile App
|--------------------------------------------------------------------------
|
| Routes untuk menghubungkan Mobile App dengan database website
| Base URL: http://localhost:8000/api (atau sesuai APP_URL)
|
*/

// ============================================
// AUTH ROUTES - Untuk login
// ============================================
Route::post('/login', [CustomerController::class, 'apiLogin']);
Route::post('/register', [CustomerController::class, 'apiRegister']);

// ============================================
// ROUTES DENGAN AUTHENTICATION
// ============================================
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ============ USER / SISWA (dari users table) ============
    Route::prefix('nasabah')->group(function () {
        Route::get('/', [UserController::class, 'apiIndex']); // Semua siswa
        Route::get('/{id}', [UserController::class, 'apiShow']); // Detail siswa
        Route::post('/', [UserController::class, 'apiStore']); // Buat siswa baru
        Route::put('/{id}', [UserController::class, 'apiUpdate']); // Update siswa
        Route::delete('/{id}', [UserController::class, 'apiDestroy']); // Hapus siswa
    });

    // ============ LOAN / PINJAMAN ============
    Route::prefix('pinjaman')->group(function () {
        Route::get('/', [LoanController::class, 'index']); // Semua pinjaman
        Route::get('/{id}', [LoanController::class, 'show']); // Detail pinjaman
        Route::post('/', [LoanController::class, 'store']); // Ciptakan pinjaman
        Route::put('/{id}', [LoanController::class, 'update']); // Update pinjaman
        Route::delete('/{id}', [LoanController::class, 'destroy']); // Hapus pinjaman
        Route::post('/{id}/angsuran', [LoanController::class, 'addInstallment']); // Tambah cicilan
        Route::get('/{id}/status', [LoanController::class, 'getStatus']); // Status pinjaman
    });

    // ============ DEPOSIT / SIMPANAN ============
    Route::prefix('simpanan')->group(function () {
        Route::get('/', [DepositController::class, 'index']); // Semua simpanan
        Route::get('/{id}', [DepositController::class, 'show']); // Detail simpanan
        Route::post('/', [DepositController::class, 'store']); // Tambah simpanan
        Route::put('/{id}', [DepositController::class, 'update']); // Update simpanan
        Route::delete('/{id}', [DepositController::class, 'destroy']); // Hapus simpanan
        Route::get('/{id}/riwayat', [DepositController::class, 'history']); // Riwayat simpanan
    });

    // ============ BILL / TAGIHAN ============
    Route::prefix('tagihan')->group(function () {
        Route::get('/', [BillController::class, 'apiIndex']); // Semua tagihan
        Route::get('/{id}', [BillController::class, 'apiShow']); // Detail tagihan
        Route::post('/', [BillController::class, 'apiStore']); // Buat tagihan
        Route::put('/{id}', [BillController::class, 'apiUpdate']); // Update tagihan
        Route::delete('/{id}', [BillController::class, 'apiDestroy']); // Hapus tagihan
        Route::post('/{id}/bayar', [BillController::class, 'pay']); // Bayar tagihan
        Route::get('/{id}/status', [BillController::class, 'getStatus']); // Status tagihan
    });

    // ============ EXPENSE / PENGELUARAN ============
    Route::prefix('pengeluaran')->group(function () {
        Route::get('/', [ExpenseController::class, 'apiIndex']); // Semua pengeluaran
        Route::get('/{id}', [ExpenseController::class, 'apiShow']); // Detail pengeluaran
        Route::post('/', [ExpenseController::class, 'apiStore']); // Catat pengeluaran
        Route::put('/{id}', [ExpenseController::class, 'apiUpdate']); // Update pengeluaran
        Route::delete('/{id}', [ExpenseController::class, 'apiDestroy']); // Hapus pengeluaran
        Route::get('/kategori/list', [ExpenseController::class, 'categories']); // Daftar kategori
    });

    // ============ EXPENSE CATEGORY / KATEGORI PENGELUARAN ============
    Route::prefix('expense-category')->group(function () {
        Route::get('/', [ExpenseCategoryController::class, 'apiIndex']); // Semua kategori
        Route::get('/{id}', [ExpenseCategoryController::class, 'apiShow']); // Detail kategori
        Route::post('/', [ExpenseCategoryController::class, 'apiStore']); // Buat kategori
        Route::put('/{id}', [ExpenseCategoryController::class, 'apiUpdate']); // Update kategori
        Route::delete('/{id}', [ExpenseCategoryController::class, 'apiDestroy']); // Hapus kategori
    });

    // ============ TRANSPARENCY REPORT / LAPORAN TRANSPARANSI ============
    Route::prefix('transparency-report')->group(function () {
        Route::get('/', [TransparencyReportController::class, 'apiIndex']); // Semua laporan
        Route::get('/{id}', [TransparencyReportController::class, 'apiShow']); // Detail laporan
        Route::post('/', [TransparencyReportController::class, 'apiStore']); // Buat laporan
        Route::put('/{id}', [TransparencyReportController::class, 'apiUpdate']); // Update laporan
        Route::delete('/{id}', [TransparencyReportController::class, 'apiDestroy']); // Hapus laporan
    });

    // ============ VISIT / KUNJUNGAN ============
    Route::prefix('kunjungan')->group(function () {
        Route::get('/', [VisitController::class, 'index']); // Semua kunjungan
        Route::get('/{id}', [VisitController::class, 'show']); // Detail kunjungan
        Route::post('/', [VisitController::class, 'store']); // Catat kunjungan baru
        Route::put('/{id}', [VisitController::class, 'update']); // Update kunjungan
        Route::delete('/{id}', [VisitController::class, 'destroy']); // Hapus kunjungan
    });

    // ============ DASHBOARD SUMMARY ============
    Route::prefix('dashboard')->group(function () {
        Route::get('/summary', function (Request $request) {
            return [
                'total_nasabah' => \App\Models\Customer::count(),
                'total_pinjaman' => \App\Models\Loan::sum('amount'),
                'total_simpanan' => \App\Models\Deposit::sum('amount'),
                'total_pengeluaran' => \App\Models\Expense::sum('amount'),
            ];
        });
    });
});
