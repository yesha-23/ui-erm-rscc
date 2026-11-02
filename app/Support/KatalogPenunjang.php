<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Daftar pemeriksaan penunjang (CONTOH).
 * IT RSCC: ganti dengan tabel master pemeriksaan dari LIS (lab) / RIS (radiologi).
 */
class KatalogPenunjang
{
    public const JENIS = [
        'lab' => [
            'judul'  => 'Laboratorium',
            'tujuan' => 'laboratorium',
            'ikon'   => 'bi-eyedropper',
            'contoh' => 'darah rutin',
        ],
        'radiologi' => [
            'judul'  => 'Radiologi',
            'tujuan' => 'radiologi',
            'ikon'   => 'bi-lungs',
            'contoh' => 'thorax',
        ],
    ];

    // Tulis 'Nama' saja, atau 'Nama' => 'catatan persiapan' bila ada persiapan khusus.
    private const DAFTAR = [
        'lab' => [
            'Hematologi' => [
                'Darah rutin', 'Hemoglobin', 'Leukosit', 'Trombosit', 'Hematokrit', 'LED', 'Golongan darah',
            ],
            'Kimia klinik' => [
                'Gula darah sewaktu', 'Gula darah puasa' => 'Pasien perlu puasa', 'Ureum', 'Kreatinin',
                'SGOT', 'SGPT', 'Profil lipid' => 'Pasien perlu puasa', 'Asam urat',
            ],
            'Elektrolit' => ['Natrium', 'Kalium', 'Klorida'],
            'Serologi / imunologi' => [
                'Widal', 'NS1 Dengue', 'IgG / IgM Dengue', 'HBsAg', 'Anti-HIV' => 'Perlu persetujuan pasien',
            ],
            'Urin & feses' => ['Urin rutin', 'Feses rutin'],
        ],
        'radiologi' => [
            'Rontgen' => [
                'Thorax PA', 'Thorax AP / lateral', 'BNO', 'BNO 3 posisi' => 'Perlu persiapan usus',
                'Vertebra lumbal', 'Ekstremitas',
            ],
            'USG' => ['USG abdomen' => 'Pasien perlu puasa', 'USG ginjal', 'USG kandungan'],
            'CT scan' => ['CT scan kepala', 'CT scan abdomen' => 'Perlu persiapan khusus'],
        ],
    ];

    /** [['judul' => ..., 'items' => [['kode', 'nama', 'catatan'], ...]], ...] */
    public static function grup(string $jenis): array
    {
        return collect(self::DAFTAR[$jenis])->map(fn ($items, $judul) => [
            'judul' => $judul,
            'items' => collect($items)->map(function ($nilai, $kunci) {
                [$nama, $catatan] = is_int($kunci) ? [$nilai, null] : [$kunci, $nilai];

                return ['kode' => Str::slug($nama), 'nama' => $nama, 'catatan' => $catatan];
            })->values()->all(),
        ])->values()->all();
    }

    /** ['darah-rutin' => 'Darah rutin', ...] — untuk validasi & menampilkan nama */
    public static function peta(string $jenis): array
    {
        return collect(self::grup($jenis))
            ->flatMap(fn ($g) => collect($g['items'])->pluck('nama', 'kode'))
            ->all();
    }
}