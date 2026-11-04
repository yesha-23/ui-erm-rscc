<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;

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

        // Pasien hari ini yang rekam medisnya belum beres (CONTOH: dari daftar IGD)
        // TODO (IT RSCC): ambil dari semua unit sesuai hak akses pengguna.
        $tindakLanjut = collect(DataContoh::kunjungan('igd'))
            ->map(function ($r) {
                $r['masalah'] = array_values(array_filter([
                    ! $r['brm'] ? 'BRM belum terisi' : null,
                    ! $r['close'] ? 'Belum close' : null,
                    ($r['penjamin'] === 'JKN' && ! $r['no_sep']) ? 'Belum cetak SEP' : null,
                ]));
                return $r;
            })
            ->filter(fn ($r) => count($r['masalah']) > 0)
            ->sortByDesc(fn ($r) => count($r['masalah']))
            ->values();

        return view('beranda', [
            'sapaan'         => $sapaan,
            'namaPengguna'   => 'Zaki', // TODO (IT RSCC): ganti dengan nama pengguna yang login
            'ringkasan'      => DataContoh::ringkasan(),
            'perUnit'        => $perUnit,
            'tugas'          => DataContoh::tugas(),
            'pasienTerakhir' => collect(session('pasien_terakhir', []))
                ->map(fn ($noReg) => DataContoh::pasien($noReg))
                ->all(),
            'tindakLanjut'   => $tindakLanjut->take(6),
            'jumlahTindak'   => $tindakLanjut->count(),
        ]);
    }
}