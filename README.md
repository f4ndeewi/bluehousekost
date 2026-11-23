# Blue House Kos — Website Laravel (User + Admin)

Website lengkap untuk **Blue House Kos** (kos putri eksklusif, 6 kamar, Malang), dibuat dari desain Stitch "Serene Sanctuary".
Sisi pengunjung (beranda, daftar kamar, detail kamar, form pemesanan) dan sisi admin (login, dasbor, kelola kamar dan foto, pesan masuk).

- Framework: **Laravel 12**, PHP **8.2+**, database default **SQLite** (tanpa instal apa pun; bisa MySQL)
- Tampilan: Blade + Tailwind (CDN) + CSS/JS kustom di `public/css` dan `public/js`
- Tidak perlu Node/npm.

---

## 1. Kebutuhan

| Perangkat lunak | Versi | Cek |
|---|---|---|
| PHP | 8.2 atau lebih baru | `php -v` |
| Composer | 2.x | `composer -V` |
| Ekstensi PHP | `pdo_sqlite`, `mbstring`, `openssl`, `fileinfo`, `tokenizer`, `xml`, `ctype`, `json`, `gd` (gd hanya untuk tes upload) | `php -m` |
| Internet | agar browser dapat memuat Tailwind CDN dan font Google | - |

Cara termudah di Windows: pasang **Laragon** (sudah termasuk PHP, Composer, MySQL) atau **XAMPP** + Composer.
macOS: `brew install php composer`.
Ubuntu: `sudo apt install php-cli php-sqlite3 php-mbstring php-xml php-gd php-curl php-zip composer unzip`.

## 2. Instalasi (pilih salah satu)

Buka terminal di folder project ini (`blue-house-kos`).

### A. Satu perintah (disarankan)

```bash
composer kos-setup
php artisan serve
```

`kos-setup` otomatis menjalankan: `composer install`, salin `.env`, buat file SQLite, `key:generate`, `storage:link`, `migrate --seed`.

### B. Manual, langkah demi langkah

```bash
composer install

# Linux/macOS
cp .env.example .env
touch database/database.sqlite
# Windows (CMD)
copy .env.example .env
type nul > database\database.sqlite

php artisan key:generate
php artisan storage:link
php artisan migrate --seed
php artisan serve
```

Buka:

- Situs: http://127.0.0.1:8000
- Admin: http://127.0.0.1:8000/admin/login

> Akun admin bawaan: **admin@bluehouse.test** / **password**. **Segera ganti** (lihat bagian 4).

### Memakai MySQL (opsional)

Buat database kosong, lalu ubah `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blue_house_kos
DB_USERNAME=root
DB_PASSWORD=
```

Lanjutkan dengan `php artisan migrate --seed`.

## 3. Menjalankan tes

```bash
php artisan test
```

Ada 17 tes: halaman publik, form pemesanan (validasi, honeypot, email), login admin, tambah/ubah/hapus kamar dengan upload foto, ganti sampul, ganti status, dan kelola pesan.
Tes upload memakai `UploadedFile::fake()->image()` yang membutuhkan ekstensi **GD**.

## 4. Keamanan awal (wajib dilakukan)

Ganti akun admin dengan salah satu cara:

```bash
# (a) buat/ubah akun kapan saja
php artisan kos:admin pemilik@bluehousekos.com "sandi-kuat-anda" --name="Pemilik"

# (b) atau isi di .env SEBELUM menjalankan `migrate --seed`
ADMIN_EMAIL=pemilik@bluehousekos.com
ADMIN_PASSWORD=sandi-kuat-anda
```

Setelah itu hapus akun bawaan `admin@bluehouse.test`, misalnya lewat `php artisan tinker`:
`App\Models\User::where('email','admin@bluehouse.test')->delete();`

## 5. Konfigurasi (`.env`)

```env
APP_NAME="Blue House Kos"
APP_URL=http://127.0.0.1:8000     # ganti dengan domain Anda di produksi
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

KOS_WHATSAPP=6282336970925          # format internasional tanpa "+" (dipakai link wa.me)
KOS_WHATSAPP_DISPLAY=082336970925   # yang tampil di situs
KOS_ADMIN_EMAIL=                    # isi untuk menerima email tiap ada pesan baru
```

Teks "tentang", alamat, periode pembayaran, dan daftar fasilitas ada di `config/kos.php`.

### Notifikasi email

1. Isi `KOS_ADMIN_EMAIL=pemilik@gmail.com`.
2. Atur SMTP di `.env` (`MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`).
   Untuk uji coba, biarkan `MAIL_MAILER=log`: isi email akan tertulis di `storage/logs/laravel.log`.
3. Jika email gagal terkirim, **pesan tetap tersimpan** dan tampil di dasbor dengan label "Gagal".

## 6. Panduan penggunaan admin

**Login**: buka `/admin/login` (atau tombol "Login Admin" di header situs).

**Dasbor** (`/admin`)

- Kartu **Okupansi** menampilkan jumlah kamar tersedia dan terisi.
- **Data Kamar**: klik chip status (Tersedia/Terisi) untuk mengubahnya seketika; tombol **Ubah**; tombol **Hapus** (foto ikut terhapus).
- **Pesan Masuk**: nama, WhatsApp (klik untuk chat), email, kamar, jenis, isi (klik untuk membuka penuh), waktu, status email.
  Pesan baru bertanda titik biru. Tombol amplop = tandai sudah/belum dibaca, tombol sampah = hapus, dan ada "Tandai semua dibaca".

**Tambah / Ubah Kamar**

- Isi nama, tarif (angka saja, mis. `800000`), status, deskripsi, dan centang fasilitas.
- **Foto**: tarik dan letakkan, atau klik area unggah. Format JPG/PNG, maks 5MB per file, maks 10 foto per kamar.
  Saat mengubah kamar, Anda bisa memilih **Sampul** (foto utama di kartu dan galeri), mengubah keterangan, atau mencentang **Hapus foto ini**.
- Perubahan langsung tampil di situs publik.

## 7. Foto kamar

- Foto yang diunggah disimpan di `storage/app/public/rooms/{id}/` dan disajikan lewat `public/storage` (**wajib** menjalankan `php artisan storage:link`).
- Seeder memakai foto contoh berupa URL dari desain Stitch yang bisa kedaluwarsa. Ganti dengan foto asli lewat Admin, menu Ubah Kamar.

## 8. Deploy ke server (ringkas)

1. Upload project, lalu `composer install --no-dev --optimize-autoloader`.
2. Di `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain-anda`, serta pengaturan database dan SMTP.
3. Jalankan `php artisan key:generate`, `php artisan migrate --force`, `php artisan storage:link`, lalu buat admin lewat `php artisan kos:admin email sandi`.
   (Jika ingin memuat 6 kamar contoh, jalankan `php artisan db:seed --class=RoomSeeder --force`.)
4. **Document root** web server harus mengarah ke folder `public/` (bukan root project).
5. Pastikan `storage/` dan `bootstrap/cache/` bisa ditulis web server (`chmod -R 775`).
6. `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
7. Batas upload foto: di `php.ini` set `upload_max_filesize` minimal 6M dan `post_max_size` minimal 40M (untuk 10 foto).

Catatan: Tailwind dimuat via CDN agar tanpa build step. Itu ideal untuk memulai; untuk kinerja maksimal di produksi bisa dimigrasikan ke Tailwind + Vite.

## 9. Pemecahan masalah

| Gejala | Penyebab dan solusi |
|---|---|
| `could not find driver` | Ekstensi `pdo_sqlite` belum aktif di `php.ini` (hapus tanda `;` pada `extension=pdo_sqlite`). |
| `Database file ... does not exist` | Buat file kosong `database/database.sqlite` (lihat langkah instalasi). |
| Tampilan polos/tidak berwarna | Browser tidak bisa mengakses `cdn.tailwindcss.com` atau Google Fonts (offline atau diblokir). |
| Foto unggahan tidak tampil | Jalankan `php artisan storage:link`; pastikan `APP_URL` benar. |
| Upload foto gagal | Naikkan `upload_max_filesize` dan `post_max_size` di `php.ini`, lalu restart server. |
| `419 Page Expired` | Sesi/CSRF kedaluwarsa: muat ulang halaman lalu kirim lagi. Pastikan `APP_URL` dan domain cocok. |
| Lupa sandi admin | `php artisan kos:admin email sandi-baru` |
| "Terlalu banyak percobaan" saat login | Tunggu 1 menit (batas 5 percobaan per email+IP). |
| Ubah `.env` tidak berpengaruh | `php artisan config:clear` |
| Mulai ulang dari awal | `php artisan migrate:fresh --seed` (menghapus semua data!) |

## 10. Struktur dan rute

```
app/Http/Controllers/          HomeController, RoomController, InquiryController (publik)
app/Http/Controllers/Admin/    AuthController, DashboardController, RoomController, InquiryController
app/Http/Middleware/           EnsureAdmin (penjaga /admin)
app/Http/Requests/             StoreInquiryRequest, RoomRequest
app/Models/                    Room, RoomPhoto, Inquiry, User
app/Mail/InquiryReceived.php   email notifikasi
app/Console/Commands/          kos:admin
config/kos.php                 data dan teks kos
database/migrations/           rooms, room_photos, inquiries (+ users, cache, jobs bawaan)
database/seeders/              RoomSeeder, AdminSeeder, InquirySeeder (contoh, hanya env local)
resources/views/               layouts, partials, pages, rooms, admin, emails
public/css/kos.css             gaya kustom
public/js/                     kos.js, tailwind-config.js, room-detail.js, admin.js, admin-room-form.js
tests/Feature/                 UserPagesTest, AdminTest
```

| Method | URL | Keterangan |
|---|---|---|
| GET | `/` | Beranda |
| GET | `/kamar` | Daftar kamar |
| GET | `/kamar/{slug}` | Detail kamar (mis. `kamar-1`) |
| POST | `/kamar/{slug}/pesan` | Kirim form (throttle 5/menit, honeypot) |
| GET/POST | `/admin/login` | Login |
| POST | `/admin/logout` | Logout |
| GET | `/admin` | Dasbor |
| GET/POST | `/admin/kamar/tambah`, `/admin/kamar` | Tambah kamar |
| GET/PUT | `/admin/kamar/{id}/ubah`, `/admin/kamar/{id}` | Ubah kamar |
| PATCH | `/admin/kamar/{id}/status` | Ganti Tersedia/Terisi |
| DELETE | `/admin/kamar/{id}` | Hapus kamar |
| POST/PATCH/DELETE | `/admin/pesan/baca-semua`, `/admin/pesan/{id}/baca`, `/admin/pesan/{id}` | Kelola pesan |

Skema database: `rooms` (number, name, slug, price, status, description, facilities JSON), `room_photos` (room_id, path, caption, sort_order), `inquiries` (room_id, name, whatsapp, email, type, message, is_read, email_sent), dan `users`.

## 11. Hasil verifikasi

Diuji pada **Laravel 12.73.0, PHP 8.3, SQLite**:

- `migrate --seed` sukses; 17 tes otomatis lulus (89 assertion).
- Uji HTTP end-to-end lewat `php artisan serve`: semua halaman publik dan aset; kirim form valid dan tidak valid; CSRF (419 tanpa token); login salah/benar; pembatas login; dasbor; tambah kamar dengan unggah 2 foto PNG; foto terbaca via `/storage`; ubah kamar (ganti sampul, keterangan, hapus foto); ganti status; hapus kamar (folder foto ikut terhapus); tandai/hapus pesan; logout.
- Seluruh JS lolos `node --check`; HTML hasil render seimbang (tidak ada tag tak tertutup).

Belum diverifikasi: tampilan visual di browser (Tailwind CDN tidak bisa diakses dari lingkungan uji), MySQL, dan pengiriman email lewat SMTP sungguhan.
