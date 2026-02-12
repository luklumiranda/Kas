# 🚀 QUICK START GUIDE - Fitur Baru Koperasi Swamitra

## 1️⃣ Instalasi (2 Pilihan)

### Opsi A: Automatic (Recommended)
**Windows:**
```bash
setup-new-features.bat
```

**Linux/Mac:**
```bash
bash setup-new-features.sh
```

### Opsi B: Manual
```bash
# Install Excel export library
composer require maatwebsite/excel

# Run database migrations
php artisan migrate

# Seed kategori pengeluaran default
php artisan db:seed --class=ExpenseCategorySeeder

# Create storage symlink
php artisan storage:link

# Clear cache
php artisan cache:clear
```

---

## 2️⃣ Akses Fitur Baru

| Fitur | URL | Deskripsi |
|-------|-----|-----------|
| **Dashboard** | `/dashboard` | Ringkasan keuangan & grafik |
| **Tagihan** | `/tagihan` | Manajemen tagihan siswa |
| **Pengeluaran** | `/pengeluaran` | Input pengeluaran + bukti nota |
| **Kategori** | `/kategori-pengeluaran` | Setup kategori pengeluaran |
| **Laporan** | `/laporan-transparansi` | Buat & export laporan |
| **Laporan Publik** | `/laporan-publik/{token}` | Share ke siswa (no login) |

---

## 3️⃣ Penggunaan Fitur

### 📊 Dashboard
Klik menu **Dashboard** untuk melihat:
- Saldo real-time
- Grafik arus kas 12 bulan
- Top 5 peminjam
- Pengeluaran per kategori
- Transaksi terbaru

### 💰 Tagihan (Billing)
**Untuk create tagihan individual:**
1. Ke `/tagihan` → Klik "Tagihan Baru"
2. Pilih siswa, tipe tagihan, jumlah, jatuh tempo
3. Click "Simpan"

**Untuk create massal:**
1. Ke `/tagihan` → Klik "Buat Massal"
2. Input: Tipe tagihan, jumlah, jatuh tempo
3. Click "Generate Tagihan" → Otomatis untuk semua siswa aktif

**Untuk catat pembayaran:**
1. Klik tombol "Bayar" di tagihan
2. Input jumlah yang dibayar
3. Click "Catat Pembayaran"

### 📝 Pengeluaran
**Untuk input pengeluaran:**
1. Ke `/pengeluaran` → Klik "Pengeluaran Baru"
2. Pilih kategori, input deskripsi, jumlah, tanggal
3. Upload **minimal 1** bukti nota (PDF/JPG/PNG)
4. Click "Simpan"

**View bukti:**
1. Click icon mata di tagihan pengeluaran
2. Download atau lihat bukti nota

### 📄 Laporan Transparansi
**Untuk buat laporan publik:**
1. Ke `/laporan-transparansi` → Klik "Laporan Baru"
2. Input judul, periode (dari-sampai)
3. Click "Buat Laporan" → Otomatis dapat token
4. Share link ke siswa: `http://localhost/laporan-publik/{token}`

**Untuk export:**
1. Di halaman laporan, set tanggal range
2. Click "Export PDF" atau "Export Excel"
3. File otomatis download

---

## 4️⃣ Setup Telegram (Optional)

Jika ingin fitur reminder Telegram ke siswa:

1. **Buat Bot:**
   - Buka Telegram, cari @BotFather
   - Ketik `/newbot`
   - Follow instruksi, dapat TOKEN

2. **Konfigurasi:**
   - Buka `.env`
   - Tambah: `TELEGRAM_BOT_TOKEN=xxxxx`
   - Save

3. **Siswa Start Bot:**
   - Cari bot Anda di Telegram
   - Click "Start"
   - Atau ketik `/start`

4. **Test Status:**
   - Siswa ketik `/status` di bot
   - Bot akan tampilkan tagihan pending

5. **Broadcast Reminder:**
   - Admin ke `/telegram/broadcast`
   - Bot otomatis kirim reminder ke semua yang overdue

📖 Detail lebih: Baca `TELEGRAM_BOT_GUIDE.md`

---

## 5️⃣ Troubleshooting

### ❌ Error "Class not found"
**Solusi:** Run `composer autoload` atau `php artisan dump-autoload`

### ❌ Migrations error
**Solusi:** 
```bash
php artisan migrate:reset
php artisan migrate
```

### ❌ File upload tidak tersimpan
**Solusi:** 
```bash
php artisan storage:link
chmod -R 755 storage/app/public
```

### ❌ PDF export error
**Solusi:** Pastikan extension `php-xml` enabled

### ❌ Excel export error
**Solusi:** 
```bash
composer require maatwebsite/excel
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

### ❌ Telegram bot tidak kirim pesan
**Solusi:**
- Cek `TELEGRAM_BOT_TOKEN` di `.env` benar
- Pastikan siswa sudah `/start` bot
- Cek internet connection server

---

## 📊 Database Tables

Automatic ter-create di migration:

| Table | Isi |
|-------|-----|
| `bills` | Data tagihan siswa |
| `expenses` | Data pengeluaran |
| `expense_receipts` | File bukti nota |
| `expense_categories` | Kategori pengeluaran |
| `telegram_users` | Link Telegram-Siswa |
| `transparency_reports` | Laporan publik |

---

## 🔐 Security Notes

⚠️ **Produksi:**
- Gunakan `.env` yang aman (jangan share token)
- Implement role-based access (bendahara, admin, siswa)
- Setup HTTPS untuk laporan publik
- Regular backup database

---

## 📚 Documentation

Baca file-file ini untuk detail lebih lanjut:

- `SETUP_FITUR_BARU.md` - Panduan lengkap instalasi
- `TELEGRAM_BOT_GUIDE.md` - Setup & integrasi Telegram
- `API_DOCUMENTATION.md` - Referensi API endpoints
- `IMPLEMENTATION_CHECKLIST.md` - Checklist implementasi

---

## 💡 Pro Tips

✅ **Dashboard:** 
- Akses setiap hari untuk monitoring keuangan
- Perhatikan status "Overdue" untuk follow-up

✅ **Tagihan Massal:**
- Gunakan feature ini tiap minggu/bulan untuk efisiensi
- Contoh: Senin = Rp5.000, Akhir bulan = Rp50.000

✅ **Upload Bukti:**
- Foto nota dengan jelas untuk audit trail
- Multiple file untuk rincian pembelian

✅ **Laporan Publik:**
- Update laporan tiap bulan
- Share ke grup WhatsApp kelas
- Tingkatkan transparansi & kepercayaan

✅ **Telegram Reminder:**
- Setup otomatis reminder setiap Minggu
- Efektif kurangi tunggakan
- Friendly reminder tanpa terkesan "menagih"

---

## 🎯 Next Steps

1. ✅ Run setup script
2. ✅ Verify `/dashboard` bisa diakses
3. ✅ Input kategori pengeluaran di `/kategori-pengeluaran`
4. ✅ Create sample bill & expense untuk test
5. ✅ Test export PDF/Excel
6. ✅ (Optional) Setup Telegram bot
7. ✅ Share laporan publik ke siswa

