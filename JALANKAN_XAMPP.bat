@echo off
title Menjalankan Aplikasi AlatKu (XAMPP)
color 0a
echo =======================================================================
echo          SISTEM INFORMASI PEMINJAMAN ALAT (ALATKU) - XAMPP
echo =======================================================================
echo.
echo Pastikan Apache dan MySQL di XAMPP Control Panel sudah dalam status START!
echo.

if not exist "backend\.env" (
    echo [1/5] Menyiapkan file .env...
    copy "backend\.env.example" "backend\.env" >nul
) else (
    echo [1/5] File .env sudah ada.
)

cd backend

if not exist "vendor\autoload.php" (
    echo [2/5] Mengunduh dependensi library (Composer Install)...
    call composer install --no-interaction
) else (
    echo [2/5] Dependensi Composer sudah terpasang.
)

echo [3/5] Menghubungkan penyimpanan gambar (Storage Link)...
php artisan storage:link --force >nul 2>&1

echo [4/5] Memastikan database siap...
php artisan migrate --force >nul 2>&1

echo [5/5] Membuka browser Google Chrome...
start http://localhost:8000

echo.
echo =======================================================================
echo   SERVER LARAVEL SEDANG BERJALAN! (JANGAN TUTUP JENDELA INI)
echo.
echo   - Web Aplikasi : http://localhost:8000
echo.
echo   AKUN DEMO:
echo   - Admin    : admin@gmail.com   ^| password123
echo   - Petugas  : petugas@gmail.com ^| password123
echo   - Peminjam : rian@gmail.com    ^| password123
echo =======================================================================
echo.
php artisan serve --port=8000
