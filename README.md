Diferensiasa APP

💰 Kas - Sistem Manajemen Keuangan (Laravel dan Kotlin)
Kas adalah aplikasi web manajemen keuangan yang dirancang khusus untuk transparansi pengelolaan dana kas (seperti kas kelas atau organisasi). Aplikasi ini berfokus pada kemudahan pencatatan transaksi dan penyajian data saldo yang akurat untuk mencegah kesalahan input manual.

🌟 Fitur Spesifik
1. Dashboard Analitik
Visualisasi Saldo: Menampilkan kartu ringkasan untuk "Total Pemasukan", "Total Pengeluaran", dan "Saldo Akhir" secara otomatis.

Log Transaksi Terakhir: Menampilkan daftar aktivitas keuangan terbaru langsung di halaman utama.

2. Manajemen Arus Kas (CRUD)
Pencatatan Presisi: Setiap transaksi mencatat nominal, kategori (Masuk/Keluar), deskripsi/keterangan, serta stempel waktu (timestamp).

Flowchart

<img width="687" height="1294" alt="image" src="https://github.com/user-attachments/assets/9657a5fb-4ca4-47b6-b85f-6686e611e5e5" />

Use Case Diagram

<img width="264" height="606" alt="image" src="https://github.com/user-attachments/assets/edc9c8cc-4086-4ce5-bb9b-1578f0f75cd3" />

Validasi Data: Sistem memastikan nominal yang diinput tidak bernilai negatif dan form tidak kosong sebelum disimpan ke database.

3. Keamanan & Autentikasi
Protected Routes: Menggunakan Middleware Laravel untuk memastikan hanya pengguna terautentikasi (Admin/Bendahara) yang dapat menambah atau mengubah data.

Bcrypt Hashing: Keamanan kata sandi pengguna dienkripsi menggunakan algoritma hashing standar industri.

CSRF Protection: Melindungi aplikasi dari serangan Cross-Site Request Forgery pada setiap form input.

🛠️ Detail Teknis (Technical Stack)
Framework: Laravel 10.x / 11.x

Database: MySQL 

Berikut adalah gambaran bagaimana data transaksi dan pengguna saling terhubung dalam sistem ini:

Catatan: Diagram ini menunjukkan relasi antara tabel users (pengelola) dengan tabel transactions (pemasukan/pengeluaran).

<img width="923" height="702" alt="image" src="https://github.com/user-attachments/assets/589865ea-6e07-4897-b263-b0df2d38a0c8" />

Engine: PHP 8.2+

Frontend: Blade Templating Engine dengan integrasi CSS/Bootstrap untuk antarmuka yang responsif.

Halaman Login

<img width="902" height="449" alt="image" src="https://github.com/user-attachments/assets/2b8b2930-f394-4a6c-8465-318244c3e409" />

<img width="648" height="1454" alt="image" src="https://github.com/user-attachments/assets/405b4e7f-97af-4be1-97b8-6006c1ec156f" />

Halaman Dashboard

<img width="905" height="425" alt="image" src="https://github.com/user-attachments/assets/3544939e-a0c4-44e3-bb84-63591de5acb9" />

<img width="650" height="1454" alt="image" src="https://github.com/user-attachments/assets/e8d4e90d-625c-4926-a348-e5640bb8054b" />

🏗️ Struktur Arsitektur (Alur Kerja)
Aplikasi ini mengikuti pola MVC (Model-View-Controller):

Models (app/Models/): Mengelola logika data transaksi dan relasi user.

Controllers (app/Http/Controllers/): Menangani logika bisnis, seperti menghitung sisa saldo dari selisih total masuk dan keluar.

Migrations (database/migrations/): Definisi struktur tabel transactions yang mencakup kolom type (enum: masuk/keluar) dan amount (decimal).

Routes (routes/web.php): Mengatur pemetaan URL yang bersih dan SEO-friendly.

🚀 Langkah Instalasi & Deployment
Kloning Repositori

Bash
git clone https://github.com/luklumiranda/Kas.git
cd Kas
Instalasi Dependensi

Bash
composer install
npm install && npm run build
Konfigurasi Environment

Bash
cp .env.example .env
php artisan key:generate
Sesuaikan DB_DATABASE, DB_USERNAME, dan DB_PASSWORD di file .env.

Migrasi & Seeding

Bash
php artisan migrate
Menjalankan Aplikasi

Bash
php artisan serve
📝 Catatan Pengembangan
Proyek ini dikembangkan dengan prinsip Clean Code untuk memudahkan pengembangan fitur di masa depan, seperti fitur ekspor laporan ke PDF/Excel atau grafik riwayat bulanan.

Dikembangkan oleh Luklu Miranda 🚀
