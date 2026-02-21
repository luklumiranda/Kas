<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ForeclosureController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\TransparencyReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Auth::routes();

// PUBLIC: Telegram Webhook
Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);

// PUBLIC: Transparency Report (Public Access)
Route::get('/laporan-publik/{token}', [TransparencyReportController::class, 'publicView'])->name('transparency.public');

Route::middleware('auth')->group(function() {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Routes lama tetap ada untuk backward compatibility
    Route::get('/dashboard-old', [HomeController::class, 'index'])->name('home');

    Route::resource('/siswa', UserController::class, ['names' => 'student']);
    Route::resource('/nasabah', CustomerController::class, ['names' => 'customer']);

    Route::post('/siswa/cetak', [UserController::class, 'print'])->name('student.print');
    Route::post('/nasabah/cetak', [CustomerController::class, 'print'])->name('customer.print');

    // NEW: Bill Management (Tagihan)
    Route::resource('/tagihan', BillController::class, ['names' => 'bill', 'parameters' => ['tagihan' => 'bill']]);
    Route::post('/tagihan/mark-paid/{bill}', [BillController::class, 'markAsPaid'])->name('bill.markAsPaid');
    Route::get('/tagihan/generate-routine', [BillController::class, 'generateRoutineBills'])->name('bill.generateRoutine');
    Route::get('/tagihan/laporan', [BillController::class, 'report'])->name('bill.report');

    // NEW: Expense Management (Pengeluaran)
    Route::resource('/pengeluaran', ExpenseController::class, ['names' => 'expense', 'parameters' => ['pengeluaran' => 'expense']]);
    Route::delete('/pengeluaran-bukti/{receipt}', [ExpenseController::class, 'deleteReceipt'])->name('expense.deleteReceipt');
    Route::get('/pengeluaran/laporan', [ExpenseController::class, 'report'])->name('expense.report');

    // NEW: Expense Categories
    Route::resource('/kategori-pengeluaran', ExpenseCategoryController::class, ['names' => 'expense-category', 'parameters' => ['kategori_pengeluaran' => 'expenseCategory']]);

    // NEW: Transparency Reports
    Route::resource('/laporan-transparansi', TransparencyReportController::class, ['names' => 'transparency', 'parameters' => ['laporan_transparansi' => 'transparencyReport']]);
    Route::get('/laporan-transparansi/export/pdf', [TransparencyReportController::class, 'exportPdf'])->name('transparency.exportPdf');
    Route::get('/laporan-transparansi/export/excel', [TransparencyReportController::class, 'exportExcel'])->name('transparency.exportExcel');

    // NEW: Telegram Management
    Route::post('/telegram/broadcast', [TelegramController::class, 'broadcastDebtReminder'])->name('telegram.broadcast');

    Route::as('transaction.')->prefix('transaksi')->group(function() {
        Route::resource('/pinjaman', LoanController::class, ['names' => 'loan']);
        Route::resource('/pembayaran', InstallmentController::class, ['names' => 'installment']);
        Route::resource('/simpanan', DepositController::class, ['names' => 'deposit']);
        Route::resource('/penarikan', WithdrawalController::class, ['names' => 'withdrawal']);

        Route::post('/pinjaman/cetak', [LoanController::class, 'print'])->name('loan.print');
        Route::post('/pembayaran/cetak', [InstallmentController::class, 'print'])->name('installment.print');
        Route::post('/simpanan/cetak', [DepositController::class, 'print'])->name('deposit.print');
        Route::post('/penarikan/cetak', [WithdrawalController::class, 'print'])->name('withdrawal.print');
    });

    Route::as('collection.')->prefix('kolektor')->group(function() {
        Route::resource('/nasabah-bermasalah', VisitController::class, ['names' => 'visit']);
        Route::resource('/penarikan-jaminan', ForeclosureController::class, ['names' => 'foreclosure']);

        Route::post('/nasabah-bermasalah/cetak', [VisitController::class, 'print'])->name('visit.print');
        Route::post('/penarikan-jaminan/cetak', [ForeclosureController::class, 'print'])->name('foreclosure.print');
    });

    Route::get('/pengaturan', [HomeController::class, 'profile'])->name('profile.show');
    Route::post('/pengaturan', [HomeController::class, 'update'])->name('profile.update');
    Route::delete('/pengaturan', [HomeController::class, 'truncate'])->name('profile.truncate');
});

