@echo off
REM Quick Setup Script untuk Windows
REM Jalankan file ini untuk setup otomatis

setlocal enabledelayedexpansion

cls
echo.
echo ======================================
echo   API Setup ^& Connection Test
echo   (Mobile App Database Connection)
echo ======================================
echo.

REM Check if artisan exists
if not exist "artisan" (
    color 4F
    echo [ERROR] artisan file tidak ditemukan
    echo Pastikan Anda menjalankan script ini dari folder Laravel
    pause
    exit /b 1
)

REM Check PHP
php --version >nul 2>&1
if errorlevel 1 (
    color 4F
    echo [ERROR] PHP tidak terinstall atau tidak di PATH
    pause
    exit /b 1
)

echo [1/4] Checking database connection...
php artisan tinker --execute="DB::connection()->getPDO();" 2>nul
if errorlevel 1 (
    color 4F
    echo [FAIL] Database connection gagal
    echo Periksa file .env dan MySQL status
    pause
    exit /b 1
) else (
    color 2F
    echo [OK] Database terhubung
    color 07
)

echo.
echo [2/4] Checking users...
php artisan tinker --execute="echo 'Found ' . \App\Models\User::count() . ' users';"

echo.
echo [3/4] Verifying API routes...
php artisan route:list --path=api | findstr "^GET\|^POST\|^PUT\|^DELETE" 
echo.

echo [4/4] Laravel Development Server...
echo.
color 2F
echo  ===================================
echo  🚀 Server starting...
echo  API Base URL: http://localhost:8000/api
echo.
echo  IMPORTANT NOTES:
echo  - Untuk mobile app di device lain: http://YOUR_IP:8000/api
echo  - Cari IP address dengan: ipconfig
echo  - Cari IPv4 Address yang dimulai dengan 192.168 atau 10.0
echo.
echo  Tekan Ctrl+C untuk stop server
echo  ===================================
color 07
echo.

php artisan serve --host=0.0.0.0 --port=8000

if errorlevel 1 (
    echo.
    color 4F
    echo [ERROR] Server gagal dijalankan
    color 07
    pause
)
