<?php

namespace App\Support;

/**
 * Daftar tanda vital + rentang normal dewasa (ACUAN CONTOH).
 * Dipakai komponen x-form.ttv, halaman Tanda vital, dan grafik.
 * IT RSCC / klinisi: sesuaikan dengan SPO RS.
 */
class RentangTtv
{
    public const KOLOM = [
        ['name' => 'td_sistolik',  'label' => 'TD sistolik',  'unit' => 'mmHg',    'min' => 90, 'max' => 139,  'step' => '1'],
        ['name' => 'td_diastolik', 'label' => 'TD diastolik', 'unit' => 'mmHg',    'min' => 60, 'max' => 89,   'step' => '1'],
        ['name' => 'nadi',         'label' => 'Nadi',         'unit' => 'x/menit', 'min' => 60, 'max' => 100,  'step' => '1'],
        ['name' => 'napas',        'label' => 'Napas',        'unit' => 'x/menit', 'min' => 12, 'max' => 20,   'step' => '1'],
        ['name' => 'suhu',         'label' => 'Suhu',         'unit' => '°C',      'min' => 36, 'max' => 37.5, 'step' => '0.1'],
        ['name' => 'spo2',         'label' => 'SpO₂',         'unit' => '%',       'min' => 95, 'max' => 100,  'step' => '1'],
    ];

    /** 'tinggi' | 'rendah' | null (normal / kosong) */
    public static function status(string $name, $nilai): ?string
    {
        if ($nilai === null || $nilai === '') {
            return null;
        }
        $k = collect(self::KOLOM)->firstWhere('name', $name);
        if (! $k) {
            return null;
        }
        $v = (float) $nilai;

        return $v < $k['min'] ? 'rendah' : ($v > $k['max'] ? 'tinggi' : null);
    }

    /** 40.1 → "40,1"; kosong → "–" */
    public static function angka($nilai): string
    {
        return ($nilai === null || $nilai === '') ? '–' : str_replace('.', ',', (string) $nilai);
    }
}