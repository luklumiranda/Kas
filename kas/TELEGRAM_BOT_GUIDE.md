# Panduan Integrasi Telegram Bot - Koperasi Swamitra

## Setup Telegram Bot

### 1. Buat Bot dengan BotFather

1. Buka Telegram dan cari **@BotFather**
2. Kirim `/start` 
3. Kirim `/newbot`
4. Ikuti instruksi untuk:
   - Masukkan nama bot (contoh: "Koperasi Swamitra Bot")
   - Masukkan username bot (contoh: "koperasi_swamitra_bot")
5. **Simpan token API yang diberikan**

### 2. Konfigurasi .env

Tambahkan ke `.env`:

```env
TELEGRAM_BOT_TOKEN=your_token_here
```

Ganti `your_token_here` dengan token dari BotFather.

### 3. Setup Webhook (Untuk Server Publik)

Jika server Anda bisa diakses dari internet (bukan localhost):

```bash
php artisan tinker
> app('App\Http\Controllers\TelegramController')->setupWebhook()
```

Untuk development lokal, skip step ini.

## Fitur Bot

### Command yang Tersedia

| Command | Deskripsi |
|---------|-----------|
| `/start` | Memulai bot dan menampilkan welcome message |
| `/status` | Lihat status tagihan Anda |
| `/bantuan` | Tampilkan semua command yang tersedia |

### Penggunaan

#### 1. User (Siswa) Mulai Bot

Siswa harus menambahkan bot terlebih dahulu:

```
Cari bot Anda (contoh: @koperasi_swamitra_bot)
Tap "Start" atau kirim /start
Bot akan mencatat Telegram ID siswa
```

#### 2. Melakukan Query Status Tagihan

```
User: /status
Bot: Akan menampilkan daftar tagihan yang belum dibayar
```

#### 3. Admin Mengirim Broadcast Reminder

Dari dashboard atau via curl:

```bash
# Via Dashboard
POST /telegram/broadcast

# Via Artisan Command
php artisan tinker
> app('App\Http\Controllers\TelegramController')->broadcastDebtReminder(new Request(['user_id' => 1]))
```

## Integrasi dengan Controller

### Mengirim Pesan ke User Tertentu

```php
use App\Http\Controllers\TelegramController;

$telegram = new TelegramController();
$telegram->sendMessage($chatId, "Halo, ini adalah pesan otomatis");
```

### Broadcast ke Semua yang Punya Tunggakan

```php
// Dari dalam controller atau route
POST /telegram/broadcast
```

Sistem akan otomatis:
1. Mencari semua tagihan yang overdue
2. Mengambil telegram_id dari customer
3. Mengirim reminder pesan ke setiap user

Format pesan:

```
Halo {Nama Siswa},

Hanya mengingatkan bahwa Anda memiliki tunggakan kas sebesar:
💰 Rp[jumlah]

Tipe: [tipe tagihan]
Jatuh Tempo: [tanggal]

Yuk, segera lunasi ke bendahara. Terima kasih! 🙏
```

## Contoh Implementasi

### 1. Menghubungkan Customer dengan Telegram

Ketika user baru menjalankan `/start`:

```php
// Di TelegramController@handleMessage()
TelegramUser::updateOrCreate(
    ['telegram_id' => $userId],
    [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'username' => $username,
        // customer_id bisa diisi nanti via admin panel
    ]
);
```

### 2. Admin Panel untuk Link Telegram User

Buat interface di dashboard untuk associate customer dengan telegram_id:

```blade
<!-- Di customer show page -->
<form action="{{ route('customer.linkTelegram', $customer) }}" method="POST">
    @csrf
    <input type="text" name="telegram_id" placeholder="Telegram ID atau Username">
    <button type="submit">Link Telegram</button>
</form>
```

### 3. Scheduled Broadcasting

Jika ingin otomatis mengirim reminder setiap hari:

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        app('App\Http\Controllers\TelegramController')->broadcastDebtReminder(new Request());
    })->dailyAt('08:00'); // Setiap hari jam 08:00
}
```

## Testing

### Test di Postman atau via PHP

```php
// Langsung di tinker
php artisan tinker

// Dapatkan customer dengan telegram user
$customer = Customer::whereHas('telegramUser')->first();

// Kirim test message
app('App\Http\Controllers\TelegramController')->sendMessage(
    $customer->telegramUser->telegram_id,
    "Test pesan dari sistem!"
);
```

### Test Webhook (Jika Setup)

```bash
curl -X POST http://your-domain.com/telegram/webhook \
  -H "Content-Type: application/json" \
  -d '{
    "message": {
      "from": {"id": 123456, "first_name": "Test", "username": "testuser"},
      "text": "/status",
      "chat": {"id": 123456}
    }
  }'
```

## Troubleshooting

### Bot tidak menerima pesan

**Solusi:**
1. Pastikan token di .env benar
2. Pastikan bot sudah di-start user terlebih dahulu
3. Pastikan server bisa mengakses API Telegram (internet connection)

### Error saat kirim broadcast

**Solusi:**
1. Pastikan customer sudah di-link dengan telegram_id
2. Pastikan TelegramUser record sudah ada
3. Cek log: `storage/logs/laravel.log`

### Webhook tidak bekerja

**Solusi:**
1. Server harus bisa diakses dari internet (bukan localhost)
2. HTTPS diperlukan untuk webhook
3. Cek status webhook: 
   ```bash
   curl https://api.telegram.org/botYOUR_TOKEN/getWebhookInfo
   ```

## Contoh .env untuk Development

```env
TELEGRAM_BOT_TOKEN=123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
```

## Next Steps

1. **Linking Telegram IDs**: Buat cara untuk siswa/admin menghubungkan telegram ID mereka
2. **Scheduled Messages**: Setup scheduler untuk reminder otomatis
3. **Group Chat**: Broadcast ke group chat kelas alih-alih DM individual
4. **Inline Buttons**: Tambahkan button di Telegram untuk approval pembayaran
5. **Webhook Verification**: Implementasi token verification untuk security

## Referensi

- [Telegram Bot API](https://core.telegram.org/bots/api)
- [BotFather Guide](https://core.telegram.org/bots#botfather)
- [Webhooks Documentation](https://core.telegram.org/bots/webhooks)

---

**Untuk bantuan lebih lanjut, hubungi admin sistem**
