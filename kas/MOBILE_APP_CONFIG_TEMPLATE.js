/**
 * MOBILE APP API CONFIG TEMPLATE
 * 
 * Copy dan sesuaikan config ini di aplikasi mobile Anda
 */

// ==========================================
// REACT NATIVE CONFIG
// ==========================================

// File: config/api.config.js

export const API_CONFIG = {
  // Ganti IP address laptop Anda di sini
  // Lihat: Settings > Network atau run: ipconfig (Windows)
  BASE_URL: 'http://192.168.1.100:8000/api',
  
  // Timeout untuk request (milliseconds)
  TIMEOUT: 10000,
  
  // Local storage key untuk token
  TOKEN_KEY: 'auth_token',
  
  // User key untuk stored user data
  USER_KEY: 'user_data',
  
  // Endpoints
  ENDPOINTS: {
    // Auth
    LOGIN: '/login',
    LOGOUT: '/logout',
    
    // Nasabah
    CUSTOMERS: '/nasabah',
    CUSTOMER_DETAIL: '/nasabah/:id',
    CUSTOMER_LOANS: '/nasabah/:id/pinjaman',
    CUSTOMER_BALANCE: '/nasabah/:id/saldo',
    CUSTOMER_DEPOSITS: '/nasabah/:id/simpanan',
    CUSTOMER_BILLS: '/nasabah/:id/tagihan',
    
    // Pinjaman
    LOANS: '/pinjaman',
    LOAN_DETAIL: '/pinjaman/:id',
    LOAN_INSTALMENT: '/pinjaman/:id/angsuran',
    LOAN_STATUS: '/pinjaman/:id/status',
    
    // Simpanan
    DEPOSITS: '/simpanan',
    DEPOSIT_DETAIL: '/simpanan/:id',
    DEPOSIT_HISTORY: '/simpanan/:id/riwayat',
    
    // Tagihan
    BILLS: '/tagihan',
    BILL_DETAIL: '/tagihan/:id',
    BILL_PAY: '/tagihan/:id/bayar',
    BILL_STATUS: '/tagihan/:id/status',
    
    // Pengeluaran
    EXPENSES: '/pengeluaran',
    EXPENSES_CATEGORIES: '/pengeluaran/kategori/list',
    
    // Dashboard
    DASHBOARD_SUMMARY: '/dashboard/summary',
  },
  
  // HTTP Headers
  HEADERS: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
};

// ==========================================
// FLUTTER CONFIG
// ==========================================

// File: lib/config/api_config.dart

class ApiConfig {
  // Ganti IP address laptop Anda di sini
  static const String BASE_URL = 'http://192.168.1.100:8000/api';
  
  static const int TIMEOUT_SECONDS = 10;
  
  static const String TOKEN_KEY = 'auth_token';
  static const String USER_KEY = 'user_data';
  
  // Endpoints
  static const String LOGIN = '/login';
  static const String LOGOUT = '/logout';
  
  // Customers
  static const String CUSTOMERS = '/nasabah';
  static String customerDetail(int id) => '/nasabah/$id';
  static String customerLoans(int id) => '/nasabah/$id/pinjaman';
  static String customerBalance(int id) => '/nasabah/$id/saldo';
  
  // Loans
  static const String LOANS = '/pinjaman';
  static String loanDetail(int id) => '/pinjaman/$id';
  static String loanInstalment(int id) => '/pinjaman/$id/angsuran';
  
  // Deposits
  static const String DEPOSITS = '/simpanan';
  static String depositHistory(int id) => '/simpanan/$id/riwayat';
  
  // Bills
  static const String BILLS = '/tagihan';
  static String billPay(int id) => '/tagihan/$id/bayar';
  
  // Dashboard
  static const String DASHBOARD_SUMMARY = '/dashboard/summary';
  
  // Headers
  static Map<String, String> getHeaders({String? token}) {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    
    if (token != null) {
      headers['Authorization'] = 'Bearer $token';
    }
    
    return headers;
  }
}

// ==========================================
// HOW TO FIND YOUR IP ADDRESS
// ==========================================

/*
WINDOWS:
1. Buka Command Prompt
2. Ketik: ipconfig
3. Cari "IPv4 Address" yang dimulai dengan 192.168 atau 10.0
4. Contoh output: 192.168.1.100

MAC:
1. Buka Terminal
2. Ketik: ifconfig
3. Cari "inet" di bawah "en0" atau "en1"
4. Contoh: inet 192.168.1.100

LINUX:
1. Buka Terminal
2. Ketik: hostname -I
3. Lihat IP address yang muncul
4. Contoh: 192.168.1.100
*/

// ==========================================
// ENVIRONMENT VARIABLES (OPTIONAL)
// ==========================================

/*
Anda bisa juga menggunakan .env file:

React Native (.env):
REACT_APP_API_BASE_URL=http://192.168.1.100:8000/api

Gunakan dengan:
const API_BASE_URL = process.env.REACT_APP_API_BASE_URL;

Flutter (pubspec.yaml - environment variables):
Gunakan flutter_dotenv package:
flutter pub add flutter_dotenv

File: .env
API_BASE_URL=http://192.168.1.100:8000/api

Gunakan dengan:
import 'package:flutter_dotenv/flutter_dotenv.dart';
String baseUrl = dotenv.env['API_BASE_URL'] ?? '';
*/

// ==========================================
// TESTING CONFIGURATION
// ==========================================

/*
Untuk testing, gunakan credentials ini:
Email: admin@example.com
Password: password123

Jika belum ada user, buat dengan:
php artisan tinker

Kemudian:
User::create([
  'name' => 'Admin',
  'email' => 'admin@example.com',
  'password' => Hash::make('password123')
]);
*/

// ==========================================
// SECURITY NOTES
// ==========================================

/*
PRODUCTION:
- Gunakan HTTPS, bukan HTTP
- Jangan hardcode IP address, gunakan domain
- Implement certificate pinning
- Gunakan secure token storage
- Implement token refresh mechanism
- Add request signing/encryption jika perlu
*/

export default API_CONFIG;
