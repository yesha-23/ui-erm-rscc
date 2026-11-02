<?php

// Identitas RS untuk kop surat & tampilan cetak.
// IT RSCC: isi nilai asli di .env (RS_ALAMAT, RS_TELEPON, RS_EMAIL).
return [
    'nama'    => 'Rumah Sakit Condong Catur',
    'kota'    => env('RS_KOTA', 'Sleman'),
    'alamat'  => env('RS_ALAMAT', '[Alamat lengkap RS]'),
    'telepon' => env('RS_TELEPON', '[No. telepon]'),
    'email'   => env('RS_EMAIL', '[Email RS]'),
    'website' => env('RS_WEBSITE', 'rscc.biz.id'),
];