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
     * 'bayar' => 'semua' supaya jumlahnya sama dengan tabel (bawaan tabel = belum terbayar).
     */
    public static function tugas(): array
    {
        return [
            ['label' => 'Pasien IGD belum close',          'jumlah' => 8, 'ikon' => 'bi-activity',
             'route' => 'kunjungan.igd',   'query' => ['status' => 'belum_close', 'bayar' => 'semua']],
            ['label' => 'BRM rawat jalan belum terisi',    'jumlah' => 3, 'ikon' => 'bi-file-earmark-medical',
             'route' => 'kunjungan.rajal', 'query' => ['status' => 'brm', 'bayar' => 'semua']],
            ['label' => 'BRM hemodialisa belum terisi',    'jumlah' => 3, 'ikon' => 'bi-droplet',
             'route' => 'kunjungan.hd',    'query' => ['status' => 'brm', 'bayar' => 'semua']],
            ['label' => 'Pasien rawat inap tanpa diagnosa', 'jumlah' => 2, 'ikon' => 'bi-clipboard2-x',
             'route' => 'kunjungan.ranap', 'query' => ['status' => 'tanpa_diagnosa', 'bayar' => 'semua']],
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
     * Data kunjungan per unit (data contoh, BUKAN data pasien asli).
     * $sub = ruang rawat inap (umum/kebidanan/icu/hcu) atau jenis OK (mayor/minor).
     * Tanggal dibuat relatif terhadap hari ini supaya filter tanggal bisa dicoba saat demo:
     * - Rawat inap : 12 pasien, masuk dalam 10 hari terakhir.
     * - Unit lain  : 12 pasien hari ini + 3 pasien kemarin.
     * Catatan untuk IT: ganti dengan query kunjungan per unit, dengan nama kunci yang sama.
     */
    public static function kunjungan(string $unit, ?string $sub = null): array
    {
        $pasien = [
            ['Andi Pratama', '000123'], ['Siti Rahmawati', '000124'], ['Budi Santoso', '000131'],
            ['Dewi Lestari', '000142'], ['Hendra Wijaya', '000157'], ['Putri Maharani', '000163'],
            ['Rudi Hartono', '000170'], ['Nur Aisyah', '000181'], ['Agus Salim', '000195'],
            ['Rina Kartika', '000203'], ['Eko Prasetyo', '000214'], ['Lina Marlina', '000226'],
            ['Fajar Ramadhan', '000231'], ['Wulan Sari', '000245'], ['Yusuf Hakim', '000252'],
        ];
        if ($unit === 'ranap') {
            $pasien = array_slice($pasien, 0, 12);
        }

        $hariIni  = now()->startOfDay();
        $dokter   = ['dr. Arif Nugroho', 'dr. Laras Wulandari', 'dr. Rizky Ananda'];
        $perawat  = ['Bagus Setiawan, A.Md.Kep', 'Fitri Handayani, A.Md.Kep', 'Maya Lestari, AMK'];
        $bidan    = ['Bidan Ratna Dewi', 'Bidan Sinta Ayu'];
        $apoteker = ['Apt. Nina Kurnia, S.Farm', 'Apt. Yoga Pradana, S.Farm'];

        // [unit tujuan, PPA 1]. PPA 1 = null berarti dokter jaga.
        $layanan = match ($unit) {
            'igd' => [['IGD', null], ['IGD, Laboratorium', null], ['IGD, Radiologi', null]],
            'hd'  => [['Hemodialisa', null], ['Laboratorium, Hemodialisa', null]],
            'rm'  => [
                ['Klinik Rehabilitasi Medik', 'dr. Hana Prameswari, Sp.KFR'],
                ['Fisioterapi',               'Citra Ayu, S.Tr.Kes'],
                ['Okupasi Terapi',            'Sari Utami, A.Md.OT'],
                ['Terapi Wicara',             'Rina Marlina, A.Md.TW'],
                ['Pedagogia',                 'Dian Puspita, S.Pd'],
            ],
            'rajal' => [
                ['Klinik Penyakit Dalam',          'dr. Arif Nugroho, Sp.PD'],
                ['Klinik Anak',                    'dr. Laras Wulandari, Sp.A'],
                ['Klinik Syaraf',                  'dr. Rizky Ananda, Sp.N'],
                ['Klinik Gigi dan Mulut',          'drg. Maya Sari, Sp.KG'],
                ['Klinik Mata',                    'dr. Dimas Pratama, Sp.M'],
                ['Klinik THT',                     'dr. Yudha Permana, Sp.THT-KL'],
                ['Klinik Kebidanan dan Kandungan', 'dr. Sekar Ayu, Sp.OG'],
                ['Klinik Bedah',                   'dr. Bima Saputra, Sp.B'],
                ['Klinik Kesehatan Jiwa',          'dr. Fajar Nugraha, Sp.KJ'],
                ['Klinik Umum',                    'dr. Tika Ramadhani'],
            ],
            default => [['—', null]],
        };

        // [ruang, bed, kelas], DPJP, dan diagnosa menyesuaikan unit rawat inap
        [$ruangan, $dpjp, $diagnosa] = match ($sub) {
            'kebidanan' => [
                [['Melati I', 'Melati I-A', 'Kelas III'], ['Melati II', 'Melati II-B', 'Kelas II'],
                 ['Teratai', 'Teratai-2', 'Kelas I'], ['Teratai VIP', 'Teratai VIP', 'VIP']],
                ['dr. Sekar Ayu, Sp.OG', 'dr. Wening Putri, Sp.OG'],
                ['G2P1A0 hamil 39 minggu', 'Post SC hari ke-1', 'Ketuban pecah dini', 'Abortus inkomplit', ''],
            ],
            'icu', 'hcu' => [
                [[strtoupper($sub), strtoupper($sub) . '-01', strtoupper($sub)],
                 [strtoupper($sub), strtoupper($sub) . '-02', strtoupper($sub)],
                 [strtoupper($sub), strtoupper($sub) . '-03', strtoupper($sub)]],
                ['dr. Arif Nugroho, Sp.PD', 'dr. Galih Wicaksono, Sp.An'],
                ['Syok sepsis', 'Gagal jantung kongestif', 'Stroke hemoragik', 'Post laparotomi', ''],
            ],
            default => [
                [['Kenanga I', 'Kenanga I-C', 'Kelas III'], ['Lily II (Isolasi)', 'Lily II-A', 'Kelas I'],
                 ['Mawar', 'Mawar-D', 'Kelas II'], ['Anggrek IV', 'Anggrek IV', 'VIP'],
                 ['Kenanga II', 'Kenanga II-B', 'Kelas III']],
                array_map(fn ($d) => $d . ', Sp.PD', $dokter),
                ['Demam tifoid', 'Pneumonia', 'Selulitis pedis', 'Dispepsia', ''],
            ],
        };

        $hasil = [];
        foreach ($pasien as $i => [$nama, $rm]) {
            [$tujuan, $ppa1] = $layanan[$i % count($layanan)];
            $kamar   = $ruangan[$i % count($ruangan)];
            $jkn     = $i % 5 !== 3;
            $tanggal = match (true) {
                $unit === 'ranap' => $hariIni->copy()->subDays(($i * 2) % 11),
                $i >= 12          => $hariIni->copy()->subDay(),
                default           => $hariIni->copy(),
            };

            $hasil[] = [
                'no_rm'         => $rm,
                'nama'          => $nama,
                'no_reg'        => '26110200' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'no_sep'        => $jkn && $i % 4 !== 2 ? '0179R01211260' . str_pad($i + 1, 5, '0', STR_PAD_LEFT) : '',
                'tanggal'       => $tanggal->toDateString(),           // untuk filter (Y-m-d)
                'tgl'           => $tanggal->translatedFormat('d M Y'), // untuk tampilan
                'jam'           => sprintf('%02d:%02d', 7 + $i, ($i * 13) % 60),
                'penjamin'      => $jkn ? 'JKN' : 'NON JKN',
                'sudah_datang'  => $i % 6 !== 4,
                'terbayar'      => $i % 3 === 0,
                'unit_tujuan'   => $tujuan,
                'brm'           => $i % 4 !== 3,
                'close'         => $i % 3 === 0,
                'rujukan'       => $i % 5 === 0,
                'kontrol'       => $i % 4 === 1,
                'dokter_isi'    => $dokter[$i % 3],
                'dokter_pulang' => $i % 3 === 0 ? $dokter[$i % 3] : '',
                'perawat'       => $perawat[$i % 3],
                'ppa1'          => $ppa1 ?? $dokter[$i % 3],
                'ppa2'          => str_contains($tujuan, 'Kebidanan') ? $bidan[$i % 2] : $perawat[($i + 1) % 3],
                'ppa_farmasi'   => in_array($unit, ['rm', 'rajal'], true) && $i % 3 !== 2 ? $apoteker[$i % 2] : '',
                'kelas'         => $kamar[2],
                'ruang'         => $kamar[0],
                'bed'           => $kamar[1],
                'dpjp'          => $dpjp[$i % count($dpjp)],
                'diagnosa'      => $diagnosa[$i % 5],
                'unit_asal'     => $i % 3 ? 'UGD' : 'Poliklinik',
            ];
        }

        return $hasil;
    }

        /**
     * Data satu pasien untuk ruang kerja Rekam Medis (mengikuti unit yang sedang dibuka).
     * Catatan untuk IT: ganti dengan query pasien + kunjungan.
     */
    public static function pasien(string $noReg): array
    {
        ['unit' => $unit, 'sub' => $sub] = KonfigurasiRekamMedis::unitAktif($noReg);

        $daftar = self::kunjungan($unit, $sub);
        $row    = collect($daftar)->firstWhere('no_reg', $noReg) ?? $daftar[0];
        $info   = KonfigurasiRekamMedis::UNIT[$unit];

        $perempuan = ['Siti Rahmawati', 'Dewi Lestari', 'Putri Maharani', 'Nur Aisyah',
                      'Rina Kartika', 'Lina Marlina', 'Wulan Sari'];

        return $row + [
            'jk'             => in_array($row['nama'], $perempuan, true) ? 'Perempuan' : 'Laki-laki',
            'umur'           => (18 + (int) $row['no_rm'] % 50) . ' th',
            'tgl_lahir'      => '[tgl lahir]',
            'alamat'         => '[alamat pasien]',
            'nik'            => '[NIK]',
            'no_hp'          => '[No. HP]',
            'no_bpjs'        => $row['penjamin'] === 'JKN' ? '[No. BPJS]' : '—',
            'alergi'         => '[isi alergi]',
            'status_hiv'     => 'Negatif (data contoh)',
            'unit'           => $info['label'] . ($sub ? ' · ' . KonfigurasiRekamMedis::SUB[$unit][$sub] : ''),
            'unit_kode'      => $unit,
            'unit_sub'       => $sub,
            'unit_route'     => $info['route'],
            'datang'         => $row['tgl'] . ', ' . $row['jam'],
            'dpjp_nama'      => match ($unit) {
                'ranap', 'ok' => $row['dpjp'],
                'rajal'       => $row['ppa1'],
                default       => $row['dokter_isi'],
            },
            'diagnosa_kerja' => match ($unit) {
                'ranap', 'ok' => $row['diagnosa'] ?: 'Belum ada diagnosa',
                'rajal'       => session("pemeriksaan.$noReg.isi.diagnosa") ?: 'Belum ada diagnosa',
                default       => 'Obs. febris H6, susp. demam tifoid',
            },
        ];
    }

    /** Kartu khusus unit di Ringkasan (CONTOH). IT RSCC: ganti dengan data form unit masing-masing. */
    public static function kartuUnit(array $p): array
    {
        $isi  = session("asesmen.{$p['no_reg']}.isi", []);
        $opsi = self::opsiAsesmenGd();
        $warnaTriase = ['merah' => 'gagal', 'kuning' => 'peringatan', 'hijau' => 'sukses', 'hitam' => 'info'];
        $triase = $isi['triase'] ?? 'kuning';

        $hariRawat = (int) abs(\Illuminate\Support\Carbon::parse($p['tanggal'])->startOfDay()
            ->diffInDays(now()->startOfDay())) + 1;

        return match ($p['unit_kode']) {
            'rajal' => [
                'judul' => 'Kunjungan poli', 'ikon' => 'bi-clipboard2-pulse',
                'sorot' => [$p['sudah_datang'] ? 'Sudah datang' : 'Belum datang', $p['sudah_datang'] ? 'info' : 'peringatan'],
                'progres' => null,
                'baris' => [
                    'Klinik'           => $p['unit_tujuan'],
                    'Dokter'           => $p['ppa1'],
                    'Jenis kunjungan'  => 'Kontrol ke-3',
                    'Rujukan / konsul' => $p['rujukan'] ? 'Ada (rujukan FKTP)' : 'Tidak ada',
                    'Surat kontrol'    => $p['kontrol'] ? 'Sudah dibuat' : 'Perlu dibuat',
                ],
            ],
            'rm' => [
                'judul' => 'Program terapi', 'ikon' => 'bi-person-walking',
                'sorot' => ['Sesi 5 dari 8', 'info'],
                'progres' => [5, 8],
                'baris' => [
                    'Layanan'         => $p['unit_tujuan'],
                    'Terapis'         => $p['ppa1'],
                    'Frekuensi'       => '2× seminggu',
                    'Sesi berikutnya' => now()->addDays(3)->translatedFormat('l, d M Y') . ' · 09:00',
                ],
            ],
            'hd' => [
                'judul' => 'Sesi hemodialisa', 'ikon' => 'bi-droplet',
                'sorot' => ['HD ke-49', 'info'],
                'progres' => null,
                'baris' => [
                    'Jadwal rutin'      => '2× seminggu (Senin & Kamis)',
                    'Akses vaskular'    => 'AV-fistula',
                    'BB kering'         => '58,0 kg',
                    'BB pre-HD'         => '63,6 kg (naik 5,6 kg)',
                    'Resep HD terakhir' => 'QB 200 · QD 400 · UF 4,5 L · 4,5 jam',
                ],
            ],
            'ranap' => [
                'judul' => 'Perawatan', 'ikon' => 'bi-hospital',
                'sorot' => ["Hari rawat ke-$hariRawat", 'info'],
                'progres' => null,
                'baris' => [
                    'Ruang / bed'    => "{$p['ruang']} · {$p['bed']}",
                    'Kelas'          => $p['kelas'],
                    'Masuk dari'     => $p['unit_asal'],
                    'Tanggal masuk'  => $p['tgl'],
                    'Rencana pulang' => 'Belum ditentukan',
                ],
            ],
            'ok' => [
                'judul' => 'Jadwal operasi', 'ikon' => 'bi-scissors',
                'sorot' => [($p['unit_sub'] === 'minor' ? 'OK Minor' : 'OK Mayor') . ' · 13:00', 'info'],
                'progres' => null,
                'baris' => [
                    'Tindakan'              => 'Eksisi tumor (contoh)',
                    'Operator'              => 'dr. Bima Saputra, Sp.B',
                    'Anestesi'              => 'dr. Galih Wicaksono, Sp.An · anestesi umum',
                    'Checklist keselamatan' => 'Sign in ✓ · Time out – · Sign out –',
                    'Asal pasien'           => "{$p['ruang']} · {$p['bed']}",
                ],
            ],
            default => [ // igd
                'judul' => 'Triase & kedatangan', 'ikon' => 'bi-activity',
                'sorot' => ['Triase ' . ($opsi['triase'][$triase] ?? 'Kuning · P2'), $warnaTriase[$triase] ?? 'peringatan'],
                'progres' => null,
                'baris' => [
                    'Cara datang'  => $opsi['cara_datang'][$isi['cara_datang'] ?? 'keluarga'] ?? 'Diantar keluarga',
                    'Waktu datang' => $p['datang'],
                    'Tujuan'       => $p['unit_tujuan'],
                    'Status'       => $p['close'] ? 'Sudah close' : 'Belum close',
                ],
            ],
        };
    }

    /** Aktivitas terakhir pada kunjungan ini (CONTOH, terbaru di atas). 'route' = nama route rekam-medis.* */
    public static function aktivitasTerakhir(): array
    {
        return [
            ['jam' => '14:00', 'ikon' => 'bi-heart-pulse',  'teks' => 'Observasi tanda vital',                      'oleh' => '[Nama Perawat]',    'route' => 'ttv'],
            ['jam' => '10:21', 'ikon' => 'bi-journal-text', 'teks' => 'CPPT dokter: obs. febris, susp. demam tifoid', 'oleh' => 'dr. [Nama Dokter]', 'route' => 'cppt'],
            ['jam' => '10:05', 'ikon' => 'bi-droplet-half', 'teks' => 'Hasil lab: 3 nilai di luar batas normal',    'oleh' => 'Laboratorium',      'route' => 'riwayat'],
            ['jam' => '09:40', 'ikon' => 'bi-image',        'teks' => 'Thorax PA: cor dan pulmo dalam batas normal', 'oleh' => 'Radiologi',         'route' => 'riwayat'],
            ['jam' => '09:12', 'ikon' => 'bi-send',         'teks' => 'Permintaan radiologi dikirim',               'oleh' => 'dr. [Nama Dokter]', 'route' => 'permintaan'],
        ];
    }

        /**
     * Kunjungan rawat jalan sebelumnya (CONTOH, terbaru di atas) — panel kanan form Pemeriksaan.
     * Catatan untuk IT: ganti dengan query pemeriksaan rawat jalan pasien yang sudah final.
     */
    public static function kunjunganSebelumnya(): array
    {
        return [
            [
                'tanggal' => '14 Agu 2026', 'klinik' => 'Klinik Penyakit Dalam', 'dokter' => 'dr. Arif Nugroho, Sp.PD',
                's' => 'Kontrol. Nyeri ulu hati berkurang, mual (-)',
                'o' => 'TD 130/80 mmHg, N 84 x/menit. Nyeri tekan epigastrium minimal',
                'a' => 'Dispepsia fungsional, perbaikan', 'icd10' => ['K30', 'Z09.8'],
                'p' => 'Omeprazole 20 mg 2×1 (14 hari). Kontrol 1 bulan.',
            ],
            [
                'tanggal' => '10 Jul 2026', 'klinik' => 'Klinik Penyakit Dalam', 'dokter' => 'dr. Arif Nugroho, Sp.PD',
                's' => 'Nyeri ulu hati 2 minggu, mual, kembung',
                'o' => 'TD 128/82 mmHg. Nyeri tekan epigastrium',
                'a' => 'Gastritis', 'icd10' => ['K29.7'],
                'p' => 'Omeprazole 20 mg 2×1, antasida 3×1. Edukasi pola makan. Kontrol 1 bulan.',
            ],
            [
                'tanggal' => '03 Mar 2026', 'klinik' => 'Klinik Gigi dan Mulut', 'dokter' => 'drg. Maya Sari, Sp.KG',
                's' => 'Gigi geraham kiri bawah ngilu',
                'o' => 'Karies media gigi 36',
                'a' => 'Karies gigi', 'icd10' => ['K02.9'],
                'p' => 'Tumpatan komposit gigi 36.',
            ],
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

        /**
     * Pilihan untuk Asesmen Awal Keperawatan Gawat Darurat (CONTOH).
     * IT RSCC: sesuaikan dengan master data / SPO keperawatan RS.
     */
    public static function opsiAsesmenGd(): array
    {
        $kunci = fn (array $daftar) => collect($daftar)
            ->mapWithKeys(fn ($nama) => [\Illuminate\Support\Str::slug($nama) => $nama])
            ->all();

        return [
            'cara_datang' => [
                'sendiri'  => 'Datang sendiri',
                'keluarga' => 'Diantar keluarga',
                'ambulans' => 'Ambulans',
                'rujukan'  => 'Rujukan faskes lain',
            ],
            'triase' => [
                'merah'  => 'Merah · P1',
                'kuning' => 'Kuning · P2',
                'hijau'  => 'Hijau · P3',
                'hitam'  => 'Hitam · P0',
            ],
            'keadaan_umum' => ['baik' => 'Baik', 'sedang' => 'Sedang', 'lemah' => 'Lemah', 'buruk' => 'Buruk'],
            'gcs_e' => [
                4 => 'Membuka spontan', 3 => 'Dengan perintah suara',
                2 => 'Dengan rangsang nyeri', 1 => 'Tidak membuka',
            ],
            'gcs_v' => [
                5 => 'Orientasi baik', 4 => 'Bingung', 3 => 'Kata-kata tidak tepat',
                2 => 'Suara tidak jelas', 1 => 'Tidak ada suara',
            ],
            'gcs_m' => [
                6 => 'Mengikuti perintah', 5 => 'Melokalisir nyeri', 4 => 'Menarik dari nyeri',
                3 => 'Fleksi abnormal', 2 => 'Ekstensi abnormal', 1 => 'Tidak ada gerakan',
            ],
            'nyeri_sifat' => $kunci(['Ditusuk', 'Terikat', 'Tumpul', 'Berdenyut', 'Terbakar']),
            'nyeri_waktu' => $kunci(['Hilang timbul', 'Terus menerus', 'Jarang']),
            'fisik' => [
                'kepala'            => 'Kepala & wajah',
                'leher'             => 'Leher',
                'dada'              => 'Dada / paru',
                'jantung'           => 'Jantung',
                'abdomen'           => 'Abdomen',
                'ekstremitas_atas'  => 'Ekstremitas atas',
                'ekstremitas_bawah' => 'Ekstremitas bawah',
                'kulit'             => 'Kulit',
            ],
            'risiko_jatuh' => [
                'a' => 'Cara berjalan tidak seimbang / memakai alat bantu',
                'b' => 'Menopang saat akan duduk (memegang kursi atau benda lain)',
            ],
            'diagnosa' => $kunci([
                'Bersihan jalan napas tidak efektif', 'Pola napas tidak efektif',
                'Perfusi jaringan tidak efektif', 'Hipertermia', 'Nyeri akut', 'Nyeri kronis',
                'Kecemasan', 'Risiko infeksi', 'Diare', 'Kekurangan volume cairan',
                'Gangguan pertukaran gas', 'Intoleransi aktivitas',
            ]),
            'intervensi' => [
                'Nyeri' => $kunci([
                    'Mengajarkan teknik relaksasi dan distraksi',
                    'Mengontrol lingkungan (cahaya, kebisingan)',
                ]),
                'Risiko jatuh' => $kunci([
                    'Pakaikan pita kuning di pergelangan tangan',
                    'Edukasi pencegahan jatuh kepada pasien dan keluarga',
                ]),
                'Lainnya' => $kunci([
                    'Kolaborasi dengan dokter untuk pemberian obat',
                    'Kompres hangat',
                    'Pantau tanda vital tiap jam',
                ]),
            ],
        ];
    }

        /**
     * Catatan CPPT / SBAR yang sudah ada pada kunjungan ini (CONTOH, terbaru di atas).
     * IT RSCC: ganti dengan query tabel cppt.
     */
    public static function cppt(): array
    {
        return [
            [
                'id' => 'c3', 'waktu' => '02 Nov · 10:21', 'ppa' => 'dr. [Nama Dokter]', 'profesi' => 'dokter',
                'format' => 'soap',
                'isi' => [
                    's' => 'Demam 6 hari, mual',
                    'o' => 'S 40,1 °C, N 102 x/mnt',
                    'a' => 'Obs. febris, susp. demam tifoid',
                    'p' => 'Cek darah rutin, Widal; parasetamol infus',
                ],
                'instruksi' => 'Observasi TTV tiap 4 jam',
                'perlu_verifikasi' => false,
                'verifikasi' => ['oleh' => 'dr. [Nama Dokter]', 'waktu' => '02 Nov · 10:21'],
            ],
            [
                'id' => 'c2', 'waktu' => '02 Nov · 09:05', 'ppa' => '[Nama Perawat]', 'profesi' => 'perawat',
                'format' => 'soap',
                'isi' => [
                    's' => 'Pusing, lemas',
                    'o' => 'TD 120/70 mmHg, RR 22 x/mnt',
                    'a' => 'Hipertermia',
                    'p' => 'Kompres hangat, edukasi minum',
                ],
                'instruksi' => 'Lapor dokter jika suhu > 39 °C',
                'perlu_verifikasi' => true,
                'verifikasi' => null,
            ],
            [
                'id' => 'c1', 'waktu' => '02 Nov · 08:52', 'ppa' => '[Nama Perawat]', 'profesi' => 'perawat',
                'format' => 'sbar',
                'isi' => [
                    's' => 'Pasien datang dengan demam 6 hari',
                    'b' => 'Tidak ada riwayat penyakit kronis',
                    'a' => 'Asesmen awal keperawatan IGD selesai; risiko jatuh: tidak berisiko',
                    'r' => 'Mohon dokter jaga memeriksa pasien',
                ],
                'instruksi' => null,
                'perlu_verifikasi' => true,
                'verifikasi' => ['oleh' => 'dr. [Nama Dokter]', 'waktu' => '02 Nov · 09:15'],
            ],
        ];
    }

        /** Katalog obat & alkes (CONTOH). IT RSCC: ganti dengan master farmasi. */
    public static function katalogObat(): array
    {
        $o = fn ($kode, $nama, $satuan, $jenis = 'obat') => compact('kode', 'nama', 'satuan', 'jenis');

        return [
            $o('PCT500', 'Parasetamol 500 mg tablet', 'tablet'),
            $o('PCTINF', 'Parasetamol infus 1 g/100 ml', 'botol'),
            $o('OMZ40', 'Omeprazole 40 mg injeksi', 'vial'),
            $o('OMZ20', 'Omeprazole 20 mg kapsul', 'kapsul'),
            $o('CTX1G', 'Ceftriaxone 1 g injeksi', 'vial'),
            $o('OND4', 'Ondansetron 4 mg injeksi', 'ampul'),
            $o('DOM10', 'Domperidon 10 mg tablet', 'tablet'),
            $o('AMX500', 'Amoksisilin 500 mg kapsul', 'kapsul'),
            $o('CTZ10', 'Cetirizin 10 mg tablet', 'tablet'),
            $o('ANT', 'Antasida doen suspensi 60 ml', 'botol'),
            $o('RL500', 'Ringer laktat 500 ml', 'kolf'),
            $o('NS500', 'NaCl 0,9% 500 ml', 'kolf'),
            $o('INFSET', 'Infus set dewasa', 'buah', 'alkes'),
            $o('IVC22', 'IV catheter 22G', 'buah', 'alkes'),
            $o('SP3', 'Spuit 3 ml', 'buah', 'alkes'),
            $o('SP5', 'Spuit 5 ml', 'buah', 'alkes'),
        ];
    }

    /** Resep kunjungan sebelumnya (untuk tombol "Salin"). */
    public static function resepSebelumnya(): array
    {
        return [
            'tanggal' => '31 Okt 2026',
            'unit'    => 'IGD',
            'dokter'  => 'dr. [Nama Dokter]',
            'items'   => [
                ['kode' => 'OMZ20', 'nama' => 'Omeprazole 20 mg kapsul', 'satuan' => 'kapsul',
                 'jumlah' => 10, 'signa' => '2 x 1', 'rute' => 'oral', 'catatan' => 'Sebelum makan'],
                ['kode' => 'ANT', 'nama' => 'Antasida doen suspensi 60 ml', 'satuan' => 'botol',
                 'jumlah' => 1, 'signa' => '3 x 1 sdm', 'rute' => 'oral', 'catatan' => ''],
                ['kode' => 'DOM10', 'nama' => 'Domperidon 10 mg tablet', 'satuan' => 'tablet',
                 'jumlah' => 10, 'signa' => '3 x 1', 'rute' => 'oral', 'catatan' => 'Bila mual'],
            ],
        ];
    }

    /** Riwayat pemberian obat ke pasien (popup di E-Resep). */
    public static function riwayatPemberianObat(): array
    {
        return [
            ['waktu' => '02 Nov · 10:30', 'obat' => 'Parasetamol infus 1 g/100 ml', 'dosis' => '1 g',    'rute' => 'IV', 'oleh' => '[Nama Perawat]'],
            ['waktu' => '02 Nov · 10:25', 'obat' => 'Omeprazole 40 mg injeksi',     'dosis' => '40 mg',  'rute' => 'IV', 'oleh' => '[Nama Perawat]'],
            ['waktu' => '02 Nov · 09:10', 'obat' => 'Ringer laktat 500 ml',         'dosis' => '20 tpm', 'rute' => 'IV', 'oleh' => '[Nama Perawat]'],
            ['waktu' => '31 Okt · 21:40', 'obat' => 'Omeprazole 40 mg injeksi',     'dosis' => '40 mg',  'rute' => 'IV', 'oleh' => '[Nama Perawat]'],
        ];
    }

    /**
     * Riwayat pemeriksaan pasien, terbaru di atas (CONTOH).
     * detail = [nama, hasil, nilai rujukan, flag ('tinggi' | 'rendah' | '')]
     */
    public static function riwayatPemeriksaan(): array
    {
        $r = fn ($tanggal, $jam, $jenis, $judul, $unit, $oleh, $ringkasan, $detail = null) =>
            compact('tanggal', 'jam', 'jenis', 'judul', 'unit', 'oleh', 'ringkasan', 'detail');

        return [
            $r('02 Nov 2026', '10:05', 'lab', 'Darah rutin, Widal, NS1 Dengue', 'Laboratorium', 'dr. [Nama Dokter]',
               '3 nilai di luar batas normal', [
                   ['Hemoglobin', '13,8 g/dl', '13–17', ''],
                   ['Leukosit', '9.800 /µl', '4.000–10.000', ''],
                   ['Trombosit', '145.000 /µl', '150.000–400.000', 'rendah'],
                   ['Neutrofil', '73,9 %', '50–70', 'tinggi'],
                   ['Widal S. typhi H', '1/80', '< 1/80', 'tinggi'],
                   ['NS1 Dengue', 'Negatif', 'Negatif', ''],
               ]),
            $r('02 Nov 2026', '09:40', 'radiologi', 'Thorax PA', 'Radiologi', 'dr. [Nama Dokter], Sp.Rad',
               'Cor dan pulmo dalam batas normal'),
            $r('02 Nov 2026', '09:20', 'ekg', 'EKG 12 lead', 'IGD', 'dr. [Nama Dokter]',
               'Sinus takikardia, 104 x/mnt'),
            $r('02 Nov 2026', '08:50', 'kunjungan', 'Kunjungan IGD', 'IGD', 'dr. [Nama Dokter]',
               'Obs. febris H6, susp. demam tifoid'),
            $r('31 Okt 2026', '22:00', 'kontrol', 'Surat kontrol', 'IGD', 'dr. [Nama Dokter]',
               'Kontrol ke Klinik Penyakit Dalam, 7 Nov 2026'),
            $r('31 Okt 2026', '21:45', 'cppt', 'CPPT dokter jaga', 'IGD', 'dr. [Nama Dokter]',
               'S: nyeri ulu hati · A: dispepsia · P: omeprazole, antasida'),
            $r('31 Okt 2026', '21:30', 'kunjungan', 'Kunjungan IGD', 'IGD', 'dr. [Nama Dokter]',
               'Dispepsia; pulang dengan obat'),
            $r('14 Agu 2026', '10:15', 'kunjungan', 'Klinik Penyakit Dalam', 'Rawat Jalan', 'dr. [Nama Dokter], Sp.PD',
               'Kontrol rutin'),
            $r('14 Agu 2026', '09:30', 'lab', 'Gula darah, profil lipid', 'Laboratorium', 'dr. [Nama Dokter], Sp.PD',
               'Semua dalam batas normal', [
                   ['Gula darah puasa', '92 mg/dl', '70–100', ''],
                   ['Kolesterol total', '185 mg/dl', '< 200', ''],
                   ['Trigliserida', '120 mg/dl', '< 150', ''],
               ]),
            $r('03 Mar 2026', '11:00', 'kunjungan', 'Klinik Gigi dan Mulut', 'Rawat Jalan', 'drg. [Nama Dokter]',
               'Karies gigi'),
        ];
    }

        /**
     * Observasi tanda vital pasien (CONTOH; tren membaik setelah terapi).
     * stempel = 'Y-m-d H:i' untuk mengurutkan.
     */
    public static function observasiTtv(): array
    {
        $o = fn ($stempel, $s, $d, $nadi, $napas, $suhu, $spo2, $nyeri) => [
            'stempel' => $stempel, 'td_sistolik' => $s, 'td_diastolik' => $d, 'nadi' => $nadi,
            'napas' => $napas, 'suhu' => $suhu, 'spo2' => $spo2, 'nyeri' => $nyeri,
            'kesadaran' => 'cm', 'oleh' => '[Nama Perawat]',
        ];

        return [
            $o('2026-11-02 14:00', 118, 76, 86, 18, 37.4, 99, 2),
            $o('2026-11-02 13:00', 120, 76, 88, 18, 37.6, 99, 2),
            $o('2026-11-02 12:00', 118, 74, 90, 19, 38.0, 99, 3),
            $o('2026-11-02 11:00', 116, 70, 94, 20, 38.6, 98, 3),
            $o('2026-11-02 10:00', 118, 72, 98, 20, 39.2, 98, 4),
            $o('2026-11-02 08:52', 120, 70, 102, 22, 40.1, 98, 5),
        ];
    }
}