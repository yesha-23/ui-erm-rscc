<?php

namespace App\Support;

/**
 * Pengaturan tampilan daftar kunjungan per unit:
 * kolom tabel, isi kolom Kelengkapan, tab status, filter tambahan, dan rentang tanggal bawaan.
 */
class KonfigurasiUnit
{
    private static function kolom(string $label, string $tipe, ?string $field = null): array
    {
        return ['label' => $label, 'tipe' => $tipe, 'field' => $field];
    }

    /** Filter tambahan selain Cari, Tanggal daftar, dan Status bayar (yang selalu ada). */
    private static function filter(bool $unit = false, bool $ppa = false, bool $ruang = false): array
    {
        return compact('unit', 'ppa', 'ruang');
    }

    public static function ambil(string $unit): array
    {
        // Kolom pembuka yang sama di semua unit
        $awal = [
            self::kolom('No', 'nomor'),
            self::kolom('No. Registrasi', 'noreg'),
            self::kolom('Pasien', 'pasien'),
            self::kolom('Daftar', 'daftar'),
        ];
        $tujuan  = self::kolom('Unit tujuan', 'unit');
        $lengkap = self::kolom('Kelengkapan', 'kelengkapan');

        $semua  = ['semua' => ['label' => 'Semua', 'saring' => fn ($r) => true]];
        $brm    = ['brm' => ['label' => 'BRM belum terisi', 'saring' => fn ($r) => ! $r['brm']]];
        $cekBrm = ['brm' => ['BRM terisi', 'BRM belum terisi']];

        // Kolom rawat inap & kamar operasi
        $kolomInap = [...$awal,
            self::kolom('Kelas', 'kelas'),
            self::kolom('Ruang', 'ruang'),
            self::kolom('DPJP', 'teks', 'dpjp'),
            self::kolom('Diagnosa', 'teks', 'diagnosa'),
            self::kolom('Unit asal', 'teks', 'unit_asal')];
        $tabInap = $semua
            + ['tanpa_diagnosa' => ['label' => 'Belum ada diagnosa', 'saring' => fn ($r) => $r['diagnosa'] === '']];

        return match ($unit) {
            'igd' => [
                'kolom'  => [...$awal, $tujuan,
                             self::kolom('Dokter pengisi', 'teks', 'dokter_isi'),
                             self::kolom('Dokter memulangkan', 'teks', 'dokter_pulang'),
                             self::kolom('Perawat', 'teks', 'perawat'), $lengkap],
                'cek'    => $cekBrm + ['close' => ['Sudah close', 'Belum close']],
                'info'   => [],
                'tab'    => $semua
                            + ['belum_close' => ['label' => 'Belum close', 'saring' => fn ($r) => ! $r['close']]]
                            + $brm,
                'filter' => self::filter(),
                'rentang_hari' => 0,
            ],
            'hd' => [
                'kolom'  => [...$awal, $tujuan,
                             self::kolom('PPA 1', 'teks', 'ppa1'),
                             self::kolom('PPA 2', 'teks', 'ppa2'), $lengkap],
                'cek'    => $cekBrm,
                'info'   => [],
                'tab'    => $semua + $brm,
                'filter' => self::filter(),
                'rentang_hari' => 0,
            ],
            'rm', 'rajal' => [
                'kolom'  => [...$awal, $tujuan,
                             self::kolom('PPA 1', 'teks', 'ppa1'),
                             self::kolom('PPA 2', 'teks', 'ppa2'),
                             self::kolom('PPA farmasi', 'teks', 'ppa_farmasi'), $lengkap],
                // Wajib (✓ / !). Rujukan & surat kontrol bukan kewajiban → badge info netral, muncul kalau ada.
                'cek'    => $cekBrm,
                'info'   => $unit === 'rajal'
                            ? ['rujukan' => ['bi-box-arrow-up-right', 'Rujuk/Konsul'],
                               'kontrol' => ['bi-calendar-check', 'Surat kontrol']]
                            : [],
                'tab'    => $semua
                            + ['belum_datang' => ['label' => 'Belum datang', 'saring' => fn ($r) => ! $r['sudah_datang']],
                               'sudah_datang' => ['label' => 'Sudah datang', 'saring' => fn ($r) => $r['sudah_datang']]]
                            + $brm,
                'filter' => self::filter(unit: true, ppa: true),
                'rentang_hari' => 0,
            ],
            'ranap' => [
                'kolom'  => $kolomInap,
                'cek'    => [],
                'info'   => [],
                'tab'    => $tabInap,
                'filter' => self::filter(ruang: true),
                'rentang_hari' => 13, // 14 hari termasuk hari ini (pasien dirawat berhari-hari)
            ],
            default => [ // ok
                'kolom'  => $kolomInap,
                'cek'    => [],
                'info'   => [],
                'tab'    => $tabInap,
                'filter' => self::filter(),
                'rentang_hari' => 0,
            ],
        };
    }
}