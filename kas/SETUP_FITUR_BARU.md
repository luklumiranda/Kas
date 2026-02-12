# Panduan Setup Fitur Baru - Koperasi Swamitra

## Fitur yang Telah Ditambahkan

### 1. Dashboard Ringkasan (The Command Center)
- **File**: `resources/views/dashboard/index.blade.php`
- **Controller**: `app/Http/Controllers/DashboardController.php`
- **Fitur**:
  - Widget Saldo Real-time (Total, Pemasukan, Pengeluaran)
  - Grafik Arus Kas (12 bulan terakhir menggunakan Chart.js)
  - Status Tunggakan (Total piutang, Overdue, Count)
  - Top Debtors dan Expense by Category Chart
  - Recent Transactions

### 2. Manajemen Tagihan (Bills) & Tagihan Otomatis
- **Files**:
  - Model: `app/Models/Bill.php`
  - Controller: `app/Http/Controllers/BillController.php`
  - Views: `resources/views/bills/`
  - Migration: `database/migrations/2024_02_04_100004_create_bills_table.php`

- **Fitur**:
  - CRUD Tagihan
  - Generate Tagihan Massal (untuk semua siswa aktif)
  - Catat Pembayaran Tagihan
  - Status: Pending, Paid, Overdue
  - Laporan Tagihan

### 3. Pencatatan Pengeluaran & Digital Receipt
- **Files**:
  - Models: `app/Models/Expense.php`, `app/Models/ExpenseCategory.php`, `app/Models/ExpenseReceipt.php`
  - Controllers: `app/Http/Controllers/ExpenseController.php`, `app/Http/Controllers/ExpenseCategoryController.php`
  - Views: `resources/views/expenses/`, `resources/views/expense-categories/`
  - Migrations: `database/migrations/2024_02_04_100002_create_expenses_table.php` dll



### 4. Integrasi Telegram Bot
- **Files**:
  - Model: `app/Models/TelegramUser.php`
  - Controller: `app/Http/Controllers/TelegramController.php`
  - Migration: `database/migrations/2024_02_04_100005_create_telegram_users_table.php`

- **Fitur**:
  - Webhook untuk menerima pesan Telegram
  - Command `/start`, `/status`, `/bantuan`
  - Broadcast Reminder ke siswa yang memiliki tunggakan
  - Auto-save Telegram User ID untuk komunikasi

### 5. Laporan PDF & Excel
- **Files**:
  - Controller: `app/Http/Controllers/TransparencyReportController.php`
  - Export Class: `app/Exports/TransparencyReportExport.php`
  - Views: `resources/views/transparency/`, `resources/views/reports/pdf.blade.php`
  - Migration: `database/migrations/2024_02_04_100006_create_transparency_reports_table.php`

- **Fitur**:
  - Export Laporan ke PDF (DomPDF - sudah terinstall)
  - Export Laporan ke Excel (maatwebsite/excel)
  - Filter Laporan berdasarkan Tanggal

### 6. Laporan Transparansi Publik (Read-Only)
- **Fitur**:
  - Akses publik tanpa login via token unik
  - Link dapat dibagikan ke seluruh siswa
  - Tampilan JSON/HTML untuk melihat mutasi kas
  - Update terkait: Dapat dibuka melalui route `/laporan-publik/{token}`

## Instalasi & Setup

### Step 1: Install Dependencies

```bash
composer require maatwebsite/excel
```

### Step 2: Run Migrations

```bash
php artisan migrate
```

### Step 3: Setup Telegram Bot (Optional tapi Penting)

1. Buat bot dengan BotFather di Telegram
2. Dapatkan token bot
3. Tambahkan ke `.env`:

```env
TELEGRAM_BOT_TOKEN=your_bot_token_here
```

4. Setup webhook (optional):

```bash
php artisan tinker
> app('App\Http\Controllers\TelegramController')->setupWebhook()
```

### Step 4: Konfigurasi Storage

Pastikan `storage/app/public` sudah terhubung dengan public folder:

```bash
php artisan storage:link
```

### Step 5: Publish Excel Config (Optional)

```bash
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

## Penggunaan Fitur

### Dashboard
- Akses: `/dashboard`
- Menampilkan ringkasan keuangan real-time dengan chart

### Tagihan
- Akses: `/tagihan`
- Buat tagihan individual atau massal
- Catat pembayaran dari siswa
- Lihat status: Pending, Paid, Overdue

### Pengeluaran
- Akses: `/pengeluaran`
- Input pengeluaran dengan upload 1+ bukti nota
- Kelompokkan per kategori
- View detail dengan bukti

### Transparansi & Laporan
- Akses: `/laporan-transparansi`
- Buat laporan transparansi untuk periode tertentu
- Dapatkan link publik (token) untuk dibagikan
- Export ke PDF atau Excel

### Telegram (Manual Setup)

Untuk mengirim reminder Telegram ke siswa:

```php
// Di dalam controller mana saja
$telegram = new TelegramController();
$telegram->sendMessage($chatId, $message);

// Atau broadcast ke semua yang overdue
Route::post('/telegram/broadcast', [TelegramController::class, 'broadcastDebtReminder']);
```

## Routes yang Ditambahkan

```php
// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Bills
Route::resource('/tagihan', BillController::class, ['names' => 'bill']);
Route::post('/tagihan/mark-paid/{bill}', [BillController::class, 'markAsPaid'])->name('bill.markAsPaid');
Route::post('/tagihan/generate-routine', [BillController::class, 'generateRoutineBills'])->name('bill.generateRoutine');
Route::get('/tagihan/laporan', [BillController::class, 'report'])->name('bill.report');

// Expenses
Route::resource('/pengeluaran', ExpenseController::class, ['names' => 'expense']);
Route::delete('/pengeluaran-bukti/{receipt}', [ExpenseController::class, 'deleteReceipt'])->name('expense.deleteReceipt');
Route::get('/pengeluaran/laporan', [ExpenseController::class, 'report'])->name('expense.report');

// Expense Categories
Route::resource('/kategori-pengeluaran', ExpenseCategoryController::class, ['names' => 'expense-category']);

// Transparency Reports
Route::resource('/laporan-transparansi', TransparencyReportController::class, ['names' => 'transparency']);
Route::get('/laporan-transparansi/export/pdf', [TransparencyReportController::class, 'exportPdf'])->name('transparency.exportPdf');
Route::get('/laporan-transparansi/export/excel', [TransparencyReportController::class, 'exportExcel'])->name('transparency.exportExcel');

// Telegram
Route::post('/telegram/broadcast', [TelegramController::class, 'broadcastDebtReminder'])->name('telegram.broadcast');

// Public Transparency Report
Route::get('/laporan-publik/{token}', [TransparencyReportController::class, 'publicView'])->name('transparency.public');

// Telegram Webhook
Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);
```

## Database Schema

### bills
- id, customer_id, bill_type, amount, paid_amount, due_date, paid_date, notes, timestamps

### expenses
- id, expense_category_id, created_by, description, amount, expense_date, notes, timestamps

### expense_receipts
- id, expense_id, file_path, file_name, mime_type, file_size, timestamps

### expense_categories
- id, name, description, timestamps

### telegram_users
- id, customer_id, telegram_id, first_name, last_name, username, is_active, timestamps

### transparency_reports
- id, title, description, start_date, end_date, access_token, is_active, timestamps

## Catatan Penting

1. **Telegram Bot**: Pastikan bot sudah di-start oleh user terlebih dahulu sebelum mengirim pesan
2. **Storage**: Pastikan folder `storage/app/public` dan `public/storage` terhubung dengan benar
3. **Export**: Untuk Excel export, perlu install `maatwebsite/excel` terlebih dahulu
4. **Chart.js**: Sudah included via CDN, tidak perlu install

## Troubleshooting

### Laporan tidak bisa di-export
- Pastikan `maatwebsite/excel` sudah ter-install: `composer require maatwebsite/excel`
- Cek folder `storage/app/public` bisa di-tulis

### Telegram Webhook tidak bekerja
- Pastikan TELEGRAM_BOT_TOKEN sudah set di .env
- Pastikan server bisa diakses dari internet
- Test webhook via: `POST /telegram/webhook` dengan Telegram Update data

### File bukti tidak tersimpan
- Pastikan `php artisan storage:link` sudah dijalankan
- Cek permissions folder `storage/app/public` dan `public/storage`

## Next Steps (Opsional)

1. Customize template PDF sesuai dengan branding koperasi
2. Tambahkan validasi lebih ketat untuk data keuangan
3. Setup notifikasi email untuk laporan bulanan
4. Integrasi dengan sistem pembayaran (if needed)
5. Statistik/Analytics lebih detail

