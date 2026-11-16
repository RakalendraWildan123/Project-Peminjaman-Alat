@echo off
title Menjalankan Aplikasi AlatKu (Docker)
color 0b
echo =======================================================================
echo          SISTEM INFORMASI PEMINJAMAN ALAT (ALATKU) - DOCKER
echo =======================================================================
echo.

if not exist "backend\.env" (
    echo [1/4] Menyiapkan file .env dari .env.example...
    copy "backend\.env.example" "backend\.env" >nul
) else (
    echo [1/4] File .env sudah ada.
)

echo [2/4] Menjalankan Container Docker...
docker compose up -d

echo [3/4] Menghubungkan direktori penyimpanan gambar (Storage Link)...
timeout /t 5 /nobreak >nul
docker compose exec -T app-laravel php artisan storage:link --force >nul 2>&1

echo [4/4] Membuka browser Google Chrome...
start http://localhost:8000

echo.
echo =======================================================================
echo   APLIKASI TELAH BERJALAN DENGAN SUKSES!
echo.
echo   - Web Aplikasi : http://localhost:8000
echo   - phpMyAdmin   : http://localhost:8081
echo.
echo   AKUN DEMO:
echo   - Admin    : admin@gmail.com   ^| password123
echo   - Petugas  : petugas@gmail.com ^| password123
echo   - Peminjam : rian@gmail.com    ^| password123
echo =======================================================================
echo Tekan tombol apa saja untuk menutup jendela ini...
pause >nul
