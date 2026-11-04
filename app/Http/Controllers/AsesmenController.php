<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use Illuminate\Http\Request;

class AsesmenController extends Controller
{
    /** Aturan yang hanya dipakai saat "Simpan final". Simpan draf tidak divalidasi. */
    private const ATURAN_FINAL = [
        'cara_datang'  => ['required'],
        'triase'       => ['required'],
        'keluhan'      => ['required', 'string', 'max:1000'],
        'keadaan_umum' => ['required'],
        'gcs_e'        => ['required', 'integer', 'between:1,4'],
        'gcs_v'        => ['required', 'integer', 'between:1,5'],
        'gcs_m'        => ['required', 'integer', 'between:1,6'],
        'td_sistolik'  => ['required', 'integer', 'between:40,300', 'gt:td_diastolik'],
        'td_diastolik' => ['required', 'integer', 'between:20,200'],
        'nadi'         => ['required', 'integer', 'between:20,250'],
        'napas'        => ['required', 'integer', 'between:5,80'],
        'suhu'         => ['required', 'numeric', 'between:30,45'],
        'spo2'         => ['required', 'integer', 'between:50,100'],
        'nyeri'        => ['required', 'integer', 'between:0,10'],
        'diagnosa'     => ['required', 'array', 'min:1'],
    ];

    private const PESAN = [
        'required'        => ':Attribute wajib diisi.',
        'integer'         => ':Attribute harus berupa angka bulat.',
        'numeric'         => ':Attribute harus berupa angka.',
        'between'         => ':Attribute harus antara :min dan :max.',
        'max'             => ':Attribute maksimal :max karakter.',
        'td_sistolik.gt'  => 'TD sistolik harus lebih besar dari diastolik.',
        'diagnosa.required' => 'Pilih minimal satu diagnosis keperawatan.',
        'diagnosa.min'      => 'Pilih minimal satu diagnosis keperawatan.',
    ];

    private const NAMA_ISIAN = [
        'cara_datang' => 'cara datang', 'triase' => 'triase', 'keluhan' => 'keluhan utama',
        'keadaan_umum' => 'keadaan umum', 'gcs_e' => 'GCS mata (E)', 'gcs_v' => 'GCS verbal (V)',
        'gcs_m' => 'GCS motorik (M)', 'td_sistolik' => 'TD sistolik', 'td_diastolik' => 'TD diastolik',
        'nadi' => 'nadi', 'napas' => 'frekuensi napas', 'suhu' => 'suhu', 'spo2' => 'SpO₂',
        'nyeri' => 'skala nyeri',
    ];

    public function index(string $noReg)
    {
        $tersimpan = session("asesmen.$noReg"); // simulasi; IT: ambil dari database

        return view('rekam-medis.asesmen', [
            'pasien'    => DataContoh::pasien($noReg),
            'langkah'   => ['Kedatangan', 'Objektif', 'Asesmen', 'Rencana'],
            'opsi'      => DataContoh::opsiAsesmenGd(),
            'isi'       => $tersimpan['isi'] ?? [],
            'status'    => $tersimpan['status'] ?? null,  // null | 'draf' | 'final'
            'waktu'     => $tersimpan['waktu'] ?? null,
            'crumb'     => 'Asesmen Awal Gawat Darurat',
            'menuAktif' => 'asesmen',
        ]);
    }

    public function simpan(Request $request, string $noReg)
    {
        $final = $request->input('aksi') === 'final';

        if ($final) {
            $request->validate(self::ATURAN_FINAL, self::PESAN, self::NAMA_ISIAN);
        }

        // TODO (IT RSCC): simpan ke tabel asesmen_gd (no_reg, isi, status, perawat, waktu).
        session()->put("asesmen.$noReg", [
            'isi'    => $request->except(['_token', 'aksi', 'langkah']),
            'status' => $final ? 'final' : 'draf',
            'waktu'  => now()->format('H:i'),
        ]);

        return redirect()
            ->route('rekam-medis.asesmen', $noReg)
            ->with('langkah', $request->integer('langkah')) // kembali ke langkah terakhir
            ->with('sukses', $final ? 'Asesmen disimpan final.' : 'Draf asesmen tersimpan.');
    }
}