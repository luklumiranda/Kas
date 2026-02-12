@echo off
REM Setup Script untuk Fitur Baru Diferensiasa (Windows)

echo ================================
echo Setup Fitur Baru - Diferensiasa
echo ================================

REM Step 1: Install Composer Dependencies
echo.
echo [1/5] Installing Composer Dependencies...
call composer require maatwebsite/excel

REM Step 2: Run Migrations
echo.
echo [2/5] Running Migrations...
call php artisan migrate

REM Step 3: Run Seeders (Expense Categories)
echo.
echo [3/5] Seeding Expense Categories...
call php artisan db:seed --class=ExpenseCategorySeeder

REM Step 4: Create Storage Link
echo.
echo [4/5] Creating Storage Symlink...
call php artisan storage:link

REM Step 5: Clear Cache
echo.
echo [5/5] Clearing Cache...
call php artisan cache:clear
call php artisan config:clear
call php artisan view:clear

echo.
echo ================================
echo ^✓ Setup Selesai!
echo ================================
echo.

pause
