# API Testing Guide - Postman/cURL Commands

Dokumentasi lengkap untuk testing semua API endpoints yang telah dibuat.

## 🔑 Informasi Penting

- **Base URL:** `http://localhost:8000` (local XAMPP) atau `http://<IP_SERVER>:8000` (for device)
- **Auth Test User:** username `luklu`, password `admin`
- **Content-Type:** `application/json`
- **Authentication:** Bearer Token (diperoleh dari POST /api/login)

---

## 🚀 1. Authentication Routes (No Token Required)

### POST /api/login
Buat session dan dapatkan Bearer token

**Request:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "luklu",
    "password": "admin"
  }'
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi...",
    "user": {
      "id": 1,
      "name": "Luklu Kusuma",
      "username": "luklu",
      "email": null,
      "phone": "0812345678",
      "gender": "L",
      "role": "admin",
      "created_at": "2024-01-15T10:30:00.000000Z"
    }
  }
}
```

**Save token untuk digunakan di requests berikutnya:**
```
Token: eyJ0eXAiOiJKV1QiLCJhbGciOi...
Authorization Header: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi...
```

---

## 👥 2. NASABAH / USER Routes (Authenticated)

Semua request perlu header: `Authorization: Bearer <token>`

### GET /api/nasabah
Ambil daftar semua nasabah/siswa

**Request:**
```bash
curl -X GET http://localhost:8000/api/nasabah \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi..."
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [
    {
      "id": 1,
      "username": "luklu",
      "name": "Luklu Kusuma",
      "gender": "L",
      "birth": "1990-05-15",
      "address": "Jl. Merdeka No. 123",
      "phone": "0812345678",
      "last_education": "S1",
      "role": "admin",
      "photo": null,
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    ...
  ]
}
```

### GET /api/nasabah/{id}
Ambil detail nasabah tertentu

**Request:**
```bash
curl -X GET http://localhost:8000/api/nasabah/1 \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi..."
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": {
    "id": 1,
    "username": "luklu",
    "name": "Luklu Kusuma",
    ...
  }
}
```

### POST /api/nasabah
Buat nasabah baru

**Request:**
```bash
curl -X POST http://localhost:8000/api/nasabah \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi..." \
  -H "Content-Type: application/json" \
  -d '{
    "username": "newuser",
    "name": "New User Name",
    "password": "password123",
    "gender": "L",
    "birth": "1995-03-20",
    "address": "Jl. Ahmad Yani No. 45",
    "phone": "0898765432",
    "last_education": "S1",
    "role": "member"
  }'
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Data berhasil dibuat",
  "data": {
    "id": 7,
    "username": "newuser",
    "name": "New User Name",
    ...
  }
}
```

### PUT /api/nasabah/{id}
Update nasabah

**Request:**
```bash
curl -X PUT http://localhost:8000/api/nasabah/1 \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi..." \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Name",
    "phone": "0812345678",
    "address": "Jl. Baru No. 100"
  }'
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Data berhasil diperbarui",
  "data": { ... }
}
```

### DELETE /api/nasabah/{id}
Hapus nasabah

**Request:**
```bash
curl -X DELETE http://localhost:8000/api/nasabah/1 \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi..."
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Data berhasil dihapus",
  "data": null
}
```

---

## 💰 3. BILL / TAGIHAN Routes

### GET /api/tagihan
Ambil semua tagihan dengan detail customer

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [
    {
      "id": 1,
      "customer_id": 1,
      "customer": {
        "id": 1,
        "name": "Luklu Kusuma",
        "phone": "0812345678",
        "address": "Jl. Merdeka No. 123"
      },
      "amount": 500000,
      "due_date": "2024-02-15",
      "paid_date": null,
      "paid_amount": null,
      "bill_type": "monthly",
      "notes": "Tagihan bulanan Januari",
      "status": "pending",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    ...
  ]
}
```

### GET /api/tagihan/{id}
Ambil detail tagihan tertentu

### POST /api/tagihan
Buat tagihan baru

**Request:**
```bash
curl -X POST http://localhost:8000/api/tagihan \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi..." \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id": 1,
    "amount": 500000,
    "due_date": "2024-02-15",
    "bill_type": "monthly",
    "notes": "Tagihan bulanan Januari"
  }'
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Data berhasil dibuat",
  "data": { ... }
}
```

### PUT /api/tagihan/{id}
Update tagihan

**Request:**
```bash
curl -X PUT http://localhost:8000/api/tagihan/1 \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi..." \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 600000,
    "status": "partial",
    "paid_amount": 300000,
    "paid_date": "2024-02-01"
  }'
```

### DELETE /api/tagihan/{id}
Hapus tagihan

---

## 📊 4. EXPENSE / PENGELUARAN Routes

### GET /api/pengeluaran
Ambil semua pengeluaran dengan category dan user creator

**Response:**
```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [
    {
      "id": 1,
      "expense_category_id": 1,
      "category": {
        "id": 1,
        "name": "Biaya Operasional",
        "description": "Pengeluaran operasional harian"
      },
      "description": "Beli alat kantor",
      "amount": 250000,
      "expense_date": "2024-01-20",
      "created_by": 1,
      "user": {
        "id": 1,
        "name": "Luklu Kusuma",
        "username": "luklu",
        "role": "admin"
      },
      "notes": "Printer dan kertas",
      "created_at": "2024-01-20T10:30:00.000000Z",
      "updated_at": "2024-01-20T10:30:00.000000Z"
    },
    ...
  ]
}
```

### GET /api/pengeluaran/{id}
Ambil detail pengeluaran

### POST /api/pengeluaran
Catat pengeluaran baru

**Request:**
```bash
curl -X POST http://localhost:8000/api/pengeluaran \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi..." \
  -H "Content-Type: application/json" \
  -d '{
    "expense_category_id": 1,
    "description": "Beli alat kantor",
    "amount": 250000,
    "expense_date": "2024-01-20",
    "notes": "Printer dan kertas"
  }'
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Data berhasil dibuat",
  "data": { ... }
}
```

### PUT /api/pengeluaran/{id}
Update pengeluaran

### DELETE /api/pengeluaran/{id}
Hapus pengeluaran

---

## 🏷️ 5. EXPENSE CATEGORY / KATEGORI Routes

### GET /api/expense-category
Ambil semua kategori dengan jumlah pengeluaran

**Response:**
```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [
    {
      "id": 1,
      "name": "Biaya Operasional",
      "description": "Pengeluaran operasional harian",
      "expenses_count": 5,
      "created_at": "2024-01-01T10:30:00.000000Z",
      "updated_at": "2024-01-01T10:30:00.000000Z"
    },
    {
      "id": 2,
      "name": "Biaya Perawatan",
      "description": "Perawatan gedung dan aset",
      "expenses_count": 3,
      "created_at": "2024-01-01T10:30:00.000000Z",
      "updated_at": "2024-01-01T10:30:00.000000Z"
    }
  ]
}
```

### GET /api/expense-category/{id}
Ambil detail kategori

### POST /api/expense-category
Buat kategori baru

**Request:**
```bash
curl -X POST http://localhost:8000/api/expense-category \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi..." \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Biaya Listrik",
    "description": "Biaya pemeliharaan listrik dan alat elektronik"
  }'
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Data berhasil dibuat",
  "data": { ... }
}
```

### PUT /api/expense-category/{id}
Update kategori

### DELETE /api/expense-category/{id}
Hapus kategori

⚠️ **Catatan:** Akan return 422 jika kategori masih memiliki pengeluaran

**Error Response (422):**
```json
{
  "success": false,
  "message": "Kategori tidak bisa dihapus karena masih ada pengeluaran",
  "data": null
}
```

---

## 📋 6. TRANSPARENCY REPORT Routes

### GET /api/transparency-report
Ambil semua laporan transparansi

**Response:**
```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [
    {
      "id": 1,
      "title": "Laporan Q1 2024",
      "description": "Laporan transparansi kuartal pertama tahun 2024",
      "start_date": "2024-01-01",
      "end_date": "2024-03-31",
      "is_active": true,
      "access_token": "abc123def456...",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    }
  ]
}
```

### GET /api/transparency-report/{id}
Ambil detail laporan

### POST /api/transparency-report
Buat laporan baru

**Request:**
```bash
curl -X POST http://localhost:8000/api/transparency-report \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi..." \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Laporan Q1 2024",
    "description": "Laporan transparansi kuartal pertama tahun 2024",
    "start_date": "2024-01-01",
    "end_date": "2024-03-31",
    "is_active": true
  }'
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Data berhasil dibuat",
  "data": { ... }
}
```

### PUT /api/transparency-report/{id}
Update laporan

### DELETE /api/transparency-report/{id}
Hapus laporan

---

## ⚠️ Error Responses

### 401 Unauthorized (Token Expired/Invalid)
```json
{
  "message": "Unauthenticated."
}
```

**Fix:** Login ulang untuk mendapatkan token baru

### 404 Not Found
```json
{
  "success": false,
  "message": "Data tidak ditemukan",
  "data": null
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "Validasi gagal",
  "data": {
    "customer_id": ["The customer_id field is required."],
    "amount": ["The amount must be a number."]
  }
}
```

### 500 Server Error
```json
{
  "success": false,
  "message": "Error: [detailed error message]",
  "data": null
}
```

**Debug:** Check Laravel logs di `storage/logs/laravel.log`

---

## 🧪 Testing Workflow

### 1. Login dulu
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"luklu","password":"admin"}'
```
Copy token dari response

### 2. Test User/Nasabah endpoints
```bash
curl -X GET http://localhost:8000/api/nasabah \
  -H "Authorization: Bearer <PASTE_TOKEN_HERE>"
```

### 3. Test Bill endpoints
```bash
curl -X GET http://localhost:8000/api/tagihan \
  -H "Authorization: Bearer <PASTE_TOKEN_HERE>"
```

### 4. Test Expense endpoints
```bash
curl -X GET http://localhost:8000/api/pengeluaran \
  -H "Authorization: Bearer <PASTE_TOKEN_HERE>"
```

### 5. Test Category endpoints
```bash
curl -X GET http://localhost:8000/api/expense-category \
  -H "Authorization: Bearer <PASTE_TOKEN_HERE>"
```

### 6. Test Report endpoints
```bash
curl -X GET http://localhost:8000/api/transparency-report \
  -H "Authorization: Bearer <PASTE_TOKEN_HERE>"
```

---

## 💡 Tips untuk Testing dengan Postman

1. **Set Environment Variable untuk Token:**
   - Pada POST login, set test script: 
   ```javascript
   var jsonData = pm.response.json();
   pm.environment.set("bearer_token", jsonData.data.token);
   ```

2. **Gunakan Variable di Headers:**
   - Authorization: `Bearer {{bearer_token}}`

3. **Quick Test Folder:**
   - Buat folder: Login → Get Nasabah → Create Bill → Get Bills → Update Bill → Delete Bill

4. **Monitor Logs:**
   - Terminal: `tail -f storage/logs/laravel.log`
   - Real-time log monitoring saat testing

---

## ✅ Checklist Testing

- [ ] POST /api/login - Verify token diterima
- [ ] GET /api/nasabah - Verify 6 records ditampilkan
- [ ] POST /api/nasabah - Buat user baru
- [ ] PUT /api/nasabah/{id} - Update user
- [ ] DELETE /api/nasabah/{id} - Hapus user
- [ ] GET /api/tagihan - Verify bills menampilkan customer
- [ ] POST /api/tagihan - Create bill baru
- [ ] PUT /api/tagihan/{id} - Update bill
- [ ] DELETE /api/tagihan/{id} - Hapus bill
- [ ] GET /api/pengeluaran - Verify expenses menampilkan category & user
- [ ] POST /api/pengeluaran - Create expense baru
- [ ] PUT /api/pengeluaran/{id} - Update expense
- [ ] DELETE /api/pengeluaran/{id} - Hapus expense
- [ ] GET /api/expense-category - Verify categories dengan count
- [ ] POST /api/expense-category - Create category baru
- [ ] DELETE /api/expense-category/{id} (with expenses) - Verify error 422
- [ ] DELETE /api/expense-category/{id} (no expenses) - Hapus category
- [ ] GET /api/transparency-report - Verify reports
- [ ] POST /api/transparency-report - Create report
- [ ] PUT /api/transparency-report/{id} - Update report
- [ ] DELETE /api/transparency-report/{id} - Hapus report

---

Untuk bantuan lebih lanjut, silakan check dokumentasi API atau contact support.
