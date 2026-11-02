<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use App\Support\Navigasi;

class BerandaController extends Controller
{
    public function __invoke()
    {
        $jam = now()->hour;
        $sapaan = match (true) {
            $jam < 11 => 'Selamat pagi',
            $jam < 15 => 'Selamat siang',
            $jam < 18 => 'Selamat sore',
            default   => 'Selamat malam',
        };

        $perUnit = collect(DataContoh::kunjunganPerUnit())->sortByDesc('jumlah')->values();

        return view('beranda', [
            'sapaan'       => $sapaan,
            'namaPengguna' => 'Zaki', // TODO (IT RSCC): ganti dengan nama pengguna yang login
            'ringkasan'    => DataContoh::ringkasan(),
            'perUnit'      => $perUnit,
            'tugas'        => DataContoh::tugas(),
            'unit'         => Navigasi::pelayanan(),
        ]);
    }
}