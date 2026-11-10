<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Asesmen Awal Keperawatan Gawat Darurat (IGD).
 * Isi dan nama isian mengikuti form ERM lama (igd_aak): bagian atas + tab OBJECTIVE, ASSESSMENT, PLANNING.
 */
class AsesmenController extends Controller
{
    /** Aturan yang hanya dipakai saat "Simpan final". Simpan draf tidak divalidasi. */
    private const ATURAN_FINAL = [
        'tgl_periksa'  => ['required', 'date'],
        'jam_periksa'  => ['required', 'date_format:H:i'],
        'cara_datang'  => ['required'],
        'gcs_e'        => ['required', 'integer', 'between:1,4'],
        'gcs_v'        => ['required', 'integer', 'between:1,5'],
        'gcs_m'        => ['required', 'integer', 'between:1,6'],
        'kesadaran'    => ['required'],
        'td_sistolik'  => ['required', 'integer', 'between:40,300', 'gt:td_diastolik'],
        'td_diastolik' => ['required', 'integer', 'between:20,200'],
        'nadi'         => ['required', 'integer', 'between:20,250'],
        'napas'        => ['required', 'integer', 'between:5,80'],
        'suhu'         => ['required', 'numeric', 'between:30,45'],
        'spo2'         => ['required', 'integer', 'between:50,100'],
        'nyeri'        => ['required', 'integer', 'between:0,10'],
        'jatuh.a'      => ['required'],
        'jatuh.b'      => ['required'],
        'diagnosa'     => ['required', 'array', 'min:1'],
    ];

    private const PESAN = [
        'required'                => ':Attribute wajib diisi.',
        'integer'                 => ':Attribute harus berupa angka bulat.',
        'numeric'                 => ':Attribute harus berupa angka.',
        'between'                 => ':Attribute harus antara :min dan :max.',
        'max'                     => ':Attribute maksimal :max karakter.',
        'date'                    => ':Attribute tidak valid.',
        'date_format'             => ':Attribute tidak valid (format jam:menit).',
        'td_sistolik.gt'          => 'TD sistolik harus lebih besar dari diastolik.',
        'diagnosa.required'       => 'Pilih minimal satu diagnosis keperawatan.',
        'diagnosa.min'            => 'Pilih minimal satu diagnosis keperawatan.',
        'diagnosa_lain.required'  => 'Tuliskan diagnosis keperawatan "Lainnya".',
    ];

    private const NAMA_ISIAN = [
        'tgl_periksa' => 'tgl periksa', 'jam_periksa' => 'jam periksa', 'cara_datang' => 'cara pasien datang',
        'gcs_e' => 'GCS E', 'gcs_v' => 'GCS V', 'gcs_m' => 'GCS M', 'kesadaran' => 'kesadaran',
        'td_sistolik' => 'TD sistolik', 'td_diastolik' => 'TD diastolik', 'nadi' => 'nadi',
        'napas' => 'RR', 'suhu' => 'suhu', 'spo2' => 'SpO₂', 'nyeri' => 'skala nyeri',
        'jatuh.a' => 'risiko jatuh no. 1', 'jatuh.b' => 'risiko jatuh no. 2',
        'diagnosa_lain' => 'diagnosis lainnya',
    ];

    public function index(string $noReg)
    {
        $tersimpan = session("asesmen.$noReg"); // simulasi; IT: ambil dari database

        return view('rekam-medis.asesmen', [
            'pasien'    => DataContoh::pasien($noReg),
            'langkah'   => ['Objective', 'Assessment', 'Planning'],
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
            // Diagnosis "Lainnya" dicentang → isiannya wajib
            $aturan = self::ATURAN_FINAL + [
                'diagnosa_lain' => [
                    Rule::requiredIf(in_array('lainnya', (array) $request->input('diagnosa', []), true)),
                    'nullable', 'string', 'max:300',
                ],
            ];
            $request->validate($aturan, self::PESAN, self::NAMA_ISIAN);
        }

        // TODO (IT RSCC): simpan ke tabel asesmen_gd (no_reg, isi, status, perawat, waktu).
        session()->put("asesmen.$noReg", [
            'isi'    => $request->except(['_token', 'aksi', 'langkah']),
            'status' => $final ? 'final' : 'draf',
            'waktu'  => now()->format('H:i'),
        ]);

        return redirect()
            ->route('rekam-medis.asesmen', $noReg)
            ->with('langkah', $request->integer('langkah')) // kembali ke tab terakhir
            ->with('sukses', $final ? 'Asesmen disimpan final.' : 'Draf asesmen tersimpan.');
    }
}