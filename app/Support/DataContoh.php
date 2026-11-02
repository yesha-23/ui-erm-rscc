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
            ['unit' => 'Klinik Penyakit Dalam', 'jumlah' => 44],
            ['unit' => 'Klinik Syaraf',         'jumlah' => 29],
            ['unit' => 'Klinik Gigi dan Mulut', 'jumlah' => 29],
            ['unit' => 'Fisioterapi',           'jumlah' => 25],
            ['unit' => 'Laboratorium',          'jumlah' => 24],
            ['unit' => 'IGD',                   'jumlah' => 23],
        ];
    }

    public static function kunjungan(): array
    {
        return [
            [
                'no_rm' => '000123', 'nama' => 'Andi Pratama', 'no_reg' => '2611020001',
                'no_sep' => '0179R0000000000001', 'tgl_daftar' => '02-11-2026 07:32',
                'unit' => 'Okupasi Terapi, Terapi Wicara', 'penjamin' => 'JKN',
                'sudah_datang' => true, 'ppa' => 'Terapis Wicara A', 'brm_terisi' => true,
            ],
            [
                'no_rm' => '000124', 'nama' => 'Siti Rahmawati', 'no_reg' => '2611020002',
                'no_sep' => '', 'tgl_daftar' => '02-11-2026 09:28',
                'unit' => 'Klinik Rehabilitasi Medik', 'penjamin' => 'NON JKN',
                'sudah_datang' => false, 'ppa' => '', 'brm_terisi' => false,
            ],
        ];
    }
}