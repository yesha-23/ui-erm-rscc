<?php

namespace App\Support;

/**
 * Katalog ICD-10 (diagnosis) dan ICD-9-CM (tindakan) — CONTOH sebagian kecil.
 * Catatan untuk IT RSCC: ganti dengan master ICD lengkap dari database
 * (pencarian sebaiknya lewat endpoint, bukan dikirim semua ke halaman).
 */
class KatalogIcd
{
    public static function icd10(): array
    {
        return [
            'A01.0' => 'Demam tifoid',
            'A09'   => 'Diare dan gastroenteritis infeksi',
            'A90'   => 'Demam dengue',
            'A91'   => 'Demam berdarah dengue',
            'B34.9' => 'Infeksi virus, tidak spesifik',
            'E05.9' => 'Tirotoksikosis, tidak spesifik',
            'E11.9' => 'Diabetes melitus tipe 2 tanpa komplikasi',
            'E78.5' => 'Hiperlipidemia, tidak spesifik',
            'G40.9' => 'Epilepsi, tidak spesifik',
            'G43.9' => 'Migren, tidak spesifik',
            'H10.9' => 'Konjungtivitis, tidak spesifik',
            'H52.1' => 'Miopia',
            'H66.9' => 'Otitis media, tidak spesifik',
            'I10'   => 'Hipertensi esensial (primer)',
            'I11.9' => 'Penyakit jantung hipertensi tanpa gagal jantung',
            'I63.9' => 'Infark serebral, tidak spesifik',
            'J02.9' => 'Faringitis akut, tidak spesifik',
            'J06.9' => 'Infeksi saluran napas atas akut (ISPA)',
            'J18.9' => 'Pneumonia, tidak spesifik',
            'J45.9' => 'Asma, tidak spesifik',
            'K02.9' => 'Karies gigi, tidak spesifik',
            'K29.7' => 'Gastritis, tidak spesifik',
            'K30'   => 'Dispepsia fungsional',
            'M17.9' => 'Osteoartritis lutut, tidak spesifik',
            'M54.5' => 'Nyeri punggung bawah',
            'N39.0' => 'Infeksi saluran kemih',
            'R50.9' => 'Demam, tidak spesifik',
            'Z09.8' => 'Pemeriksaan lanjutan setelah pengobatan (kontrol)',
        ];
    }

    public static function icd9(): array
    {
        return [
            '23.09' => 'Ekstraksi gigi lainnya',
            '23.2'  => 'Restorasi gigi dengan tumpatan',
            '38.99' => 'Pungsi vena lainnya (ambil darah)',
            '86.22' => 'Debridemen luka',
            '86.59' => 'Penjahitan kulit dan jaringan subkutan',
            '87.44' => 'Rontgen thorax rutin',
            '89.52' => 'Elektrokardiogram (EKG)',
            '93.94' => 'Nebulisasi',
            '96.59' => 'Irigasi luka lainnya',
            '99.29' => 'Injeksi atau infus obat lainnya',
        ];
    }
}