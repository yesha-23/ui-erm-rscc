# UI EMR RSCC

Redesign tampilan **Electronic Medical Record Rumah Sakit Condong Catur** dengan Laravel.
Repo ini berisi **tampilan dan alur**. Database dan logika dilanjutkan oleh tim IT RSCC.

> Semua data di aplikasi ini adalah **data contoh**, bukan data pasien asli.

---

## Teknologi

| Bagian | Yang dipakai |
|---|---|
| Framework | Laravel (PHP 8.3), Blade components |
| Tampilan | Bootstrap 5, Bootstrap Icons, tema `resources/css/rscc-theme.css` |
| Build aset | Vite (`npm run dev`) |
| Data sementara | `app/Support/DataContoh.php` |
| Lingkungan lokal | Laragon (PHP 8.3, MySQL, phpMyAdmin) |

---

## Cara menjalankan

1. Clone repo ke folder proyek (yang menjadi Document Root Laragon):
   ```bash
   git clone https://github.com/yesha-23/ui-erm-rscc.git
   cd ui-erm-rscc
   ```
2. Pasang dependensi:
   ```bash
   composer install
   npm install
   ```
3. Siapkan `.env`:
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
   Lalu isi bagian berikut:
   ```env
   APP_NAME="EMR RSCC"
   APP_URL=http://ui-erm-rscc.test
   APP_LOCALE=id

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=emr_rscc
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. Buat database `emr_rscc` di phpMyAdmin, lalu:
   ```bash
   php artisan migrate
   ```
5. Jalankan Vite (biarkan terminal ini tetap terbuka):
   ```bash
   npm run dev
   ```
6. Buka **http://ui-erm-rscc.test**, atau jalankan `php artisan serve` lalu buka http://127.0.0.1:8000

> Pastikan `php -v` menunjukkan **8.3**. Kalau muncul error *"requires PHP >= 8.3"*,
> terminal masih memakai PHP lain (misalnya XAMPP). Perbaiki PATH Windows atau pakai Terminal Laragon.

---

## Struktur folder

```
app/Http/Controllers/   Auth, Beranda, Kunjungan, RekamMedis
app/Support/            DataContoh.php  ← sumber data sementara
public/images/          logo-rscc.png
resources/css/          app.css, rscc-theme.css, print.css
resources/js/           app.js, rscc.js
resources/views/
  layouts/              app, guest, rekam-medis
  components/           sidebar, topbar, badge, stat-card, tabel-kunjungan, ...
  auth/                 login
  kunjungan/            daftar kunjungan per unit
  rekam-medis/          ruang kerja pasien (asesmen, resep, surat, ...)
  akun/                 profil, inbox, pengaturan
routes/web.php          daftar halaman
```

Daftar semua halaman: `php artisan route:list`

---

## Aturan kerja tim

### Branch
| Anggota | Branch tetap | Tanggung jawab |
|---|---|---|
| A — Yesha | `yesha-emr` | Tema, layout, sidebar, template tabel, Login, Beranda, daftar Rajal/Rehab/HD, akun, dokumen formal & cetak |
| B — [nama teman] | `[nama]-emr` | JS bersama, daftar IGD/Ranap/OK, ruang kerja pasien & halaman klinis Rekam Medis |

### Alur harian
```bash
# 1. Ambil perubahan terbaru
git checkout main
git pull

# 2. Kembali ke branch sendiri dan gabungkan main
git checkout yesha-emr
git merge main

# 3. Koding, lalu simpan
git add .
git commit -m "Jelaskan apa yang diubah"
git push
```
4. Di GitHub buka **Pull Request** `yesha-emr → main`, minta anggota lain review, lalu merge.

### Aturan kode
- Warna, jarak, dan radius **hanya** dari variabel CSS di `rscc-theme.css`. Jangan menulis `style="..."` langsung.
- Class buatan sendiri diberi awalan `rs-` (contoh: `rs-card`, `rs-badge-jkn`).
- Bagian tampilan yang dipakai lebih dari sekali dijadikan **Blade component**.
- Status selalu ditulis dengan **ikon + teks**, bukan warna saja.
- Uji di lebar layar 1366 px dan 375 px (HP) sebelum membuat pull request.
- **Dilarang commit:** `.env`, data pasien asli, screenshot sistem lama.

### Format pesan commit
```
Tambah template tabel kunjungan
Perbaiki warna badge NON JKN
Desain ulang halaman login sesuai mockup
```

---

## Catatan untuk IT RSCC

1. **Data:** semua halaman mengambil data dari `app/Support/DataContoh.php`.
   Ganti isi tiap fungsi dengan query database (Eloquent/Query Builder) dengan **bentuk array yang sama**. View tidak perlu diubah.
2. **Login:** belum memeriksa akun. Lihat `TODO` di `AuthController@login`.
3. **Captcha:** masih placeholder `[KODE]` di `auth/login.blade.php`.
4. **Form Rekam Medis:** yang didesain ulang secara detail adalah kerangka ruang kerja pasien, Asesmen Gawat Darurat,
   E-Resep, CPPT/SBAR, template dokumen formal, dan template checklist.
   Form lain bisa dipindahkan dari sistem lama; class Bootstrap-nya otomatis mengikuti tema baru.
5. **Acuan desain:** kanvas mockup EMR RSCC (link dari tim magang).

---

## Status

- [x] Setup Laravel + Bootstrap + tema RSCC
- [x] Mockup 22 halaman
- [ ] Layout final + sidebar dengan sub-menu
- [ ] Daftar kunjungan semua unit
- [ ] Ruang kerja pasien (Rekam Medis)
- [ ] Dokumen formal & cetak
- [ ] Tampilan HP
- [ ] Serah terima ke IT

---

Dibuat oleh tim magang · Rumah Sakit Condong Catur · 2026