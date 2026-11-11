<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use App\Support\KonfigurasiRekamMedis;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

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
            'ttv'       => $this->ttvRingkas(TtvController::terakhir($noReg)),
            'aktivitas' => DataContoh::aktivitasTerakhir(),
            'crumb'     => 'Ringkasan',
            'menuAktif' => 'ringkasan',
        ]);
    }

    /** Submit final: hanya bila semua form wajib sudah selesai. */
    public function simpanFinal(string $noReg)
    {
        $pasien     = DataContoh::pasien($noReg);
        $penghalang = KonfigurasiRekamMedis::penghalangFinal(
            KonfigurasiRekamMedis::tugas($pasien['unit_kode'], $noReg)
        );

        if ($penghalang) {
            return back()->with('gagal', 'Belum bisa final: '
                . implode(', ', array_column($penghalang, 'label')) . ' belum selesai.');
        }

        // TODO (IT RSCC): set kunjungan close + BRM terisi, kunci form, catat log siapa & kapan.
        session()->put("final.$noReg", [
            'waktu' => now()->format('H:i'),
            'oleh'  => DataContoh::pengguna()['nama'],
        ]);

        return redirect()->route('rekam-medis.show', $noReg)->with('sukses', 'Kunjungan disimpan final.');
    }

    /** Buka kembali kunjungan yang sudah final (untuk koreksi). */
    public function batalFinal(string $noReg)
    {
        // TODO (IT RSCC): catat alasan & siapa yang membuka kembali.
        session()->forget("final.$noReg");

        return back()->with('sukses', 'Kunjungan dibuka kembali untuk koreksi.');
    }

    /** Riwayat alergi pasien (berlaku untuk semua kunjungan, disimpan per No. RM). */
    public function simpanAlergi(Request $request, string $noReg)
    {
        $data = $request->validateWithBag('alergi', [
            'status'     => ['required', Rule::in(['tidak', 'ada'])],
            'keterangan' => ['exclude_unless:status,ada', 'required', 'string', 'max:200'],
        ], [
            'status.required'     => 'Pilih status alergi.',
            'keterangan.required' => 'Tuliskan alergi terhadap apa.',
            'keterangan.max'      => 'Keterangan alergi maksimal 200 karakter.',
        ]);

        $pasien = DataContoh::pasien($noReg);
        session()->put("alergi.{$pasien['no_rm']}", $data + ['waktu' => now()->format('d M Y, H:i')]);

        return back()->with('sukses', 'Riwayat alergi diperbarui.');
    }

    /**
     * TTV terakhir + tanda tinggi/rendah.
     * Rentang normal dewasa sama dengan form asesmen (acuan; sesuaikan SPO RS).
     */
    private function ttvRingkas(array $o): array
    {
        $cek = fn ($n, $min, $maks) => $n < $min ? 'rendah' : ($n > $maks ? 'tinggi' : '');

        return [
            'waktu' => Carbon::parse($o['stempel'])->locale('id')
                ->translatedFormat(Carbon::parse($o['stempel'])->isToday() ? 'H:i' : 'd M, H:i'),
            'item'  => [
                ['TD',    "{$o['td_sistolik']}/{$o['td_diastolik']}", 'mmHg',
                          $cek($o['td_sistolik'], 90, 139) ?: $cek($o['td_diastolik'], 60, 89)],
                ['Nadi',  $o['nadi'], 'x/menit', $cek($o['nadi'], 60, 100)],
                ['Napas', $o['napas'], 'x/menit', $cek($o['napas'], 12, 20)],
                ['Suhu',  number_format((float) $o['suhu'], 1, ',', ''), '°C', $cek($o['suhu'], 36, 37.5)],
                ['SpO₂',  $o['spo2'], '%', $cek($o['spo2'], 95, 100)],
                ['Nyeri', $o['nyeri'] ?? '–', 'dari 10', ($o['nyeri'] ?? 0) >= 4 ? 'tinggi' : ''],
            ],
        ];
    }
}