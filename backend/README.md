# Panduan Mengatasi Error dan Troubleshooting Proyek

Buku panduan praktis ini berisi solusi lengkap langkah demi langkah untuk mengatasi berbagai kemungkinan error yang sering terjadi saat menginstal, menyalakan Docker, menjalankan Composer, migrasi database, dan mengoperasikan aplikasi ini.

---

## Daftar Isi Panduan
1. Masalah Docker dan Docker Compose
2. Masalah Composer dan Dependensi PHP
3. Masalah Database MySQL dan Migrasi
4. Masalah Port Bentrok (Port Already in Use)
5. Masalah Kunci Aplikasi dan File Konfigurasi (.env)
6. Masalah Gambar Upload Tidak Muncul (Storage Link)
7. Masalah Izin Folder (Permission Denied)
8. Masalah Cache yang Menumpuk
9. Perintah Pemulihan Cepat (One-Line Fix)

---

## 1. Masalah Docker dan Docker Compose

### Kasus A: Muncul Pesan "error during connect ... docker client must be run with elevated privileges" atau "Cannot connect to the Docker daemon"
* **Penyebab:** Aplikasi Docker Desktop di komputer Anda belum dinyalakan atau proses mesin Docker belum selesai berjalan dengan sempurna.
* **Solusi:**
  1. Buka aplikasi **Docker Desktop** dari menu Start Windows Anda.
  2. Tunggu beberapa saat sampai ikon Docker di pojok kiri bawah jendela Docker Desktop berubah warna menjadi hijau dengan tulisan **Engine running**.
  3. Buka kembali terminal PowerShell atau CMD, lalu jalankan ulang perintah:
     ```bash
     docker compose up -d
     ```

### Kasus B: Muncul Pesan "Conflict. The container name is already in use"
* **Penyebab:** Container dengan nama yang sama (misalnya `laravel-UJICOBA` atau `MySQL-UJICOBA`) masih tersisa atau sedang berjalan dari sesi sebelumnya.
* **Solusi:**
  Hentikan dan bersihkan container lama dengan perintah berikut di terminal:
  ```bash
  docker compose down
  ```
  Atau jika masih membandel, paksa hapus container lama tersebut dengan perintah:
  ```bash
  docker rm -f laravel-UJICOBA MySQL-UJICOBA PhpMyadmin-UJICOBA
  ```
  Setelah itu, nyalakan kembali dengan perintah:
  ```bash
  docker compose up -d
  ```

---

## 2. Masalah Composer dan Dependensi PHP

### Kasus A: Muncul Pesan "vendor/autoload.php was not found" atau "Failed to open stream: No such file or directory"
* **Penyebab:** Folder pustaka `vendor` belum terpasang atau proses unduh paket belum selesai.
* **Solusi:**
  Jika Anda menggunakan Docker, jalankan perintah instalasi Composer langsung di dalam container:
  ```bash
  docker exec -it laravel-UJICOBA composer install
  ```
  Jika Anda menjalankan PHP secara lokal di komputer (tanpa Docker), masuk ke folder backend terlebih dahulu lalu jalankan:
  ```bash
  cd backend
  composer install
  ```

### Kasus B: Muncul Pesan "Your requirements could not be resolved for your PHP version" atau "The requested PHP extension is missing"
* **Penyebab:** Komputer lokal Anda memiliki versi PHP yang lebih rendah atau ada ekstensi PHP yang belum diaktifkan (misalnya ekstensi `pdo_mysql`, `curl`, `gd`, atau `zip`).
* **Solusi Terbaik:**
  Gunakan Docker karena Docker sudah menyediakan lingkungan PHP versi 8.4 lengkap dengan seluruh ekstensi yang dibutuhkan. Anda tidak perlu memasang atau mengedit file `php.ini` di laptop Anda. Cukup jalankan perintah Composer melalui Docker seperti pada Kasus A di atas.

### Kasus C: Muncul Pesan "Allowed memory size of ... bytes exhausted" saat Composer Install
* **Penyebab:** Batas memori PHP bawaan komputer tidak cukup saat mengunduh paket Composer.
* **Solusi:**
  Jalankan Composer dengan menonaktifkan batas memori menggunakan perintah:
  ```bash
  docker exec -it laravel-UJICOBA php -d memory_limit=-1 /usr/bin/composer install
  ```

---

## 3. Masalah Database MySQL dan Migrasi

### Kasus A: Muncul Pesan "SQLSTATE[HY000] [2002] Connection refused"
* **Penyebab:** Aplikasi Laravel mencoba menghubungi database, tetapi container MySQL belum selesai menyala atau belum siap menerima sambungan.
* **Solusi:**
  1. Saat pertama kali `docker compose up -d` dijalankan, MySQL membutuhkan waktu sekitar 15 sampai 30 detik untuk menyiapkan data awal.
  2. Tunggu sekitar 20 detik, lalu jalankan kembali perintah migrasi:
     ```bash
     docker exec -it laravel-UJICOBA php artisan migrate --seed
     ```
  3. Pastikan konfigurasi host di dalam file `backend/.env` tertulis:
     ```env
     DB_HOST=mysql-server
     DB_PORT=3306
     ```
     Catatan: Jika Anda mengakses database dari luar Docker menggunakan aplikasi Navicat atau DBeaver di laptop, gunakan host `127.0.0.1` dan port `3307`. Namun untuk aplikasi Laravel yang berjalan di dalam Docker, tetap gunakan host `mysql-server` dan port `3306`.

### Kasus B: Muncul Pesan "Access denied for user 'api_ujikom'@'...' (using password: YES)"
* **Penyebab:** Nama pengguna atau kata sandi database pada file `backend/.env` tidak sama dengan pengaturan di file `docker-compose.yml`.
* **Solusi:**
  Buka file `backend/.env` dan pastikan bagian database tertulis persis seperti ini:
  ```env
  DB_CONNECTION=mysql
  DB_HOST=mysql-server
  DB_PORT=3306
  DB_DATABASE=API_ujikom
  DB_USERNAME=api_ujikom
  DB_PASSWORD=api_ujikom
  ```

### Kasus C: Data Database Kacau, Duplikat, atau Ingin Mengulang dari Awal
* **Penyebab:** Terjadi salah input data saat pengujian atau ingin membersihkan seluruh tabel agar kembali ke kondisi awal bawaan seeder.
* **Solusi:**
  Jalankan perintah reset bersih berikut:
  ```bash
  docker exec -it laravel-UJICOBA php artisan migrate:fresh --seed
  ```
  Perintah ini akan menghapus semua tabel lama, membuat ulang tabel dari file migrasi yang bersih, dan mengisi kembali akun pengujian admin, petugas, serta siswa.

---

## 4. Masalah Port Bentrok (Port Already in Use)

### Kasus: Muncul Pesan "Bind for 0.0.0.0:8000 failed: port is already allocated" atau "port 3307 is already allocated"
* **Penyebab:** Ada aplikasi lain di laptop Anda yang sedang menggunakan port yang sama. Contoh umum:
  * Port 8000 sedang digunakan oleh proyek Laravel lain.
  * Port 3307 sedang digunakan oleh aplikasi database lain.
  * Port 8081 sedang digunakan oleh aplikasi lain.

### Solusi 1: Mematikan Aplikasi yang Memakai Port Tersebut
1. Buka PowerShell di Windows sebagai Administrator.
2. Cari nomor proses (PID) yang memakai port 8000 dengan perintah:
   ```powershell
   netstat -ano | findstr :8000
   ```
3. Lihat angka paling kanan (misalnya angka 12345), lalu matikan prosesnya dengan perintah:
   ```powershell
   taskkill /PID 12345 /F
   ```

### Solusi 2: Mengganti Nomor Port di File docker-compose.yml
Jika Anda tidak ingin mematikan aplikasi lain tersebut, Anda bisa mengganti angka port di file `docker-compose.yml`.

Sebagai contoh, ubah baris port aplikasi dari:
```yaml
ports:
  - "8000:8000"
```
Menjadi angka port lain yang kosong, misalnya:
```yaml
ports:
  - "8080:8000"
```
Setelah diubah dan disimpan, jalankan ulang:
```bash
docker compose up -d
```
Maka web dapat diakses melalui alamat http://localhost:8080.

---

## 5. Masalah Kunci Aplikasi dan File Konfigurasi (.env)

### Kasus A: Muncul Pesan "No application encryption key has been specified"
* **Penyebab:** Kunci enkripsi keamanan Laravel belum dibuat pada file `.env`.
* **Solusi:**
  Jalankan perintah pembuatan kunci otomatis berikut:
  ```bash
  docker exec -it laravel-UJICOBA php artisan key:generate
  ```

### Kasus B: File backend/.env Tidak Ditemukan
* **Penyebab:** File `.env` belum dibuat dari file contoh `.env.example`.
* **Solusi:**
  Salin file contoh menjadi file `.env` dengan perintah:
  ```bash
  copy backend\.env.example backend\.env
  ```
  Kemudian jalankan kembali pembuatan kunci:
  ```bash
  docker exec -it laravel-UJICOBA php artisan key:generate
  ```

---

## 6. Masalah Gambar Upload Tidak Muncul (Storage Link)

### Kasus: Foto Alat atau Foto Profil yang Diupload Berwarna Abu-Abu atau Menghasilkan Error 404 Not Found
* **Penyebab:** Tautan penghubung (symlink) dari folder penyimpanan fisik `storage/app/public` menuju folder web publik `public/storage` belum terpasang atau terputus.
* **Solusi:**
  Jalankan perintah pembuatan tautan penyimpanan berikut di dalam container:
  ```bash
  docker exec -it laravel-UJICOBA php artisan storage:link --force
  ```
  Setelah itu, segarkan (refresh) halaman browser Anda dengan menekan tombol Ctrl dan F5 secara bersamaan.

---

## 7. Masalah Izin Folder (Permission Denied)

### Kasus: Muncul Pesan "The stream or file .../storage/logs/laravel.log could not be opened: failed to open stream: Permission denied"
* **Penyebab:** Sistem operasi membatasi hak akses web server untuk menulis file catatan log atau menyimpan file unggahan di folder `storage`.
* **Solusi:**
  Beri hak akses penuh pada folder storage dan cache dengan perintah:
  ```bash
  docker exec -it laravel-UJICOBA chmod -R 777 storage bootstrap/cache
  ```

---

## 8. Masalah Cache yang Menumpuk

### Kasus: Halaman Web Tidak Berubah Padahal File Blade atau Kode Controller Sudah Diedit
* **Penyebab:** Laravel menyimpan salinan sementara (cache) dari tampilan halaman, rute URL, dan konfigurasi agar website berjalan cepat. Terkadang cache lama masih tersimpan sehingga perubahan kode belum terlihat.
* **Solusi:**
  Bersihkan seluruh cache aplikasi dengan satu perintah sakti:
  ```bash
  docker exec -it laravel-UJICOBA php artisan optimize:clear
  ```
  Perintah ini otomatis membersihkan:
  * Cache konfigurasi sistem
  * Cache daftar rute URL
  * Cache kompilasi template Blade
  * Cache data aplikasi sementara

---

## 9. Perintah Pemulihan Cepat (One-Line Fix)

Jika Anda bingung error apa yang sedang terjadi dan ingin membuat aplikasi kembali berjalan normal seperti baru pertama kali dipasang, lakukan 3 langkah pemulihan cepat ini di terminal:

1. **Langkah 1: Matikan dan Nyalakan Ulang Kontainer:**
   ```bash
   docker compose down && docker compose up -d
   ```
2. **Langkah 2: Bersihkan Cache dan Pasang Tautan Gambar:**
   ```bash
   docker exec -it laravel-UJICOBA php artisan optimize:clear
   docker exec -it laravel-UJICOBA php artisan storage:link --force
   ```
3. **Langkah 3: Reset dan Isi Ulang Database:**
   ```bash
   docker exec -it laravel-UJICOBA php artisan migrate:fresh --seed
   ```

Setelah ketiga langkah di atas selesai, buka browser Anda di http://localhost:8000 dan aplikasi siap digunakan kembali dengan lancar.
