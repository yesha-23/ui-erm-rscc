<?php

// Identitas RS untuk kop surat & tampilan cetak (sesuai kop resmi RS).
return [
    'badan_usaha' => env('RS_BADAN_USAHA', 'PT. Karya Mitra Pratama'),
    'nama'        => env('RS_NAMA', 'Rumah Sakit Condong Catur'),
    'kota'        => env('RS_KOTA', 'Sleman'),
    'alamat'      => env('RS_ALAMAT', 'Jl. Manggis No.6, Gempol, Condongcatur, Depok, Sleman, Yogyakarta'),
    'telepon'     => env('RS_TELEPON', '(0274) 887494, 4463083'),
    'email'       => env('RS_EMAIL', 'rscc_yogya@yahoo.co.id'),
    'website'     => env('RS_WEBSITE', 'www.rs-condongcatur.com'),
];