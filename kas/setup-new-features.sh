#!/bin/bash
# Setup Script untuk Fitur Baru Diferensiasa

echo "================================"
echo "Setup Fitur Baru - Diferensiasa"
echo "================================"

# Step 1: Install Composer Dependencies
echo ""
echo "[1/5] Installing Composer Dependencies..."
composer require maatwebsite/excel

# Step 2: Run Migrations
echo ""
echo "[2/5] Running Migrations..."
php artisan migrate

# Step 3: Run Seeders (Expense Categories)
echo ""
echo "[3/5] Seeding Expense Categories..."
php artisan db:seed --class=ExpenseCategorySeeder

# Step 4: Create Storage Link
echo ""
echo "[4/5] Creating Storage Symlink..."
php artisan storage:link

# Step 5: Clear Cache
echo ""
echo "[5/5] Clearing Cache..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo ""
echo "================================"
echo "✓ Setup Selesai!"
echo "================================"
echo ""
echo "Langkah selanjutnya:"
echo "1. Akses dashboard di: http://localhost/dashboard"
echo ""
