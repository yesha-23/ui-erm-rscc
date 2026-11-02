<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use App\Support\KatalogPenunjang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermintaanController extends Controller
{
    public function index(string $noReg, string $jenis = 'lab')
    {
        return view('rekam-medis.permintaan', [
            'pasien'     => DataContoh::pasien($noReg),
            'jenis'      => $jenis,
            'info'       => KatalogPenunjang::JENIS[$jenis],
            'semuaJenis' => KatalogPenunjang::JENIS,
            'grup'       => KatalogPenunjang::grup($jenis),
            'dipilih'    => old('items', []), // tetap tercentang bila validasi gagal
            // Simulasi: permintaan yang baru dikirim (session) tampil paling atas
            'riwayat'    => array_merge(session("permintaan.$noReg", []), DataContoh::permintaanPenunjang()),
            'crumb'      => 'Permintaan Pemeriksaan',
            'menuAktif'  => 'permintaan',
        ]);
    }

    public function kirim(Request $request, string $noReg, string $jenis)
    {
        $peta = KatalogPenunjang::peta($jenis);

        $data = $request->validate([
            'items'     => ['required', 'array', 'min:1'],
            'items.*'   => [Rule::in(array_keys($peta))],
            'prioritas' => ['required', Rule::in(['cito', 'rutin'])],
            'diagnosa'  => ['required', 'string', 'max:500'],
            'catatan'   => ['nullable', 'string', 'max:500'],
        ], [
            'items.required'     => 'Pilih minimal satu pemeriksaan.',
            'items.min'          => 'Pilih minimal satu pemeriksaan.',
            'items.*.in'         => 'Ada pemeriksaan yang tidak dikenal.',
            'prioritas.required' => 'Pilih prioritas Cito atau Rutin.',
            'diagnosa.required'  => 'Diagnosa / catatan klinis wajib diisi.',
            'diagnosa.max'       => 'Diagnosa maksimal 500 karakter.',
            'catatan.max'        => 'Catatan maksimal 500 karakter.',
        ]);

        // TODO (IT RSCC): simpan ke tabel permintaan & kirim ke LIS/RIS.
        $baru = [
            'waktu'     => now()->locale('id')->translatedFormat('d M Y, H:i'),
            'jenis'     => $jenis,
            'items'     => array_map(fn ($kode) => $peta[$kode], $data['items']),
            'prioritas' => $data['prioritas'],
            'status'    => 'terkirim',
            'oleh'      => '[Nama Pengguna]',
        ];
        session()->put("permintaan.$noReg", array_merge([$baru], session("permintaan.$noReg", [])));

        $info = KatalogPenunjang::JENIS[$jenis];

        return redirect()
            ->route('rekam-medis.permintaan', [$noReg, $jenis])
            ->with('sukses', sprintf(
                'Permintaan %d pemeriksaan %s terkirim (%s).',
                count($data['items']),
                $info['tujuan'],
                $data['prioritas'] === 'cito' ? 'CITO' : 'rutin'
            ));
    }
}