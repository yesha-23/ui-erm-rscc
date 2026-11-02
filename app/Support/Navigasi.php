<?php

namespace App\Support;

/**
 * Daftar menu sidebar utama.
 * Catatan untuk IT RSCC: angka 'jumlah' nanti diambil dari query jumlah pasien.
 */
class Navigasi
{
    public static function pelayanan(): array
    {
        return [
            ['label' => 'IGD',                'icon' => 'bi-activity',         'route' => 'kunjungan.igd'],
            ['label' => 'Rawat Jalan',        'icon' => 'bi-clipboard2-pulse', 'route' => 'kunjungan.rajal'],
            ['label' => 'Rehabilitasi Medik', 'icon' => 'bi-person-walking',   'route' => 'kunjungan.rm'],
            ['label' => 'Hemodialisa',        'icon' => 'bi-droplet',          'route' => 'kunjungan.hd'],
            [
                'label' => 'Rawat Inap', 'icon' => 'bi-hospital', 'route' => 'kunjungan.ranap', 'param' => 'ruang',
                'sub' => [
                    ['label' => 'R. Inap Umum',      'value' => 'umum',      'jumlah' => 6],
                    ['label' => 'R. Inap Kebidanan', 'value' => 'kebidanan', 'jumlah' => 2],
                    ['label' => 'ICU',               'value' => 'icu',       'jumlah' => 1],
                    ['label' => 'HCU',               'value' => 'hcu',       'jumlah' => 0],
                ],
            ],
            [
                'label' => 'Kamar Operasi', 'icon' => 'bi-scissors', 'route' => 'kunjungan.ok', 'param' => 'jenis',
                'sub' => [
                    ['label' => 'OK Mayor', 'value' => 'mayor', 'jumlah' => 4],
                    ['label' => 'OK Minor', 'value' => 'minor', 'jumlah' => 1],
                ],
            ],
        ];
    }
}