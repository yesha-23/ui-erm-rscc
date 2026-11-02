# UI EMR RS Condong Catur

Redesain tampilan **Electronic Medical Record (EMR)** Rumah Sakit Condong Catur, dibangun ulang dari nol dengan **Laravel + Bootstrap 5**.

Proyek magang (2–13 November 2026). Repo ini berisi **tampilan dan alur** saja. Semua data masih berupa **data contoh**. Database, autentikasi asli, dan logika bisnis dilanjutkan oleh **IT RSCC**.

| | |
|---|---|
| Anggota A | Yesha (`yesha-emr`) |
| Anggota B | Clara (`clara-emr`) |
| Mockup | Kanvas mockup EMR RSCC (22 halaman + 5 komponen) |
| Serah terima | Jumat, 13 November 2026 (undangan repo ke IT RSCC) |

---

## Teknologi

| Bagian | Versi / alat |
|---|---|
| Backend | Laravel, PHP 8.3 |
| Tampilan | Blade components, Bootstrap 5.3, Bootstrap Icons |
| Grafik | Chart.js |
| Build aset | Vite (Node 22) |
| Lingkungan lokal | Laragon (PHP 8.3, MySQL, phpMyAdmin) |

---

## Cara menjalankan

Prasyarat: PHP 8.3, Composer 2, Node.js 20+ dan MySQL. Di Windows, paling mudah memakai **Laragon**.

```bash
git clone https://github.com/yesha-23/ui-erm-rscc.git
cd ui-erm-rscc

composer install
npm install

cp .env.example .env          # Windows PowerShell: copy .env.example .env
php artisan key:generate
```

Atur database di `.env`, lalu buat database kosong `emr_rscc` lewat phpMyAdmin:

```env
APP_NAME="EMR RSCC"
APP_LOCALE=id
DB_CONNECTION=mysql
DB_DATABASE=emr_rscc
DB_USERNAME=root
DB_PASSWORD=
```

```bash
php artisan migrate           # tabel bawaan Laravel (session, cache)
npm run dev                   # terminal 1 — biarkan berjalan
php artisan serve             # terminal 2 → http://127.0.0.1:8000
```

Untuk produksi, ganti `npm run dev` dengan `npm run build`.

**Login:** isian apa saja diterima (data contoh). Validasi hanya memeriksa bahwa No. HP, password dan captcha tidak kosong.

> Kalau muncul error versi PHP 8.2, berarti terminal masih memakai PHP XAMPP. Jalankan perintah lewat **Terminal Laragon**, atau taruh folder PHP Laragon di urutan teratas PATH.

---

## Halaman yang tersedia

| Halaman | URL | Keterangan |
|---|---|---|
| Login | `/login` | Latar foto gedung RS |
| Beranda | `/beranda` | Sapaan, kartu angka, grafik kunjungan per unit, tugas |
| Daftar kunjungan | `/kunjungan/igd`, `/hemodialisa`, `/rehab-medik`, `/rawat-jalan` | Filter, chip status, paginasi; jadi kartu di HP |
| Rawat Inap | `/kunjungan/rawat-inap/{umum\|kebidanan\|icu\|hcu}` | Sub-menu di sidebar |
| Kamar Operasi | `/kunjungan/ok/{mayor\|minor}` | Sub-menu di sidebar |
| Profil, Inbox, Pengaturan | `/akun/profil`, `/akun/inbox`, `/akun/pengaturan` | Pengaturan tampilan disimpan di session |
| Ringkasan pasien | `/rekam-medis/{noReg}` | Ruang kerja pasien; sidebar utama otomatis mengecil |
| Surat & persetujuan | `/rekam-medis/{noReg}/dokumen/{surat\|persetujuan}/{slug?}` | 22 surat + 27 persetujuan, 5 template form |
| Cetak | `/rekam-medis/{noReg}/cetak` | Resume medis + dokumen kunjungan |
| Lembar cetak A4 | `.../dokumen/{jenis}/{slug}/cetak`, `.../cetak/resume-medis` | Kop resmi RS; `?draf=1` menambah cap DRAF |
| Permintaan lab / radiologi | `/rekam-medis/{noReg}/permintaan/{lab\|radiologi}` | Katalog berkelompok, cari, prioritas Cito/Rutin |
| Berkas digital | `/rekam-medis/{noReg}/berkas` | Daftar berkas + popup unggah tarik-lepas |

Contoh No. Reg untuk dicoba: `2611020001` sampai `2611020012`.

Daftar lengkap route: `php artisan route:list`.

---

## Struktur folder

```
app/Http/Controllers/   Auth, Beranda, Kunjungan, Akun, RekamMedis, Dokumen,
                        Cetak, Permintaan, Berkas
app/Support/            DataContoh        → semua data contoh (diganti query oleh IT)
                        Navigasi          → isi menu sidebar
                        KonfigurasiUnit   → kolom, tab dan filter per unit
                        KatalogDokumen    → daftar surat & persetujuan
                        KatalogPenunjang  → daftar pemeriksaan lab & radiologi
config/rscc.php         identitas RS untuk kop surat (bisa ditimpa lewat .env)
public/images/          logo-rscc.png, logo-kop-rscc.png, gedung-rscc.webp/.jpg
resources/css/          app.css (impor Bootstrap + tema), rscc-theme.css, cetak.css
resources/js/           app.js (titik masuk), rscc.js (semua fungsi interaktif)
resources/views/
  layouts/              app, guest, rekam-medis, cetak
  components/           sidebar, topbar, search, badge, stat-card, flash,
                        tabel-kunjungan, filter-kunjungan, tab-status, status-cek,
                        pasien-header, menu-rm, kop-surat
  auth/ beranda kunjungan/ akun/ cetak/
  rekam-medis/          show, dokumen (+ dokumen/*), cetak, permintaan, berkas
routes/web.php
```

---

## Pedoman tampilan (wajib diikuti saat menambah halaman)

**Warna dan ukuran.** Ambil dari variabel CSS di bagian atas `rscc-theme.css`, misalnya `var(--rs-primary)`. Jangan menulis kode warna langsung di halaman. Class buatan sendiri diberi awalan `rs-`.

| Kebutuhan | Pakai |
|---|---|
| Kotak cari | `<x-search label="Cari pasien" name="q" placeholder="..." />` |
| Kartu / panel | `<section class="rs-card">` |
| Judul halaman | `<p class="rs-eyebrow">Kelompok</p>` + `<h2 class="h4 fw-bold">Judul</h2>` |
| Tombol simpan form panjang | `.rs-action-bar` di **luar** `<form>`, dengan tombol `form="id-form"` (contoh: `rekam-medis/dokumen.blade.php`) |
| Tabel yang jadi kartu di HP | `<table class="table rs-table rs-table-kartu">`, setiap `<td data-label="Judul kolom">`, kolom utama `rs-td-utama`, kolom tombol `rs-td-aksi` |
| Menu yang belum dibuat | Di `components/menu-rm.blade.php` isi `'href' => '#'`; label **Segera** muncul otomatis |
| Popup (modal) | `@push('modal') ... @endpush`, karena layout menyediakan `@stack('modal')` sebelum `</body>` |
| Lembar cetak | `@extends('layouts.cetak')` + `@include('cetak._tanda-tangan', [...])` |
| Notifikasi berhasil | `return redirect()->...->with('sukses', 'Pesan')` |
| Status | Selalu teks + ikon, jangan hanya warna |

Sebelum membuat pull request, uji di lebar **1366 px**, **768 px** dan **390 px** (F12 → Ctrl+Shift+M).

---

## Catatan untuk IT RSCC

Data contoh **seluruhnya** ada di `app/Support/DataContoh.php`. Bentuk array-nya sudah disamakan dengan yang dipakai view, sehingga cukup diganti query tanpa mengubah tampilan. Cari komentar `TODO (IT RSCC)` di kode untuk melihat titik-titik yang perlu disambungkan.

| Bagian | File | Yang perlu dilanjutkan |
|---|---|---|
| Login | `AuthController` | Autentikasi asli (No. HP + password), captcha, logout, middleware `auth` di semua route |
| Nama pengguna | `components/topbar`, `layouts/cetak`, beberapa controller | Ganti `[Nama Pengguna]` / `Zaki Kurnia...` dengan `auth()->user()` |
| Beranda | `BerandaController` | Angka ringkasan, data grafik, daftar tugas |
| Daftar kunjungan | `KunjunganController`, `KonfigurasiUnit` | Query kunjungan per unit; filter, pencarian dan paginasi sudah siap (`LengthAwarePaginator`); tombol Ekspor |
| Akun | `AkunController` | Simpan password & pengaturan tampilan ke database (sekarang di session), isi Inbox |
| Surat & persetujuan | `DokumenController`, `KatalogDokumen` | Simpan draf/final, isi form per dokumen, tanda tangan elektronik |
| Cetak | `CetakController`, `DataContoh::isiDokumen()`, `resumeMedis()` | Ambil isi dari data tersimpan; penomoran surat; QR/TTE di kolom tanda tangan |
| Permintaan lab/radiologi | `PermintaanController`, `KatalogPenunjang` | Master pemeriksaan dari LIS/RIS, simpan & kirim permintaan (sekarang simulasi di session) |
| Berkas digital | `BerkasController` | Simpan fisik berkas (mis. `$file->store("berkas/$noReg")`), route Lihat & Unduh, hak akses |
| Identitas RS | `config/rscc.php` | Bisa ditimpa lewat `.env`: `RS_ALAMAT`, `RS_TELEPON`, `RS_EMAIL`, dll. |
| Server | `php.ini` | `upload_max_filesize = 10M`, `post_max_size = 110M` untuk unggah berkas |

Form Rekam Medis yang **tidak** didesain ulang satu per satu (±115 form di sistem lama) cukup dipindahkan ke dalam `layouts/rekam-medis`. Input, tabel, radio dan judulnya otomatis mengikuti tema lewat `rscc-theme.css`. Perbaikan tata letak per form menjadi pekerjaan lanjutan.

---

## Alur kerja Git

```bash
# Sebelum mulai bekerja
git checkout main
git pull
git checkout yesha-emr        # atau clara-emr
git merge main

# Setelah selesai satu bagian
git add .
git commit -m "Pesan yang jelas"
git push
```

Gabungkan ke `main` lewat **pull request**, lalu minta anggota lain mengecek.

---

## Catatan untuk Anggota B (Clara)

Sebelum membuat halaman klinis (Asesmen, CPPT/SBAR, E-Resep, Riwayat, Checklist):

1. Buat branch: `git checkout main && git pull && git checkout -b clara-emr`.
2. Baca bagian **Pedoman tampilan** di atas, karena semua pola komponen sudah tersedia.
3. Halamanmu berada di dalam `layouts.rekam-medis`. Kirim `pasien`, `crumb` dan `menuAktif` dari controller (contoh: `PermintaanController::index`).
4. Setelah halaman jadi, ganti `'#'` di `components/menu-rm.blade.php` dengan `route(...)` supaya label "Segera" hilang.
5. Data contoh ditambahkan di `DataContoh.php`.

---

## Privasi

- Repo ini **hanya** berisi data contoh (nama dan No. RM palsu).
- **Jangan** commit `.env`, data pasien asli, screenshot sistem lama, atau berkas uji unggahan.
- Saat menguji unggah berkas, gunakan dokumen palsu.