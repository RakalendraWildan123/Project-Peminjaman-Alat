# AlatKu - Sistem Peminjaman Alat Praktikum

Aplikasi web dan API untuk mengelola peminjaman serta pengembalian alat praktikum sekolah atau kampus secara rapi, cepat, dan otomatis.

---

## Daftar Isi
1. Penjelasan Singkat Aplikasi
2. Siapa Saja Penggunanya (3 Hak Akses)
3. Teknologi yang Digunakan
4. Struktur Folder dan Letak File
5. Database (Lemari Arsip Digital)
6. Komponen Penting di Balik Layar
7. Pintu Masuk Aplikasi (Routing)
8. Tampilan Halaman (Views)
9. Layanan REST API
10. Alur Kerja Aplikasi dari Awal Sampai Selesai
11. Cara Menjalankan Aplikasi

---

## 1. Penjelasan Singkat Aplikasi

Bayangkan sebuah laboratorium atau bengkel di sekolah. Di sana ada banyak alat praktikum seperti kabel jaringan, kamera, atau perkakas pertukangan.

Biasanya, siswa meminjam alat menggunakan buku tulis manual:
* Siswa mencatat nama dan alat yang dipinjam di buku.
* Petugas lab harus mencari barang di lemari dan menghitung sisa barang secara manual.
* Saat alat dikembalikan, sering timbul lupa apakah barang sudah dikembalikan lengkap, apakah ada bagian yang rusak, atau apakah siswa telat mengembalikan sehingga perlu membayar denda.

Aplikasi AlatKu mengubah cara kerja buku manual tersebut menjadi sistem online yang praktis:
* Siswa bisa melihat katalog alat dari HP atau laptop masing-masing.
* Siswa mengajukan pinjaman alat secara mandiri.
* Petugas lab cukup menekan tombol untuk menyetujui, dan stok barang di sistem akan langsung berkurang otomatis.
* Saat alat dikembalikan, petugas memeriksa kondisi barang, dan sistem otomatis menghitung apakah pengembalian tersebut tepat waktu atau terlambat.
* Semua transaksi dicatat rapi secara otomatis oleh sistem, sehingga tidak ada barang yang hilang tanpa jejak.

---

## 2. Siapa Saja Penggunanya (3 Hak Akses)

Aplikasi ini membagi penggunanya menjadi tiga tingkatan peran:

### Peran Siswa atau Peminjam
* Melihat daftar alat yang sedang tersedia untuk dipinjam, lengkap dengan foto barang, jumlah stok, dan kondisi fisiknya.
* Memilih alat dan mengajukan pinjaman dengan menentukan tanggal rencana pengembalian.
* Memantau status pinjamannya di halaman riwayat, apakah masih menunggu persetujuan, sedang dipinjam, sudah selesai, atau ditolak.
* Mengajukan permintaan pengembalian ketika alat selesai dipakai.

### Peran Petugas Lab
* Menerima permohonan peminjaman baru dari siswa.
* Menyetujui atau menolak permohonan pinjam.
* Menerima barang fisik saat dikembalikan oleh siswa ke meja lab.
* Mencatat kondisi barang saat kembali (apakah masih baik atau rusak).
* Memasukkan nominal denda jika siswa terlambat mengembalikan atau jika alat mengalami kerusakan.
* Melihat dan mencetak laporan transaksi peminjaman.

### Peran Admin Utama (Kepala Lab atau Guru)
* Memiliki seluruh hak yang dimiliki oleh petugas lab.
* Menambah jenis alat baru ke gudang, mengedit rincian alat, atau menghapus alat yang sudah tidak dipakai.
* Mengatur kategori alat, seperti kategori Jaringan, Multimedia, atau Perkakas.
* Mengelola akun pengguna, seperti membuat akun petugas baru atau menambah akun siswa.
* Melihat grafik statistik pada dashboard, seperti persentase status peminjaman dan daftar siswa yang paling sering meminjam alat.
* Memantau buku rekam jejak aktivitas sistem untuk melihat riwayat perubahan data.

---

## 3. Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan beberapa teknologi utama:

* **Laravel Versi 11:** Kerangka kerja utama berbasis PHP. Laravel menyediakan fondasi keamanan yang kuat, sistem login siap pakai, dan mempermudah pengelolaan data sehingga pembuatan aplikasi menjadi rapi dan aman.
* **MySQL Versi 8:** Sistem basis data untuk menyimpan seluruh informasi aplikasi secara teratur, mulai dari data akun, daftar inventaris barang, riwayat pinjam, hingga catatan denda.
* **Tailwind CSS:** Alat bantu penataan tampilan antarmuka agar halaman web terlihat rapi, bersih, dan nyaman digunakan di layar komputer maupun ponsel pintar.
* **SweetAlert2:** Kotak pesan dialog konfirmasi yang muncul di layar, misalnya untuk memastikan petugas benar-benar yakin sebelum menyetujui atau menolak peminjaman.
* **Chart.js:** Pustaka pembuat grafik visual untuk menampilkan diagram lingkaran dan diagram batang di dashboard admin.
* **Docker:** Wadah pengemas aplikasi agar sistem ini bisa langsung dijalankan di komputer mana saja tanpa perlu memasang PHP atau MySQL secara manual satu per satu.

---

## 4. Struktur Folder dan Letak File

Folder utama aplikasi berada di dalam direktori backend. Berikut panduan praktis untuk menemukan file yang Anda butuhkan:

* **Folder app:** Merupakan pusat logika dan aturan kerja aplikasi.
  * **app/Http/Controllers:** Tempat file manajer yang mengatur alur data antara tampilan layar dan database.
  * **app/Http/Middleware:** Tempat file penjaga keamanan yang memeriksa hak akses pengguna sebelum halaman dibuka.
  * **app/Http/Requests:** Tempat file validasi yang memeriksa apakah formulir diisi dengan benar.
  * **app/Http/Resources:** Tempat file penata data agar informasi yang dikirim ke aplikasi lain tersusun rapi.
  * **app/Models:** Tempat file perwakilan tabel database, seperti file Alat, User, dan Peminjaman.
  * **app/Observers:** Tempat file pemantau otomatis yang bertugas mencatat buku riwayat aktivitas sistem.
* **Folder database:** Berisi cetakan struktur tabel dan pengisi data awal.
  * **database/migrations:** File skrip yang bertugas membuat tabel di database secara otomatis.
  * **database/seeders:** File skrip yang bertugas mengisi data contoh atau akun pengujian awal.
* **Folder resources/views:** Berisi seluruh desain halaman web yang dilihat oleh pengguna.
  * **views/layouts:** Kerangka utama halaman seperti sidebar dan menu navigasi atas.
  * **views/auth:** Halaman formulir login dan pendaftaran akun.
  * **views/admin:** Halaman khusus untuk menu admin.
  * **views/petugas:** Halaman khusus untuk menu petugas lab.
  * **views/peminjam:** Halaman khusus untuk katalog dan riwayat peminjaman siswa.
* **Folder routes:** Berisi daftar alamat URL aplikasi.
  * **routes/web.php:** Daftar alamat yang dibuka melalui browser laptop atau HP.
  * **routes/api.php:** Daftar alamat yang digunakan untuk pertukaran data dengan aplikasi mobile atau sistem luar.
* **Folder storage/app/public:** Tempat penyimpanan file fisik seperti foto alat dan foto profil pengguna.

---

## 5. Database (Lemari Arsip Digital)

Database aplikasi ini terdiri dari beberapa tabel utama:

* **Tabel users:** Menyimpan data nama, alamat email, kata sandi yang disamarkan dengan aman, nomor telepon, alamat rumah, foto profil, dan tingkatan peran (admin, petugas, atau peminjam).
* **Tabel kategori:** Menyimpan kelompok penggolongan alat, misalnya kategori Komputer, Multimedia, atau Elektronika.
* **Tabel alat:** Menyimpan data nama alat, kategori alat, sisa jumlah stok di gudang, kondisi fisik barang, catatan deskripsi, dan foto alat.
* **Tabel peminjaman:** Menyimpan catatan transaksi peminjaman utama, meliputi siapa yang meminjam, tanggal peminjaman dibuat, batas tanggal rencana kembali, dan status pinjaman saat ini.
* **Tabel detail_pinjam:** Menyimpan rincian alat apa saja yang dipinjam beserta jumlah unit yang diambil dalam satu formulir pinjaman.
* **Tabel pengembalian:** Menyimpan bukti fisik saat alat diserahkan kembali ke lab, tanggal alat diterima, kondisi barang saat kembali, catatan denda, dan nama petugas yang melayani.
* **Tabel log_aktivitas:** Buku catatan otomatis yang merekam seluruh peristiwa penting di aplikasi beserta waktu kejadiannya.

### Penjelasan Status Peminjaman
* **diajukan:** Pengajuan baru saja dikirim oleh siswa dan masih menunggu persetujuan petugas lab.
* **dipinjam:** Pengajuan sudah disetujui petugas, barang fisik sudah diambil siswa, dan stok di gudang sudah dikurangi.
* **selesai:** Barang sudah dikembalikan ke lab dalam kondisi aman dan tepat waktu.
* **telat:** Barang dikembalikan melewati batas tanggal yang sudah ditentukan sebelumnya.
* **ditolak:** Pengajuan tidak disetujui oleh petugas, misalnya karena stok alat sedang kosong atau alat sedang dalam perbaikan.

---

## 6. Komponen Penting di Balik Layar

Berikut fungsi dari komponen-komponen utama aplikasi:

### Controller (Pengatur Alur Data)
Controller bertindak sebagai pengelola utama. Ketika pengguna menekan sebuah tombol di web, Controller yang akan menerima permintaan tersebut, mengambil data yang diperlukan dari database, memeriksa apakah ada aturan yang dilanggar, lalu menampilkan hasilnya kembali ke layar pengguna.

### Middleware (Penjaga Pintu Masuk)
Middleware bertindak sebagai penjaga keamanan. Sebelum sebuah halaman dimuat, Middleware memeriksa apakah pengguna sudah login dan apakah pengguna memiliki hak untuk masuk. Sebagai contoh, jika seorang siswa mencoba membuka halaman admin, Middleware akan langsung menghadang dan mengembalikannya ke halaman yang sesuai.

### Form Request (Pemeriksa Formulir)
Form Request bertindak memeriksa isian formulir. Sebelum data disimpan, Form Request memeriksa apakah ada kolom penting yang terlewat atau format yang salah. Contohnya, jika siswa mengisi rencana tanggal pengembalian dengan tanggal kemarin, sistem akan menolak formulir tersebut secara otomatis.

### Observer (Pencatat Otomatis)
Observer bekerja di latar belakang tanpa mengganggu alur utama. Setiap kali ada penambahan alat baru, perubahan status pinjaman, atau penghapusan akun, Observer akan mencatat peristiwa tersebut ke dalam tabel log aktivitas secara otomatis.

### API Resource (Penyaring Data)
API Resource bertindak merapikan susunan data sebelum dikirimkan ke aplikasi lain. Bagian ini memastikan bahwa data penting seperti kata sandi tidak ikut terkirim, serta memastikan tautan gambar foto dapat diakses dengan benar.

---

## 7. Pintu Masuk Aplikasi (Routing)

Aplikasi memiliki dua pintu masuk utama yang diatur pada folder routes:

* **File routes/web.php:**
  Jalur ini digunakan saat pengguna membuka aplikasi melalui browser web. Di sini diatur pembagian halaman, misalnya halaman katalog hanya bisa dibuka oleh siswa, sedangkan halaman persetujuan hanya bisa dibuka oleh petugas dan admin.
* **File routes/api.php:**
  Jalur ini digunakan untuk melayani pertukaran data dalam format teks terstruktur (JSON). Jalur ini sangat berguna jika di masa depan sekolah ingin membuat aplikasi mobile Android atau iOS.

---

## 8. Tampilan Halaman (Views)

Seluruh tampilan antarmuka dibuat menggunakan teknologi Blade milik Laravel:

* **Kerangka Bersama (Layouts):** Menu samping, bilah atas, dan logo sekolah hanya dibuat satu kali di dalam file kerangka utama, sehingga halaman lain tinggal mengisi konten di bagian tengahnya saja.
* **Katalog Berbasis Kartu:** Setiap barang di katalog disajikan dalam bentuk kartu yang memuat foto, nama alat, kategori, dan sisa stok. Jika sebuah alat belum memiliki foto, sistem akan menampilkan ikon perlengkapan secara otomatis agar tampilan tetap rapi.
* **Dialog Konfirmasi:** Setiap tindakan penting, seperti menyetujui peminjaman atau menghapus data, selalu menampilkan jendela konfirmasi untuk mencegah kesalahan penekanan tombol.

---

## 9. Layanan REST API

Aplikasi menyediakan jalur data resmi dengan alamat dasar http://localhost:8000/api:

* **Layanan Terbuka:**
  * Masuk akun untuk mendapatkan kunci token keamanan.
  * Mendaftar akun baru bagi siswa.
* **Layanan Khusus Siswa Terdaftar:**
  * Membaca daftar katalog barang yang tersedia.
  * Mengirimkan formulir pengajuan peminjaman baru.
  * Membaca daftar riwayat peminjaman milik sendiri.
* **Layanan Khusus Petugas:**
  * Menyetujui permohonan pinjam yang masuk.
  * Memproses barang yang dikembalikan oleh siswa.
  * Menarik rekapitulasi data laporan.
* **Layanan Khusus Admin:**
  * Mengelola seluruh data alat, kategori barang, dan akun pengguna.
  * Membaca seluruh buku rekam jejak sistem.

---

## 10. Alur Kerja Aplikasi dari Awal Sampai Selesai (Penjelasan Detail File ke File)

Untuk memahami bagaimana kode di dalam aplikasi bekerja, mari kita ikuti perjalanan data dari satu file ke file lainnya saat terjadi aktivitas di aplikasi.

### Hubungan Antara File Model dan File Migration
Banyak orang bingung apa bedanya file Model dan file Migration. Berikut perbedaannya secara sederhana:
* **File Migration (misalnya database/migrations/create_peminjaman_table.php):**
  Ibarat cetakan biru atau kuli bangunan. File ini bertugas membuat lemari tabel fisik di dalam database MySQL beserta tiang-tiang kolomnya, seperti kolom nama, kolom tanggal pinjam, dan kolom status. File migration dijalankan di awal saat aplikasi pertama kali dipasang.
* **File Model (misalnya app/Models/Peminjaman.php):**
  Ibarat sekretaris yang bertugas mengisi dan mengambil berkas dari lemari tabel tersebut saat aplikasi sedang berjalan. Model tidak membuat tabel baru, melainkan bertindak mewakili tabel yang sudah dicetak oleh file migration tadi.

---

### Alur 1: Saat Siswa Mengajukan Peminjaman Alat

Berikut tahapan perjalanan file saat seorang siswa mengajukan pinjaman:

1. **File Tampilan Formulir (resources/views/peminjam/detail-alat.blade.php):**
   Siswa membuka halaman rincian barang, memilih jumlah alat yang ingin dipinjam, menentukan tanggal rencana pengembalian, lalu menekan tombol Ajukan Peminjaman. Tombol ini mengirimkan seluruh isian formulir ke alamat web peminjam/ajukan.
2. **File Penunjuk Arah (routes/web.php):**
   Menerima kiriman dari tombol tadi. File web.php memeriksa alamat peminjam/ajukan dan mengarahkan data tersebut ke fungsi ajukanPeminjaman yang ada di dalam file controller peminjam.
3. **File Penjaga Keamanan (app/Http/Middleware/CheckRole.php):**
   Sebelum data sampai ke controller, file penjaga ini memeriksa terlebih dahulu: apakah orang yang menekan tombol ini benar-benar siswa yang sah dan sudah login. Jika terbukti siswa, data dipersilakan lewat.
4. **File Manajer Pengendali (app/Http/Controllers/PeminjamController.php):**
   Di dalam file ini, fungsi ajukanPeminjaman mulai bekerja:
   * Memeriksa tanggal rencana pengembalian agar tidak diisi tanggal kemarin.
   * Membuka transaksi penyimpanan yang aman.
   * Menghubungi file Model Peminjaman dan Model DetailPinjam untuk menyimpan data.
5. **File Model Peminjaman (app/Models/Peminjaman.php) Menuju Database:**
   Model Peminjaman mengambil data ID siswa, tanggal pinjam hari ini, tanggal rencana kembali, dan status diajukan. Kemudian Model ini memasukkan data tersebut ke dalam tabel peminjaman di database MySQL, yaitu tabel yang strukturnya sudah dibuat oleh file migration create_peminjaman_table.php.
6. **File Model DetailPinjam (app/Models/DetailPinjam.php):**
   Siswa bisa meminjam lebih dari satu jenis alat sekaligus. Model ini bertugas mencatat alat apa saja dan berapa unit yang diambil ke dalam tabel detail_pinjam yang dicetak oleh file migration create_detail_pinjam_table.php.
7. **File Kamera Pemantau Otomatis (app/Observers/PeminjamanObserver.php):**
   Tepat saat file Model Peminjaman selesai menyimpan data baru ke database, file Observer ini otomatis terbangun. Observer ini memanggil file Model LogAktivitas (app/Models/LogAktivitas.php) untuk menulis baris riwayat baru ke tabel log aktivitas di database: Siswa bernama fulan telah mengajukan peminjaman baru.
8. **Kembali ke File Tampilan Riwayat (resources/views/peminjam/riwayat.blade.php):**
   Setelah semua penyimpanan berhasil, Controller mengarahkan siswa ke halaman riwayat peminjaman. Halaman ini membaca data dari Model dan menampilkan kartu pinjaman baru dengan label kuning bertuliskan Menunggu Persetujuan.

---

### Alur 2: Saat Petugas Menyetujui Peminjaman

Berikut tahapan perjalanan file saat petugas lab menyetujui permohonan siswa:

1. **File Tampilan Petugas (resources/views/petugas/peminjaman/index.blade.php):**
   Petugas melihat daftar siswa yang sedang mengajukan pinjaman. Petugas menekan tombol Setujui. File dialog konfirmasi (resources/views/layouts/sweetalert.blade.php) memunculkan kotak pertanyaan: Apakah Anda yakin ingin menyetujui peminjaman ini. Saat petugas menekan Ya, formulir persetujuan dikirim.
2. **File Penunjuk Arah (routes/web.php):**
   Meneruskan klik persetujuan tersebut ke fungsi setujuiPeminjaman yang berada di file controller petugas.
3. **File Manajer Pengendali (app/Http/Controllers/PetugasController.php):**
   Fungsi setujuiPeminjaman membuka sistem penguncian database agar aman dari tabrakan data jika ada orang lain yang meminjam di detik yang sama.
4. **File Model Alat (app/Models/Alat.php):**
   Controller memanggil Model Alat untuk memeriksa stok barang di database. Jika sisa stok di database mencukupi, Model Alat diperintahkan untuk mengurangi jumlah stok barang tersebut di tabel alat (tabel yang dibuat oleh file migration create_alat_table.php).
5. **File Model Peminjaman (app/Models/Peminjaman.php):**
   Controller memerintahkan Model Peminjaman untuk mengubah kolom status transaksi di database dari yang tadinya diajukan menjadi dipinjam.
6. **File Pemantau Otomatis (app/Observers/PeminjamanObserver.php):**
   Observer mendeteksi bahwa kolom status pada tabel peminjaman baru saja berganti. Observer langsung menuliskan laporan ke tabel log aktivitas: Status peminjaman nomor sekian telah disetujui dan diubah menjadi dipinjam.
7. **Pembaruan Layar Petugas:**
   Controller mengirim pesan sukses warna hijau ke layar petugas lab, memberi tahu bahwa peminjaman telah resmi disetujui dan stok barang di gudang inventaris sudah otomatis berkurang.

---

### Alur 3: Saat Alat Fisik Dikembalikan ke Lab

Berikut tahapan perjalanan file saat barang dikembalikan oleh siswa:

1. **File Tampilan Pengembalian (resources/views/petugas/pengembalian/index.blade.php):**
   Siswa menyerahkan alat fisik ke meja lab. Petugas memeriksa kondisi barangnya, lalu menekan tombol Proses Pengembalian. Di layar muncul jendela untuk mengisi kondisi barang (misalnya Baik dan Lengkap) dan nominal denda jika ada. Petugas menekan tombol Simpan Pengembalian.
2. **File Penunjuk Arah (routes/web.php):**
   Mengarahkan data penyerahan barang tersebut ke fungsi prosesPengembalian di file controller petugas.
3. **File Manajer Pengendali (app/Http/Controllers/PetugasController.php):**
   Controller membandingkan tanggal hari ini dengan tanggal rencana pengembalian yang tersimpan di database:
   * Jika tanggal hari ini melewati batas rencana kembali, Controller menentukan status akhir menjadi telat dan mencatat denda.
   * Jika dikembalikan tepat waktu, Controller menentukan status akhir menjadi selesai.
4. **File Model Pengembalian (app/Models/Pengembalian.php):**
   Model ini menyimpan bukti pengembalian (tanggal pengembalian, kondisi fisik alat, nominal denda, dan nama petugas yang melayani) ke dalam tabel pengembalian yang dicetak oleh file migration create_pengembalian_table.php.
5. **File Model Peminjaman (app/Models/Peminjaman.php):**
   Model ini memperbarui kolom status pada transaksi peminjaman utama menjadi selesai atau telat.
6. **File Model Alat (app/Models/Alat.php):**
   Model Alat menambahkan kembali angka stok barang di database sesuai jumlah unit yang dikembalikan, sehingga alat tersebut langsung muncul kembali di etalase katalog dan siap dipinjam oleh siswa lainnya.
7. **File Pemantau Pengembalian (app/Observers/PengembalianObserver.php):**
   Observer otomatis mencatat rekam jejak bahwa alat telah resmi dikembalikan ke lab beserta catatan kondisi dan dendanya ke dalam tabel log aktivitas sistem.

---

## 11. Cara Menjalankan Aplikasi

Aplikasi ini dapat dijalankan dengan mudah menggunakan Docker Desktop:

1. **Menyalakan Aplikasi:**
   Buka terminal di folder project, lalu ketik perintah:
   docker compose up -d
2. **Menyiapkan Database Awal (Untuk Pertama Kali):**
   docker exec -it laravel-UJICOBA php artisan migrate --seed
3. **Membuka di Browser:**
   * Alamat Website Utama: http://localhost:8000
   * Alamat Database PhpMyAdmin: http://localhost:8081

### Akun Uji Coba:
Seluruh akun menggunakan kata sandi yang sama: password123

* **Akun Admin:** admin@gmail.com
* **Akun Petugas:** petugas@gmail.com
* **Akun Siswa Rian:** rian@gmail.com
* **Akun Siswa Siti:** siti@gmail.com
* **Akun Siswa Eka:** eka@gmail.com
