<?php

namespace App\Support;

/**
 * Pengaturan ruang kerja rekam medis per unit:
 * - unit kunjungan yang sedang dibuka (diingat per No. Reg),
 * - form khusus unit di menu,
 * - daftar "Perlu dikerjakan" di Ringkasan.
 * Catatan untuk IT RSCC: unit diambil dari tabel kunjungan; status tiap form dari tabel form masing-masing.
 * 'href' => '#' = form belum dibuat → tampil "Segera".
 */
class KonfigurasiRekamMedis
{
    public const UNIT = [
        'igd'   => ['label' => 'IGD',                'route' => 'kunjungan.igd'],
        'rajal' => ['label' => 'Rawat Jalan',        'route' => 'kunjungan.rajal'],
        'rm'    => ['label' => 'Rehabilitasi Medik', 'route' => 'kunjungan.rm'],
        'hd'    => ['label' => 'Hemodialisa',        'route' => 'kunjungan.hd'],
        'ranap' => ['label' => 'Rawat Inap',         'route' => 'kunjungan.ranap'],
        'ok'    => ['label' => 'Kamar Operasi',      'route' => 'kunjungan.ok'],
    ];

    public const SUB = [
        'ranap' => ['umum' => 'R. Inap Umum', 'kebidanan' => 'R. Inap Kebidanan', 'icu' => 'ICU', 'hcu' => 'HCU'],
        'ok'    => ['mayor' => 'OK Mayor', 'minor' => 'OK Minor'],
    ];

    /** Dipanggil saat tombol Buka diklik (?unit=...&sub=...). Nilai tidak dikenal diabaikan. */
    public static function simpanUnit(string $noReg, string $unit, ?string $sub): void
    {
        if (! isset(self::UNIT[$unit])) {
            return;
        }
        if (isset(self::SUB[$unit])) {
            $sub = isset(self::SUB[$unit][$sub ?? '']) ? $sub : array_key_first(self::SUB[$unit]);
        } else {
            $sub = null;
        }
        session()->put("unit_kunjungan.$noReg", ['unit' => $unit, 'sub' => $sub]);
    }

    /** Unit kunjungan pasien ini. Bawaan: IGD. */
    public static function unitAktif(string $noReg): array
    {
        return session("unit_kunjungan.$noReg", ['unit' => 'igd', 'sub' => null]);
    }

    private static function segera(string $key, string $label): array
    {
        return ['key' => $key, 'label' => $label, 'href' => '#'];
    }

    /** Form utama unit → masuk ke kelompok "Asesmen & pemeriksaan" (setelah Ringkasan). */
    public static function formUtama(string $unit, \Closure $r): array
    {
        $s = fn ($key, $label) => self::segera($key, $label);

        return match ($unit) {
            'igd'   => [['key' => 'asesmen', 'label' => 'Asesmen awal gawat darurat', 'href' => $r('asesmen')]],
            'rajal' => [['key' => 'pemeriksaan', 'label' => 'Pemeriksaan dokter (SOAP)', 'href' => $r('pemeriksaan')]],
            'rm'    => [$s('rm-dokter', 'Asesmen dokter rehab medik'), $s('rm-terapis', 'Program terapi (terapis)'),
                        $s('rm-uji', 'Uji fungsi')],
            'ranap' => [$s('ranap-medis', 'Asesmen awal medis'), $s('ranap-kep', 'Asesmen awal keperawatan'),
                        $s('ranap-ulang', 'Asesmen ulang')],
            default => [],
        };
    }

    /** Kelompok menu tambahan khusus unit (tampil setelah "Asesmen & pemeriksaan"). */
    public static function grupKhusus(string $unit): array
    {
        $s = fn ($key, $label) => self::segera($key, $label);

        return match ($unit) {
            'rm' => [
                ['judul' => 'Program terapi', 'ikon' => 'bi-calendar2-week', 'items' => [
                    $s('rm-jadwal', 'Jadwal terapi'),
                    $s('rm-tumbuh', 'Skrining tumbuh kembang anak'),
                ]],
            ],
            'hd' => [
                ['judul' => 'Sesi hemodialisa', 'ikon' => 'bi-droplet', 'items' => [
                    $s('hd-pra', 'Pra-HD'),
                    $s('hd-resep', 'Resep HD'),
                    $s('hd-intra', 'Monitoring intra-HD'),
                    $s('hd-pasca', 'Pasca-HD & evaluasi'),
                ]],
            ],
            'ranap' => [
                ['judul' => 'Perawatan', 'ikon' => 'bi-hospital', 'items' => [
                    $s('ranap-nursing', 'Asuhan keperawatan'),
                    $s('ranap-cairan', 'Monitoring cairan'),
                    $s('ranap-bed', 'Manajemen bed'),
                    $s('ranap-pulang', 'Discharge summary'),
                ]],
            ],
            'ok' => [
                ['judul' => 'Pra-operasi', 'ikon' => 'bi-clipboard2-check', 'items' => [
                    $s('ok-pra-bedah', 'Asesmen pra bedah'),
                    $s('ok-pra-anestesi', 'Asesmen pra anestesi'),
                    $s('ok-kep-pre', 'Asuhan keperawatan preoperatif'),
                    $s('ok-penanda', 'Penanda area operasi'),
                ]],
                ['judul' => 'Saat operasi', 'ikon' => 'bi-scissors', 'items' => [
                    $s('ok-checklist', 'Checklist keselamatan operasi'),
                    $s('ok-anestesi', 'Catatan anestesi'),
                    $s('ok-operasi', 'Catatan operasi'),
                    $s('ok-phaco', 'Catatan operasi Phaco'),
                    $s('ok-kep-peri', 'Asuhan keperawatan perioperatif'),
                    $s('ok-resep', 'Resep OK'),
                ]],
                ['judul' => 'Pasca-operasi', 'ikon' => 'bi-heart-pulse', 'items' => [
                    $s('ok-pulih', 'Monitoring ruang pulih'),
                ]],
            ],
            default => [],
        };
    }

    /**
     * Daftar "Perlu dikerjakan" di Ringkasan.
     * status: 'selesai' | 'draf' | 'belum'. Asesmen IGD membaca isian asli; lainnya CONTOH.
     */
    public static function tugas(string $unit, string $noReg): array
    {
        $r = fn (string $nama, array $p = []) => route("rekam-medis.$nama", [$noReg, ...$p]);
        $t = fn (string $label, string $status, string $ket, string $href = '#') => compact('label', 'status', 'ket', 'href');

        $asesmen = session("asesmen.$noReg");
        [$stAsesmen, $ketAsesmen] = match ($asesmen['status'] ?? null) {
            'final' => ['selesai', 'Disimpan final · ' . $asesmen['waktu']],
            'draf'  => ['draf', 'Draf · ' . $asesmen['waktu']],
            default => ['belum', 'Belum diisi'],
        };

        $periksa = session("pemeriksaan.$noReg");
        [$stPeriksa, $ketPeriksa] = match ($periksa['status'] ?? null) {
            'final' => ['selesai', 'Disimpan final · ' . $periksa['waktu']],
            'draf'  => ['draf', 'Draf · ' . $periksa['waktu']],
            default => ['belum', 'Belum diisi'],
        };

        return match ($unit) {
            'rajal' => [
                $t('Tanda vital', 'selesai', 'Terakhir 14:00', $r('ttv')),
                $t('Pemeriksaan dokter (SOAP)', $stPeriksa, $ketPeriksa, $r('pemeriksaan')),
                $t('E-Resep', 'belum', 'Belum ada resep', $r('resep')),
                $t('Surat kontrol', 'belum', 'Perlu dibuat', $r('dokumen', ['surat'])),
            ],
            'rm' => [
                $t('Asesmen dokter rehab medik', 'selesai', 'Kunjungan sebelumnya · 07 Okt'),
                $t('Program terapi sesi ini', 'belum', 'Diisi terapis'),
                $t('Jadwal sesi berikutnya', 'belum', 'Belum dijadwalkan'),
            ],
            'hd' => [
                $t('Pra-HD', 'selesai', '07:15'),
                $t('Resep HD', 'selesai', '07:20'),
                $t('Monitoring intra-HD', 'draf', '2 observasi tercatat'),
                $t('Pasca-HD & evaluasi', 'belum', 'Belum diisi'),
            ],
            'ranap' => [
                $t('Asesmen awal medis', 'selesai', 'Hari pertama rawat'),
                $t('Asesmen awal keperawatan', 'selesai', 'Hari pertama rawat'),
                $t('Monitoring tanda vital', 'selesai', 'Terakhir 14:00', $r('ttv')),
                $t('CPPT hari ini', 'belum', 'Belum ada catatan hari ini', $r('cppt')),
                $t('Discharge summary', 'belum', 'Diisi saat pasien pulang'),
            ],
            'ok' => [
                $t('Asesmen pra bedah', 'selesai', '08:30'),
                $t('Asesmen pra anestesi', 'selesai', '09:10'),
                $t('Penanda area operasi', 'selesai', '09:15'),
                $t('Checklist keselamatan operasi', 'draf', 'Sign in selesai · Time out & Sign out belum'),
                $t('Catatan operasi', 'belum', 'Diisi operator'),
                $t('Monitoring ruang pulih', 'belum', 'Setelah operasi'),
            ],
            default => [ // igd
                $t('Triase', 'selesai', '08:52', $r('asesmen')),
                $t('Asesmen awal keperawatan GD', $stAsesmen, $ketAsesmen, $r('asesmen')),
                $t('Tanda vital', 'selesai', 'Terakhir 14:00', $r('ttv')),
                $t('CPPT dokter', 'selesai', '10:21', $r('cppt')),
                $t('E-Resep', 'belum', 'Belum ada resep', $r('resep')),
                $t('Close kunjungan', 'belum', 'Setelah pasien pulang / dipindah'),
            ],
        };
    }
}