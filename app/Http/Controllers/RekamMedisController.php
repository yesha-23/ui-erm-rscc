<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use App\Support\KonfigurasiRekamMedis;
use Illuminate\Support\Carbon;

class RekamMedisController extends Controller
{
    /** Ringkasan pasien: halaman pertama ruang kerja, isinya menyesuaikan unit kunjungan. */
    public function show(string $noReg)
    {
        $pasien = DataContoh::pasien($noReg);
        $tugas  = KonfigurasiRekamMedis::tugas($pasien['unit_kode'], $noReg);

        return view('rekam-medis.show', [
            'pasien'    => $pasien,
            'tugas'     => $tugas,
            'selesai'   => collect($tugas)->where('status', 'selesai')->count(),
            'kartu'     => DataContoh::kartuUnit($pasien),
            'ttv'       => $this->ttvRingkas(DataContoh::observasiTtv()[0]),
            'aktivitas' => DataContoh::aktivitasTerakhir(),
            'crumb'     => 'Ringkasan',
            'menuAktif' => 'ringkasan',
        ]);
    }

    /**
     * TTV terakhir + tanda tinggi/rendah.
     * Rentang normal dewasa sama dengan form asesmen (acuan; sesuaikan SPO RS).
     */
    private function ttvRingkas(array $o): array
    {
        $cek = fn ($n, $min, $maks) => $n < $min ? 'rendah' : ($n > $maks ? 'tinggi' : '');

        return [
            'waktu' => Carbon::parse($o['stempel'])->format('H:i'),
            'item'  => [
                ['TD',    "{$o['td_sistolik']}/{$o['td_diastolik']}", 'mmHg',
                          $cek($o['td_sistolik'], 90, 139) ?: $cek($o['td_diastolik'], 60, 89)],
                ['Nadi',  $o['nadi'], 'x/menit', $cek($o['nadi'], 60, 100)],
                ['Napas', $o['napas'], 'x/menit', $cek($o['napas'], 12, 20)],
                ['Suhu',  number_format($o['suhu'], 1, ',', ''), '°C', $cek($o['suhu'], 36, 37.5)],
                ['SpO₂',  $o['spo2'], '%', $cek($o['spo2'], 95, 100)],
                ['Nyeri', $o['nyeri'], 'dari 10', $o['nyeri'] >= 4 ? 'tinggi' : ''],
            ],
        ];
    }
}