<?php

namespace App\Support;

/**
 * Data contoh untuk tampilan (BUKAN data pasien asli).
 * Catatan untuk IT RSCC: ganti isi tiap fungsi dengan query database
 * dengan bentuk array yang sama, tampilan tidak perlu diubah.
 */
class DataContoh
{
    public static function ringkasan(): array
    {
        return [
            'kunjungan'   => 284,
            'rawat_jalan' => 283,
            'rawat_inap'  => 1,
        ];
    }

    public static function kunjunganPerUnit(): array
    {
        return [
            ['unit' => 'Klinik Penyakit Dalam',     'jumlah' => 44],
            ['unit' => 'Klinik Syaraf',             'jumlah' => 29],
            ['unit' => 'Klinik Gigi dan Mulut',     'jumlah' => 29],
            ['unit' => 'Fisioterapi',               'jumlah' => 25],
            ['unit' => 'Laboratorium',              'jumlah' => 24],
            ['unit' => 'IGD',                       'jumlah' => 23],
            ['unit' => 'Klinik Mata',               'jumlah' => 23],
            ['unit' => 'Klinik Kesehatan Jiwa',     'jumlah' => 20],
            ['unit' => 'Terapi Wicara',             'jumlah' => 20],
            ['unit' => 'Klinik Rehabilitasi Medik', 'jumlah' => 18],
            ['unit' => 'Hemodialisa',               'jumlah' => 17],
            ['unit' => 'Klinik Anak',               'jumlah' => 12],
            ['unit' => 'Okupasi Terapi',            'jumlah' => 11],
        ];
    }

    /**
     * Pekerjaan yang belum selesai hari ini (tampil di Beranda).
     * 'query' memakai nama tab di KonfigurasiUnit, jadi klik langsung membuka tab yang sesuai.
     */
    public static function tugas(): array
    {
        return [
            ['label' => 'Pasien IGD belum close',          'jumlah' => 8, 'ikon' => 'bi-activity',
             'route' => 'kunjungan.igd',   'query' => ['status' => 'belum_close']],
            ['label' => 'BRM rawat jalan belum terisi',    'jumlah' => 3, 'ikon' => 'bi-file-earmark-medical',
             'route' => 'kunjungan.rajal', 'query' => ['status' => 'brm']],
            ['label' => 'BRM hemodialisa belum terisi',    'jumlah' => 3, 'ikon' => 'bi-droplet',
             'route' => 'kunjungan.hd',    'query' => ['status' => 'brm']],
            ['label' => 'Pasien rawat inap tanpa diagnosa', 'jumlah' => 2, 'ikon' => 'bi-clipboard2-x',
             'route' => 'kunjungan.ranap', 'query' => ['status' => 'tanpa_diagnosa']],
        ];
    }

    /** Pengguna yang sedang login. Catatan untuk IT: ganti dengan auth()->user(). */
    public static function pengguna(): array
    {
        return [
            'nama'        => 'Zaki Kurnia Bhaskara, S.Kom',
            'nama_pendek' => 'Zaki Kurnia B., S.Kom',
            'panggilan'   => 'Zaki',
            'inisial'     => 'ZK',
            'jabatan'     => 'Admin IT',
            'unit'        => 'Teknologi Informasi',
            'nip'         => '[NIP]',
            'no_hp'       => '08xx-xxxx-xxxx',
            'email'       => '[email]@rscc.biz.id',
            'akses'       => 'Semua unit',
        ];
    }

    public static function riwayatMasuk(): array
    {
        return [
            ['waktu' => '02 Nov 2026 · 08:47', 'perangkat' => 'Chrome · Windows', 'berhasil' => true],
            ['waktu' => '30 Okt 2026 · 14:52', 'perangkat' => 'Chrome · Windows', 'berhasil' => true],
            ['waktu' => '30 Okt 2026 · 14:50', 'perangkat' => 'Chrome · Windows', 'berhasil' => false, 'alasan' => 'Captcha salah'],
            ['waktu' => '29 Okt 2026 · 07:58', 'perangkat' => 'Chrome · Android',  'berhasil' => true],
        ];
    }

    /** Pesan di Inbox. Catatan untuk IT: ganti dengan tabel pesan/notifikasi. */
    public static function pesan(): array
    {
        return [
            [
                'id' => 1, 'dari' => 'IT RSCC', 'inisial' => 'IT', 'kategori' => 'Pengumuman',
                'judul' => 'Pemeliharaan server EMR', 'waktu' => '02 Nov · 09:15', 'dibaca' => false,
                'ringkas' => 'EMR tidak dapat diakses Sabtu, 7 Nov 2026 pukul 22.00–24.00.',
                'isi' => "Yth. seluruh pengguna EMR,\n\nAkan dilakukan pemeliharaan server pada Sabtu, 7 November 2026 pukul 22.00–24.00 WIB. Selama waktu tersebut EMR tidak dapat diakses.\n\nMohon simpan pekerjaan sebelum pukul 21.45.\n\nTerima kasih,\nIT RSCC",
            ],
            [
                'id' => 2, 'dari' => 'Unit Rekam Medis', 'inisial' => 'RM', 'kategori' => 'Tugas',
                'judul' => '3 BRM rawat jalan belum lengkap', 'waktu' => '02 Nov · 08:30', 'dibaca' => false,
                'ringkas' => 'Mohon dilengkapi sebelum pukul 16.00 hari ini.',
                'isi' => "Terdapat 3 berkas rekam medis rawat jalan hari ini yang belum lengkap.\n\nDaftarnya bisa dilihat di menu Rawat Jalan, tab \"BRM belum terisi\". Mohon dilengkapi sebelum pukul 16.00.\n\nUnit Rekam Medis",
            ],
            [
                'id' => 3, 'dari' => 'Komite Mutu', 'inisial' => 'KM', 'kategori' => 'Pengumuman',
                'judul' => 'Pengisian bundles PPI bulan November', 'waktu' => '01 Nov · 13:05', 'dibaca' => false,
                'ringkas' => 'Checklist PPI kini tersedia di menu Mutu pada ruang kerja pasien.',
                'isi' => "Mulai November, checklist bundles PPI (IP, CAUTI, IDO, VAP, IADP) diisi langsung di EMR melalui menu Mutu pada ruang kerja pasien.\n\nKomite Mutu",
            ],
            [
                'id' => 4, 'dari' => 'IT RSCC', 'inisial' => 'IT', 'kategori' => 'Info',
                'judul' => 'Akun Anda berhasil dibuat', 'waktu' => '28 Okt · 10:00', 'dibaca' => true,
                'ringkas' => 'Selamat datang di EMR RSCC.',
                'isi' => "Selamat datang di EMR RSCC.\n\nGunakan nomor HP yang terdaftar untuk masuk. Segera ganti password di menu Pengaturan.\n\nIT RSCC",
            ],
        ];
    }

        /**
     * Data kunjungan per unit (data contoh, 12 baris).
     * Catatan untuk IT: ganti dengan query kunjungan per unit, dengan nama kunci yang sama.
     */
    public static function kunjungan(string $unit): array
    {
        $pasien = [
            ['Andi Pratama', '000123'], ['Siti Rahmawati', '000124'], ['Budi Santoso', '000131'],
            ['Dewi Lestari', '000142'], ['Hendra Wijaya', '000157'], ['Putri Maharani', '000163'],
            ['Rudi Hartono', '000170'], ['Nur Aisyah', '000181'], ['Agus Salim', '000195'],
            ['Rina Kartika', '000203'], ['Eko Prasetyo', '000214'], ['Lina Marlina', '000226'],
        ];
        $dokter  = ['dr. Arif Nugroho', 'dr. Laras Wulandari', 'dr. Rizky Ananda'];
        $perawat = ['Bagus Setiawan, A.Md.Kep', 'Fitri Handayani, A.Md.Kep', 'Maya Lestari, AMK'];
        $terapis = ['Citra Ayu, S.Tr.Kes', 'Rina Marlina, A.Md.TW', 'Sari Utami, A.Md.OT'];
        $tujuan  = [
            'igd'   => ['IGD', 'IGD, Laboratorium', 'IGD, Radiologi'],
            'hd'    => ['Hemodialisa', 'Laboratorium, Hemodialisa'],
            'rm'    => ['Okupasi Terapi', 'Terapi Wicara', 'Klinik Rehabilitasi Medik'],
            'rajal' => ['Klinik Penyakit Dalam', 'Klinik Syaraf', 'Klinik Gigi dan Mulut', 'Klinik Mata'],
        ];
        $ruang    = [
            ['Kenanga I', 'Kenanga I-C', 'Kelas III'], ['Lily II (Isolasi)', 'Lily II-A', 'Kelas I'],
            ['Mawar', 'Mawar-D', 'Kelas II'], ['Anggrek IV', 'Anggrek IV', 'VIP'],
        ];
        $diagnosa = ['Demam tifoid', 'Pneumonia', 'Selulitis pedis', 'Dispepsia', ''];
        $daftarTujuan = $tujuan[$unit] ?? ['—'];

        $hasil = [];
        foreach ($pasien as $i => [$nama, $rm]) {
            $hasil[] = [
                'no_rm'        => $rm,
                'nama'         => $nama,
                'no_reg'       => '26110200' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'no_sep'       => $i % 4 === 2 ? '' : '0179R01211260' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'tgl'          => '02 Nov 2026',
                'jam'          => sprintf('%02d:%02d', 7 + $i, ($i * 13) % 60),
                'penjamin'     => $i % 5 === 3 ? 'NON JKN' : 'JKN',
                'sudah_datang' => $i % 6 !== 4,
                'terbayar'     => $i % 3 === 0,
                'unit_tujuan'  => $daftarTujuan[$i % count($daftarTujuan)],
                'brm'          => $i % 4 !== 3,
                'close'        => $i % 3 === 0,
                'rujukan'      => $i % 5 === 0,
                'kontrol'      => $i % 4 === 1,
                'dokter_isi'   => $dokter[$i % 3],
                'dokter_pulang'=> $i % 3 === 0 ? $dokter[$i % 3] : '',
                'perawat'      => $perawat[$i % 3],
                'ppa1'         => in_array($unit, ['rm', 'rajal']) ? $terapis[$i % 3] : $dokter[$i % 3],
                'ppa2'         => $perawat[($i + 1) % 3],
                'ppa_farmasi'  => '',
                'kelas'        => $ruang[$i % 4][2],
                'ruang'        => $ruang[$i % 4][0],
                'bed'          => $ruang[$i % 4][1],
                'dpjp'         => $dokter[$i % 3] . ', Sp.PD',
                'diagnosa'     => $diagnosa[$i % 5],
                'unit_asal'    => $i % 3 ? 'UGD' : 'Poliklinik',
            ];
        }

        return $hasil;
    }

    /** Data satu pasien untuk ruang kerja Rekam Medis. Catatan untuk IT: ganti dengan query pasien + kunjungan. */
    public static function pasien(string $noReg): array
    {
        $daftar = self::kunjungan('igd');
        $row = collect($daftar)->firstWhere('no_reg', $noReg) ?? $daftar[0];

        return $row + [
            'jk'             => 'Laki-laki',
            'umur'           => '21 th',
            'tgl_lahir'      => '[tgl lahir]',
            'alamat'         => '[alamat pasien]',
            'alergi'         => '[isi alergi]',
            'unit'           => 'IGD',
            'unit_route'     => 'kunjungan.igd',
            'datang'         => '02 Nov 2026, 08:50',
            'dpjp_nama'      => 'dr. [Nama Dokter]',
            'diagnosa_kerja' => 'Obs. febris H6, susp. demam tifoid',
        ];
    }

    /**
     * Isi contoh dokumen untuk pratinjau cetak.
     * IT RSCC: ganti dengan data draf/final yang tersimpan di database.
     */
    public static function isiDokumen(array $dokumen, array $pasien): array
    {
        $hari  = now()->locale('id');
        $tgl   = fn ($c) => $c->translatedFormat('d F Y');
        $kode  = collect(explode('-', $dokumen['slug']))->map(fn ($s) => strtoupper($s[0]))->join('');
        $dokter = [['Dokter pemeriksa', $pasien['dpjp_nama'], 'SIP. [No. SIP]']];

        $isi = match ($dokumen['form']) {
            'sakit' => [
                'pembuka'   => 'Yang bertanda tangan di bawah ini, dokter ' . config('rscc.nama') . ', menerangkan bahwa:',
                'pengantar' => 'Berdasarkan hasil pemeriksaan, pasien tersebut perlu beristirahat karena sakit:',
                'rincian'   => [
                    'Diagnosa'       => $pasien['diagnosa_kerja'],
                    'Lama istirahat' => '3 (tiga) hari',
                    'Terhitung'      => $tgl($hari) . ' s.d. ' . $tgl($hari->copy()->addDays(2)),
                ],
                'penutup'       => 'Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.',
                'penandatangan' => $dokter,
            ],
            'sehat' => [
                'pembuka'   => 'Yang bertanda tangan di bawah ini, dokter ' . config('rscc.nama') . ', menerangkan bahwa:',
                'pengantar' => 'Telah dilakukan pemeriksaan kesehatan dengan hasil sebagai berikut:',
                'rincian'   => [
                    'Tinggi / berat badan' => '165 cm / 60 kg',
                    'Tekanan darah'        => '120/80 mmHg',
                    'Buta warna'           => 'Tidak',
                    'Kesimpulan'           => 'SEHAT',
                    'Keperluan'            => 'Melamar pekerjaan',
                ],
                'penutup'       => 'Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.',
                'penandatangan' => $dokter,
            ],
            'rujukan' => [
                'kepada'    => ['Yth. Dokter Spesialis Penyakit Dalam', '[Nama RS / faskes tujuan]', 'di Tempat'],
                'pembuka'   => 'Dengan hormat, mohon pemeriksaan dan penanganan lebih lanjut terhadap pasien:',
                'pengantar' => 'Dengan keterangan sebagai berikut:',
                'rincian'   => [
                    'Poli / spesialis tujuan' => 'Penyakit Dalam',
                    'Diagnosa'                => $pasien['diagnosa_kerja'],
                    'Alasan dirujuk'          => 'Memerlukan pemeriksaan dan penanganan lanjutan',
                    'Terapi yang diberikan'   => 'Parasetamol infus 1 g, Omeprazole inj 40 mg',
                ],
                'penutup'       => 'Atas kerja sama dan bantuannya kami ucapkan terima kasih.',
                'penandatangan' => $dokter,
            ],
            'persetujuan' => [
                'pembuka'   => 'Saya yang bertanda tangan di bawah ini, selaku pasien / wali pasien:',
                'pengantar' => 'Menyatakan telah menerima penjelasan dan memahami hal-hal berikut:',
                'rincian'   => [
                    'Dokumen'                 => $dokumen['nama'],
                    'Dokter yang menjelaskan' => $pasien['dpjp_nama'],
                    'Penjelasan diterima'     => 'Diagnosis, tujuan tindakan, risiko dan komplikasi, alternatif, serta prognosis',
                    'Keputusan'               => 'SETUJU',
                ],
                'penutup'       => 'Pernyataan ini saya buat dengan penuh kesadaran dan tanpa paksaan dari pihak mana pun.',
                'penandatangan' => [
                    ['Yang menyatakan', $pasien['nama']],
                    ['Saksi', '[Nama Saksi]'],
                    ['Petugas yang menjelaskan', '[Nama Petugas]'],
                ],
            ],
            default => [
                'pembuka'       => 'Dengan ini menerangkan bahwa:',
                'pengantar'     => null,
                'rincian'       => ['Keterangan' => '[Isi keterangan sesuai dokumen]'],
                'penutup'       => 'Demikian dokumen ini dibuat untuk dipergunakan sebagaimana mestinya.',
                'penandatangan' => $dokter,
            ],
        };

        return $isi + ['nomor' => sprintf('017/%s/RSCC/%s', $kode, $hari->format('m/Y'))];
    }

    /** Isi contoh resume medis. IT RSCC: ganti dengan query asesmen, CPPT, resep, dll. */
    public static function resumeMedis(array $pasien): array
    {
        return [
            'Keluhan utama'             => 'Demam 6 hari, mual, nyeri ulu hati.',
            'Riwayat penyakit sekarang' => 'Demam naik-turun terutama sore/malam, nafsu makan menurun, BAB belum 2 hari.',
            'Pemeriksaan fisik'         => 'KU sedang, compos mentis. TD 120/70 mmHg, N 102 x/mnt, RR 22 x/mnt, S 40,1 °C, SpO₂ 98 %. Abdomen: nyeri tekan epigastrium.',
            'Pemeriksaan penunjang'     => 'Trombosit 145.000/µl (↓), Neutrofil 73,9 % (↑), Widal S. typhi H 1/80.',
            'Diagnosa utama'            => $pasien['diagnosa_kerja'],
            'Diagnosa sekunder'         => 'Dehidrasi ringan–sedang',
            'Tindakan'                  => 'Pemasangan infus, pengambilan sampel darah',
            'Terapi'                    => 'Parasetamol infus 1 g, Omeprazole inj 40 mg, Ceftriaxon inj 1 g',
            'Kondisi saat keluar'       => 'Membaik, dipindahkan ke rawat inap',
            'Instruksi tindak lanjut'   => 'Rawat inap di bawah DPJP Penyakit Dalam',
        ];
    }

    /** Dokumen yang sudah dibuat pada kunjungan ini (untuk halaman Cetak). */
    public static function dokumenTersimpan(): array
    {
        return [
            ['nama' => 'General Consent', 'jenis' => 'persetujuan', 'slug' => 'general-consent',
             'waktu' => '02 Nov 2026, 08:55', 'oleh' => '[Nama Petugas]', 'final' => true],
            ['nama' => 'Surat Sakit', 'jenis' => 'surat', 'slug' => 'surat-sakit',
             'waktu' => '02 Nov 2026, 10:30', 'oleh' => 'dr. [Nama Dokter]', 'final' => true],
            ['nama' => 'Surat Rujukan Keluar', 'jenis' => 'surat', 'slug' => 'surat-rujukan-keluar',
             'waktu' => '02 Nov 2026, 11:02', 'oleh' => 'dr. [Nama Dokter]', 'final' => false],
        ];
    }

    /** Permintaan penunjang yang sudah ada pada kunjungan ini. IT RSCC: ganti dengan query. */
    public static function permintaanPenunjang(): array
    {
        return [
            ['waktu' => '02 Nov 2026, 09:12', 'jenis' => 'radiologi', 'items' => ['Thorax PA'],
             'prioritas' => 'rutin', 'status' => 'proses', 'oleh' => 'dr. [Nama Dokter]'],
            ['waktu' => '02 Nov 2026, 09:05', 'jenis' => 'lab', 'items' => ['Darah rutin', 'Widal', 'NS1 Dengue'],
             'prioritas' => 'cito', 'status' => 'hasil', 'oleh' => 'dr. [Nama Dokter]'],
        ];
    }

    /** Berkas digital yang sudah ada pada pasien ini. IT RSCC: ganti dengan query tabel berkas. */
    public static function berkasDigital(): array
    {
        return [
            ['nama' => 'Hasil-lab-klinik-perujuk.pdf', 'jenis' => 'penunjang-luar', 'tipe' => 'pdf',
             'ukuran' => '412 KB', 'waktu' => '02 Nov 2026, 08:58', 'oleh' => '[Nama Petugas]',
             'keterangan' => 'Hasil lab dari klinik perujuk, 31 Okt 2026'],
            ['nama' => 'Surat-rujukan-puskesmas.jpg', 'jenis' => 'rujukan', 'tipe' => 'gambar',
             'ukuran' => '1,2 MB', 'waktu' => '02 Nov 2026, 08:52', 'oleh' => '[Nama Petugas]',
             'keterangan' => null],
            ['nama' => 'Kartu-penjamin.jpg', 'jenis' => 'identitas', 'tipe' => 'gambar',
             'ukuran' => '236 KB', 'waktu' => '02 Nov 2026, 08:51', 'oleh' => '[Nama Petugas]',
             'keterangan' => null],
        ];
    }
}