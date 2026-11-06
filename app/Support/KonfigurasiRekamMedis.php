<?php

namespace App\Support;
use App\Http\Controllers\KeselamatanOperasiController;

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
    public static function grupKhusus(string $unit, \Closure $r): array
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
                    ['key' => 'hd-pra',   'label' => 'Pra-HD',              'href' => $r('hd', ['pra'])],
                    ['key' => 'hd-resep', 'label' => 'Resep HD',            'href' => $r('hd', ['resep'])],
                    ['key' => 'hd-intra', 'label' => 'Monitoring intra-HD', 'href' => $r('hd', ['intra'])],
                    ['key' => 'hd-pasca', 'label' => 'Pasca-HD & evaluasi', 'href' => $r('hd', ['pasca'])],
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
                    ['key' => 'ok-checklist', 'label' => 'Checklist keselamatan operasi', 'href' => $r('ok-checklist')],
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
     * status: 'selesai' | 'draf' | 'belum'. wajib=false → tidak menghalangi Submit final.
     * Asesmen IGD, Pemeriksaan rajal, dan Sesi HD membaca isian asli; lainnya CONTOH.
     */
    public static function tugas(string $unit, string $noReg): array
    {
        $r = fn (string $nama, array $p = []) => route("rekam-medis.$nama", [$noReg, ...$p]);
        $t = fn (string $label, string $status, string $ket, string $href = '#', bool $wajib = true)
            => compact('label', 'status', 'ket', 'href', 'wajib');

        // Status form yang punya draf/final
        $statusForm = fn (?array $data) => match ($data['status'] ?? null) {
            'final' => ['selesai', 'Disimpan final · ' . $data['waktu']],
            'draf'  => ['draf', 'Draf · ' . $data['waktu']],
            default => ['belum', 'Belum diisi'],
        };
        [$stAsesmen, $ketAsesmen] = $statusForm(session("asesmen.$noReg"));
        [$stPeriksa, $ketPeriksa] = $statusForm(session("pemeriksaan.$noReg"));

        // Sesi HD
        $hd     = session("hd.$noReg", []);
        $hdSt   = fn (string $k) => isset($hd[$k]) ? 'selesai' : 'belum';
        $hdKet  = fn (string $k) => isset($hd[$k]) ? 'Tersimpan · ' . $hd[$k]['waktu'] : 'Belum diisi';
        $jmlObs = count($hd['intra'] ?? []);
        
        // Checklist keselamatan operasi (3 tahap)
        [$stCekOk, $ketCekOk] = KeselamatanOperasiController::ringkas($noReg);

        return match ($unit) {
            'rajal' => [
                $t('Tanda vital', 'selesai', 'Terakhir 14:00', $r('ttv')),
                $t('Pemeriksaan dokter (SOAP)', $stPeriksa, $ketPeriksa, $r('pemeriksaan')),
                $t('E-Resep', 'belum', 'Bila ada terapi obat', $r('resep'), false),
                $t('Surat kontrol', 'belum', 'Bila pasien perlu kontrol', $r('dokumen', ['surat']), false),
            ],
            'rm' => [
                $t('Asesmen dokter rehab medik', 'selesai', 'Kunjungan sebelumnya · 07 Okt'),
                $t('Program terapi sesi ini', 'belum', 'Diisi terapis'),
                $t('Jadwal sesi berikutnya', 'belum', 'Belum dijadwalkan'),
            ],
            'hd' => [
                $t('Pra-HD', $hdSt('pra'), $hdKet('pra'), $r('hd', ['pra'])),
                $t('Resep HD', $hdSt('resep'), $hdKet('resep'), $r('hd', ['resep'])),
                $t('Monitoring intra-HD', $jmlObs ? 'selesai' : 'belum',
                   $jmlObs ? "$jmlObs observasi tercatat" : 'Belum ada observasi', $r('hd', ['intra'])),
                $t('Pasca-HD & evaluasi', $hdSt('pasca'), $hdKet('pasca'), $r('hd', ['pasca'])),
            ],
            'ranap' => [
                $t('Asesmen awal medis', 'selesai', 'Hari pertama rawat'),
                $t('Asesmen awal keperawatan', 'selesai', 'Hari pertama rawat'),
                $t('Monitoring tanda vital', 'selesai', 'Terakhir 14:00', $r('ttv')),
                $t('CPPT hari ini', 'belum', 'Catatan harian PPA', $r('cppt'), false),
                $t('Discharge summary', 'belum', 'Diisi saat pasien pulang'),
            ],
            'ok' => [
                $t('Asesmen pra bedah', 'selesai', '08:30'),
                $t('Asesmen pra anestesi', 'selesai', '09:10'),
                $t('Penanda area operasi', 'selesai', '09:15'),
                $t('Checklist keselamatan operasi', $stCekOk, $ketCekOk, $r('ok-checklist')),
                $t('Catatan operasi', 'belum', 'Diisi operator'),
                $t('Monitoring ruang pulih', 'belum', 'Setelah operasi'),
            ],
            default => [ // igd — "Close kunjungan" diganti tombol Submit final
                $t('Triase', 'selesai', '08:52', $r('asesmen')),
                $t('Asesmen awal keperawatan GD', $stAsesmen, $ketAsesmen, $r('asesmen')),
                $t('Tanda vital', 'selesai', 'Terakhir 14:00', $r('ttv')),
                $t('CPPT dokter', 'selesai', '10:21', $r('cppt')),
                $t('E-Resep', 'belum', 'Bila ada terapi obat', $r('resep'), false),
            ],
        };
    }

    /**
     * Form yang menghalangi Submit final: wajib, belum selesai, dan halamannya sudah tersedia.
     * (Form "Segera" tidak ikut diperiksa supaya alur tetap bisa didemokan.)
     */
    public static function penghalangFinal(array $tugas): array
    {
        return array_values(array_filter(
            $tugas,
            fn ($t) => $t['wajib'] && $t['status'] !== 'selesai' && $t['href'] !== '#'
        ));
    }
}