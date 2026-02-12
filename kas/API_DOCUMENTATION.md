# API Documentation - Fitur Baru Diferensiasa

## Base URL
```
http://localhost/
```

## Authentication
Semua endpoint (kecuali public ones) memerlukan login via Laravel session.

---

## 1. DASHBOARD

### GET Dashboard
```
GET /dashboard
```

**Response:**
```json
{
  "balance": 5000000,
  "totalIncome": 10000000,
  "totalExpense": 5000000,
  "monthlyIncome": 2000000,
  "monthlyExpense": 1000000,
  "totalDebt": 500000,
  "overdueDebt": 200000,
  "overdueCount": 5,
  "cashFlowData": [...],
  "topDebtors": [...],
  "expenseByCategory": {...},
  "recentBills": [...],
  "recentExpenses": [...]
}
```

---

## 2. BILLS (TAGIHAN)

### GET List Tagihan
```
GET /tagihan
```

**Query Parameters:**
- `page` - Halaman (default: 1)

**Response:** Paginated list of bills

### POST Create Tagihan
```
POST /tagihan
Content-Type: application/json

{
  "customer_id": 1,
  "bill_type": "Kas Bulanan",
  "amount": 50000,
  "due_date": "2024-02-29",
  "notes": "Tagihan kas bulan Februari"
}
```

**Response:**
```json
{
  "id": 1,
  "customer_id": 1,
  "bill_type": "Kas Bulanan",
  "amount": 50000,
  "paid_amount": 0,
  "due_date": "2024-02-29",
  "paid_date": null,
  "notes": "...",
  "created_at": "2024-02-04T10:00:00",
  "updated_at": "2024-02-04T10:00:00"
}
```

### GET Bill Detail
```
GET /tagihan/{id}
```

### PUT Update Bill
```
PUT /tagihan/{id}
Content-Type: application/json

{
  "customer_id": 1,
  "bill_type": "Kas Bulanan",
  "amount": 50000,
  "due_date": "2024-02-29",
  "notes": "..."
}
```

### POST Mark Bill as Paid
```
POST /tagihan/mark-paid/{id}
Content-Type: application/json

{
  "paid_amount": 50000
}
```

### DELETE Bill
```
DELETE /tagihan/{id}
```

### POST Generate Bulk Bills
```
POST /tagihan/generate-routine
Content-Type: application/json

{
  "amount": 50000,
  "bill_type": "Kas Bulanan",
  "due_date": "2024-02-29"
}
```

**Response:** Success message with count of created bills

### GET Bill Report
```
GET /tagihan/laporan
```

---

## 3. EXPENSES (PENGELUARAN)

### GET List Pengeluaran
```
GET /pengeluaran
```

**Query Parameters:**
- `page` - Halaman (default: 1)

**Response:** Paginated list of expenses

### POST Create Pengeluaran
```
POST /pengeluaran
Content-Type: multipart/form-data

{
  "expense_category_id": 1,
  "description": "Fotokopi untuk UTS",
  "amount": 250000,
  "expense_date": "2024-02-04",
  "notes": "Fotokopi 500 lembar",
  "receipts": [file, file, ...]
}
```

**Required**: Minimal 1 file receipt (PDF/JPG/PNG, max 5MB)

**Response:**
```json
{
  "id": 1,
  "expense_category_id": 1,
  "created_by": 1,
  "description": "Fotokopi untuk UTS",
  "amount": 250000,
  "expense_date": "2024-02-04",
  "notes": "...",
  "created_at": "2024-02-04T10:00:00",
  "updated_at": "2024-02-04T10:00:00",
  "receipts": [...]
}
```

### GET Expense Detail
```
GET /pengeluaran/{id}
```

### PUT Update Expense
```
PUT /pengeluaran/{id}
Content-Type: multipart/form-data

{
  "expense_category_id": 1,
  "description": "...",
  "amount": 250000,
  "expense_date": "2024-02-04",
  "notes": "...",
  "receipts": [optional: new files]
}
```

### DELETE Expense
```
DELETE /pengeluaran/{id}
```

### DELETE Expense Receipt
```
DELETE /pengeluaran-bukti/{receipt_id}
```

### GET Expense Report
```
GET /pengeluaran/laporan?start_date=2024-02-01&end_date=2024-02-29
```

---

## 4. EXPENSE CATEGORIES

### GET List Kategori
```
GET /kategori-pengeluaran
```

### POST Create Kategori
```
POST /kategori-pengeluaran
Content-Type: application/json

{
  "name": "Fotokopi",
  "description": "Biaya fotokopi untuk keperluan sekolah"
}
```

### PUT Update Kategori
```
PUT /kategori-pengeluaran/{id}
Content-Type: application/json

{
  "name": "Fotokopi",
  "description": "..."
}
```

### DELETE Kategori
```
DELETE /kategori-pengeluaran/{id}
```

---

## 5. TRANSPARENCY REPORTS

### GET List Laporan Transparansi
```
GET /laporan-transparansi
```

### POST Create Laporan
```
POST /laporan-transparansi
Content-Type: application/json

{
  "title": "Laporan Transparansi Kas Januari 2024",
  "description": "Laporan mutasi kas bulan Januari",
  "start_date": "2024-01-01",
  "end_date": "2024-01-31"
}
```

**Response:**
```json
{
  "id": 1,
  "title": "...",
  "description": "...",
  "start_date": "2024-01-01",
  "end_date": "2024-01-31",
  "access_token": "abc123def456...",
  "is_active": true,
  "created_at": "2024-02-04T10:00:00",
  "updated_at": "2024-02-04T10:00:00"
}
```

### PUT Update Laporan
```
PUT /laporan-transparansi/{id}
Content-Type: application/json

{
  "title": "...",
  "description": "...",
  "start_date": "...",
  "end_date": "...",
  "is_active": true
}
```

### DELETE Laporan
```
DELETE /laporan-transparansi/{id}
```

### GET Laporan Publik (No Auth Required)
```
GET /laporan-publik/{access_token}
```

**Response:** HTML page dengan detail laporan

### GET Export PDF
```
GET /laporan-transparansi/export/pdf?start_date=2024-02-01&end_date=2024-02-29
```

**Response:** PDF file download

### GET Export Excel
```
GET /laporan-transparansi/export/excel?start_date=2024-02-01&end_date=2024-02-29
```

**Response:** Excel file download

---

## 6. TELEGRAM BOT

### POST Webhook (No Auth Required)
```
POST /telegram/webhook
Content-Type: application/json

{
  "update_id": 123456,
  "message": {
    "message_id": 1,
    "from": {
      "id": 123456789,
      "is_bot": false,
      "first_name": "John",
      "last_name": "Doe",
      "username": "johndoe"
    },
    "chat": {
      "id": 123456789,
      "type": "private"
    },
    "date": 1704225600,
    "text": "/status"
  }
}
```

**Response:**
```json
{
  "ok": true
}
```

### POST Broadcast Debt Reminder
```
POST /telegram/broadcast
```

**Response:** Success message with count of messages sent

---

## Error Responses

### 400 Bad Request
```json
{
  "message": "Validation error",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "message": "This action is unauthorized"
}
```

### 404 Not Found
```json
{
  "message": "Resource not found"
}
```

### 422 Unprocessable Entity
```json
{
  "message": "The given data was invalid",
  "errors": {...}
}
```

### 500 Internal Server Error
```json
{
  "message": "Server error",
  "error": "..."
}
```

---

## Pagination Format

```json
{
  "data": [...],
  "current_page": 1,
  "from": 1,
  "last_page": 5,
  "per_page": 15,
  "to": 15,
  "total": 75,
  "links": {...}
}
```

```php
// Di routes/web.php
Route::middleware('throttle:60,1')->group(function () {
    // Routes here
});
```