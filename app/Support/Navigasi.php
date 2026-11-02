<?php

namespace App\Support;

/**
 * Daftar menu sidebar utama (juga dipakai untuk pintasan di Beranda).
 * Catatan untuk IT RSCC: angka 'jumlah' nanti diambil dari query jumlah pasien.
 */
class Navigasi
{
    public static function pelayanan(): array
    {
        return [
            ['label' => 'IGD',                'icon' => 'bi-activity',         'route' => 'kunjungan.igd',   'ket' => 'Gawat darurat'],
            ['label' => 'Rawat Jalan',        'icon' => 'bi-clipboard2-pulse', 'route' => 'kunjungan.rajal', 'ket' => 'Poliklinik'],
            ['label' => 'Rehabilitasi Medik', 'icon' => 'bi-person-walking',   'route' => 'kunjungan.rm',    'ket' => 'Fisio, OT, terapi wicara'],
            ['label' => 'Hemodialisa',        'icon' => 'bi-droplet',          'route' => 'kunjungan.hd',    'ket' => 'Cuci darah'],
            [
                'label' => 'Rawat Inap', 'icon' => 'bi-hospital', 'route' => 'kunjungan.ranap', 'param' => 'ruang',
                'ket'   => 'Bangsal, ICU, HCU',
                'sub'   => [
                    ['label' => 'R. Inap Umum',      'value' => 'umum',      'jumlah' => 6],
                    ['label' => 'R. Inap Kebidanan', 'value' => 'kebidanan', 'jumlah' => 2],
                    ['label' => 'ICU',               'value' => 'icu',       'jumlah' => 1],
                    ['label' => 'HCU',               'value' => 'hcu',       'jumlah' => 0],
                ],
            ],
            [
                'label' => 'Kamar Operasi', 'icon' => 'bi-scissors', 'route' => 'kunjungan.ok', 'param' => 'jenis',
                'ket'   => 'OK mayor & minor',
                'sub'   => [
                    ['label' => 'OK Mayor', 'value' => 'mayor', 'jumlah' => 4],
                    ['label' => 'OK Minor', 'value' => 'minor', 'jumlah' => 1],
                ],
            ],
        ];
    }
}