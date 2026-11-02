<?php

namespace App\Support;

/**
 * Pengaturan tampilan daftar kunjungan per unit:
 * kolom tabel, isi kolom Kelengkapan, tab status, dan filter tambahan.
 */
class KonfigurasiUnit
{
    private static function kolom(string $label, string $tipe, ?string $field = null): array
    {
        return ['label' => $label, 'tipe' => $tipe, 'field' => $field];
    }

    public static function ambil(string $unit): array
    {
        $pasien = self::kolom('Pasien', 'pasien');
        $daftar = self::kolom('Daftar', 'daftar');
        $tujuan = self::kolom('Unit tujuan', 'unit');
        $lengkap = self::kolom('Kelengkapan', 'kelengkapan');

        $semua = ['semua' => ['label' => 'Semua', 'saring' => fn ($r) => true]];
        $brm   = ['brm' => ['label' => 'BRM belum terisi', 'saring' => fn ($r) => ! $r['brm']]];
        $cekBrm = ['brm' => ['BRM terisi', 'BRM belum terisi']];

        return match ($unit) {
            'igd' => [
                'kolom'  => [$pasien, $daftar, $tujuan,
                             self::kolom('Dokter pengisi', 'teks', 'dokter_isi'),
                             self::kolom('Dokter memulangkan', 'teks', 'dokter_pulang'),
                             self::kolom('Perawat', 'teks', 'perawat'), $lengkap],
                'cek'    => $cekBrm + ['close' => ['Sudah close', 'Belum close']],
                'tab'    => $semua
                            + ['belum_close' => ['label' => 'Belum close', 'saring' => fn ($r) => ! $r['close']]]
                            + $brm,
                'filter' => ['unit' => false, 'ppa' => false],
            ],
            'hd' => [
                'kolom'  => [$pasien, $daftar, $tujuan,
                             self::kolom('PPA 1', 'teks', 'ppa1'),
                             self::kolom('PPA 2', 'teks', 'ppa2'), $lengkap],
                'cek'    => $cekBrm,
                'tab'    => $semua + $brm,
                'filter' => ['unit' => false, 'ppa' => true],
            ],
            'rm', 'rajal' => [
                'kolom'  => [$pasien, $daftar, $tujuan,
                             self::kolom('PPA 1', 'teks', 'ppa1'),
                             self::kolom('PPA 2', 'teks', 'ppa2'),
                             self::kolom('PPA farmasi', 'teks', 'ppa_farmasi'), $lengkap],
                'cek'    => $unit === 'rajal'
                            ? $cekBrm + ['rujukan' => ['Rujukan/konsul ada', 'Rujukan/konsul belum'],
                                         'kontrol' => ['Surat kontrol ada', 'Surat kontrol belum']]
                            : $cekBrm,
                'tab'    => $semua
                            + ['belum_datang' => ['label' => 'Belum datang', 'saring' => fn ($r) => ! $r['sudah_datang']],
                               'sudah_datang' => ['label' => 'Sudah datang', 'saring' => fn ($r) => $r['sudah_datang']]]
                            + $brm,
                'filter' => ['unit' => true, 'ppa' => true],
            ],
            default => [ // ranap & ok
                'kolom'  => [$pasien, $daftar,
                             self::kolom('Kelas', 'kelas'),
                             self::kolom('Ruang', 'ruang'),
                             self::kolom('DPJP', 'teks', 'dpjp'),
                             self::kolom('Diagnosa', 'teks', 'diagnosa'),
                             self::kolom('Unit asal', 'teks', 'unit_asal')],
                'cek'    => [],
                'tab'    => $semua
                            + ['tanpa_diagnosa' => ['label' => 'Belum ada diagnosa', 'saring' => fn ($r) => $r['diagnosa'] === '']],
                'filter' => ['unit' => false, 'ppa' => false],
            ],
        };
    }
}