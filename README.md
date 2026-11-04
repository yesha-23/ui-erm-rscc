# UI EMR RS Condong Catur

Redesain tampilan **Electronic Medical Record (EMR)** Rumah Sakit Condong Catur, dibangun ulang dari nol dengan **Laravel + Bootstrap 5**.

Proyek magang 2–13 November 2026. Repo ini berisi **tampilan dan alur**. Semua data masih **data contoh**, dan proses simpan disimulasikan di session. Database, autentikasi asli, dan logika bisnis dilanjutkan oleh **IT RSCC**.

| | |
|---|---|
| Pengembang | Yesha (`yesha-emr`) · Clara (`clara-emr`) |
| Mockup | Kanvas mockup EMR RSCC (22 halaman + 5 komponen) |
| Serah terima | Jumat, 13 November 2026 (undangan repo ke IT RSCC) |

---

## Teknologi

| Bagian | Versi / alat |
|---|---|
| Backend | Laravel, PHP 8.3 |
| Tampilan | Blade components, Bootstrap 5.3, Bootstrap Icons |
| Grafik | Chart.js |
| Build aset | Vite (Node 20+) |
| Lingkungan lokal | Laragon atau PHP 8.3 + MySQL |

---

## Cara menjalankan

Prasyarat: PHP 8.3 (ekstensi `curl`, `fileinfo`, `mbstring`, `openssl`, `pdo_mysql`, `zip`), Composer 2, Node.js 20+, dan MySQL.

```bash
git clone https://github.com/yesha-23/ui-erm-rscc.git
cd ui-erm-rscc

composer install
npm install

cp .env.example .env          # Windows: copy .env.example .env
php artisan key:generate
```

Atur database di `.env`:

```env
APP_NAME="EMR RSCC"
APP_LOCALE=id
DB_CONNECTION=mysql
DB_DATABASE=emr_rscc
DB_USERNAME=root
DB_PASSWORD=
```

```bash
php artisan migrate           # tabel bawaan Laravel (session, cache); jawab "yes" bila diminta membuat database
npm run dev                   # terminal 1, biarkan berjalan
php artisan serve             # terminal 2 → http://127.0.0.1:8000
```

Untuk produksi, ganti `npm run dev` dengan `npm run build`.

**Login:** isian apa saja diterima (data contoh). Contoh pasien: buka **IGD**, lalu klik **Buka** pada **Andi Pratama** (No. Reg `2611020001`). No. Reg contoh: `2611020001` sampai `2611020012`.

---

## Halaman yang tersedia

### Halaman umum

| Halaman | URL |
|---|---|
| Login | `/login` |
| Beranda | `/beranda` |
| Daftar kunjungan | `/kunjungan/igd`, `/hemodialisa`, `/rehab-medik`, `/rawat-jalan` |
| Rawat Inap | `/kunjungan/rawat-inap/{umum\|kebidanan\|icu\|hcu}` |
| Kamar Operasi | `/kunjungan/ok/{mayor\|minor}` |
| Profil, Inbox, Pengaturan | `/akun/profil`, `/akun/inbox`, `/akun/pengaturan` |

### Ruang kerja pasien: `/rekam-medis/{noReg}/…`

| Kelompok menu | Halaman | URL | Keterangan |
|---|---|---|---|
| Asesmen & pemeriksaan | Ringkasan pasien | `/` | Halaman pertama ruang kerja pasien |
| | Asesmen awal GD | `/asesmen` | 4 langkah, simpan draf / final |
| | Tanda vital | `/ttv` | Ringkasan, grafik tren, observasi baru |
| Catatan terintegrasi | CPPT / SBAR | `/cppt` | Format SOAP / SBAR, verifikasi DPJP |
| Penunjang | Permintaan lab / radiologi | `/permintaan/{lab\|radiologi}` | Katalog, prioritas Cito / Rutin |
| | Berkas digital | `/berkas` | Popup unggah tarik-lepas |
| Farmasi | E-Resep | `/resep` | Cari obat, salin resep, riwayat pemberian obat |
| Riwayat | Riwayat pemeriksaan | `/riwayat` | Linimasa, hasil lab |
| Dokumen | Surat & persetujuan | `/dokumen/{surat\|persetujuan}/{slug?}` | 22 surat + 27 persetujuan |
| | Cetak | `/cetak` | Lembar A4 dengan kop RS, cap DRAF |
| Mutu & lainnya | Checklist | `/checklist/{ppi\|cp\|psikologi}/{slug?}` | 17 form, skor kepatuhan otomatis |

**Cari menu:** tekan **Ctrl+K** (⌘K di Mac) dari halaman mana pun.

Daftar lengkap route: `php artisan route:list`.

---

## Struktur folder

```
app/Http/Controllers/   Auth, Beranda, Kunjungan, Akun, RekamMedis, Dokumen, Cetak,
                        Permintaan, Berkas, Asesmen, Ttv, Cppt, Resep, Riwayat, Checklist
app/Support/            DataContoh        → semua data contoh (diganti query oleh IT)
                        Navigasi          → isi sidebar utama
                        KonfigurasiUnit   → kolom, tab, filter per unit
                        MenuRekamMedis    → isi menu rekam medis (dipakai menu samping & Ctrl+K)
                        KatalogDokumen    → daftar surat & persetujuan
                        KatalogPenunjang  → daftar pemeriksaan lab & radiologi
                        KatalogChecklist  → isi 17 form checklist PPI / CP / Psikologi
                        RentangTtv        → rentang normal tanda vital (satu sumber)
config/rscc.php         identitas RS untuk kop surat
public/images/          logo-rscc.png, logo-kop-rscc.png, gedung-rscc.webp/.jpg
resources/css/          app.css, rscc-theme.css, cetak.css
resources/js/           app.js (memanggil semua fungsi), rscc.js (fungsi interaktif)
resources/views/
  layouts/              app, guest, rekam-medis, cetak
  components/           sidebar, topbar, search, badge, stat-card, flash, tabel-kunjungan,
                        filter-kunjungan, tab-status, status-cek, pasien-header, menu-rm,
                        cari-menu, kop-surat
  components/form/      pilihan, ttv, gcs, skala-nyeri, fisik   ← komponen form klinis
  auth/ beranda kunjungan/ akun/ cetak/
  rekam-medis/          show, dokumen (+ dokumen/*), cetak, permintaan, berkas, asesmen,
                        ttv, cppt, resep (+ resep/_baris), riwayat, checklist
routes/web.php
```

---

## Pedoman tampilan

**Warna dan ukuran.** Ambil dari variabel CSS di bagian atas `rscc-theme.css`, misalnya `var(--rs-primary)`. Class buatan sendiri diberi awalan `rs-`. CSS baru ditaruh sebelum bagian `/* ---------- Layar HP ---------- */`.

| Kebutuhan | Pakai |
|---|---|
| Kotak cari | `<x-search label="…" placeholder="…" />` |
| Kartu / panel | `<section class="rs-card">` |
| Judul halaman | `<p class="rs-eyebrow">Kelompok</p>` + `<h2 class="h4 fw-bold">Judul</h2>` |
| Tombol simpan form panjang | `.rs-action-bar` di **luar** `<form>`, dengan tombol `form="id-form"` |
| Pilihan berbentuk chip | `<x-form.pilihan name="…" label="…" :opsi="[…]" :nilai="…" />` (tambah `tipe="checkbox"` untuk pilihan banyak) |
| Tanda vital | `<x-form.ttv :isi="$isi" />` (rentang normal dari `RentangTtv`) |
| GCS / skala nyeri / pemeriksaan fisik | `<x-form.gcs>`, `<x-form.skala-nyeri>`, `<x-form.fisik>` |
| Tabel yang jadi kartu di HP | `table.rs-table-kartu`, setiap `<td data-label="…">`, kolom utama `rs-td-utama`, kolom tombol `rs-td-aksi` |
| Menu rekam medis baru | Tambah item di `app/Support/MenuRekamMedis.php`. `'href' => '#'` menampilkan label "Segera" |
| Popup (modal) | `@push('modal') … @endpush` |
| Lembar cetak | `@extends('layouts.cetak')` + `@include('cetak._tanda-tangan', […])` |
| Notifikasi berhasil | `->with('sukses', 'Pesan')` |
| Status | Selalu teks + ikon, jangan hanya warna |

Uji di lebar **1366 px**, **768 px**, dan **390 px** (F12 → Ctrl+Shift+M) sebelum membuat pull request.

---

## Catatan untuk IT RSCC

Semua data contoh ada di `app/Support/DataContoh.php`. Bentuk array-nya sama dengan yang dipakai view, jadi cukup diganti query tanpa mengubah tampilan. Proses simpan saat ini memakai **session** sebagai simulasi. Cari komentar **`TODO (IT RSCC)`** untuk melihat titik-titik yang perlu disambungkan.

| Bagian | File | Yang perlu dilanjutkan |
|---|---|---|
| Login | `AuthController` | Autentikasi asli, captcha, logout, middleware `auth` di semua route |
| Nama pengguna | `topbar`, `layouts/cetak`, beberapa controller | Ganti `[Nama Pengguna]` / `[Nama PPA]` dengan `auth()->user()` |
| Beranda | `BerandaController` | Angka ringkasan, data grafik, daftar tugas |
| Daftar kunjungan | `KunjunganController`, `KonfigurasiUnit` | Query kunjungan per unit; tombol Ekspor |
| Akun | `AkunController` | Simpan password & pengaturan ke database, isi Inbox |
| Surat & persetujuan | `DokumenController`, `KatalogDokumen` | Simpan draf / final, isi per dokumen, tanda tangan elektronik |
| Cetak | `CetakController`, `DataContoh::isiDokumen()`, `resumeMedis()` | Isi dari data tersimpan, penomoran surat, QR/TTE |
| Permintaan lab / radiologi | `PermintaanController`, `KatalogPenunjang` | Master pemeriksaan dari LIS/RIS, simpan & kirim |
| Berkas digital | `BerkasController` | Simpan fisik berkas, route Lihat & Unduh, hak akses |
| Asesmen awal GD | `AsesmenController`, `DataContoh::opsiAsesmenGd()` | Tabel asesmen (draf / final), perawat dari akun login |
| Tanda vital | `TtvController`, `RentangTtv` | Tabel observasi TTV; rentang normal sesuai SPO RS |
| CPPT / SBAR | `CpptController` | Tabel CPPT; **hanya DPJP** pasien yang boleh memverifikasi |
| E-Resep | `ResepController`, `DataContoh::katalogObat()` | Master obat & stok farmasi, simpan resep, kirim ke depo farmasi |
| Riwayat pemeriksaan | `RiwayatController` | Gabungan data kunjungan, lab, radiologi, EKG, CPPT, surat kontrol |
| Checklist | `ChecklistController`, `KatalogChecklist` | Simpan jawaban; isi butir disesuaikan dengan Komite PPI / Komite Medik |
| Identitas RS | `config/rscc.php` | Bisa ditimpa lewat `.env` (`RS_ALAMAT`, `RS_TELEPON`, `RS_EMAIL`, …) |
| Server | `php.ini` | `upload_max_filesize = 10M`, `post_max_size = 110M` |

### Melanjutkan form rekam medis lain (±27 form)

Sistem lama punya sekitar 28 form rekam medis. Yang sudah dibuat sebagai contoh lengkap adalah **Asesmen Awal Gawat Darurat** dan **Tanda vital**. Untuk form lain:

1. Buat controller + view di `rekam-medis/`. Contoh untuk ditiru: `AsesmenController` + `rekam-medis/asesmen.blade.php`.
2. Susun isian dari komponen `components/form/*` (pilihan, ttv, gcs, skala-nyeri, fisik), lalu tambahkan komponen baru bila perlu.
3. Daftarkan route di grup `rekam-medis/{noReg}`, lalu tambahkan item menu di `app/Support/MenuRekamMedis.php`. Form baru otomatis muncul di menu samping dan di Ctrl+K.

Form yang tidak didesain ulang tetap bisa dipindahkan apa adanya ke dalam `layouts/rekam-medis`. Input, tabel, dan judulnya otomatis mengikuti tema lewat `rscc-theme.css`.

Halaman **i-Care** belum dikerjakan karena tidak ada contoh tampilannya.

---

## Alur kerja Git

```bash
# Sebelum mulai bekerja
git checkout main
git pull
git checkout nama-branch
git merge main

# Setelah selesai satu fitur
git add .
git commit -m "Pesan yang jelas"
git push
```

Gabungkan ke `main` lewat **pull request** yang dicek anggota lain.

---

## Privasi

- Repo ini **hanya** berisi data contoh (nama dan No. RM palsu).
- **Jangan** commit `.env`, data pasien asli, screenshot sistem lama, atau berkas uji unggahan.