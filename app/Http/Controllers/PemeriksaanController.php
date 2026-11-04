<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use App\Support\KatalogIcd;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Pemeriksaan dokter rawat jalan (format SOAP).
 * Simpan draf = tanpa validasi; simpan final = divalidasi.
 */
class PemeriksaanController extends Controller
{
    private const ISIAN = ['keluhan', 'rps', 'fisik', 'diagnosa', 'icd10', 'rencana', 'icd9',
                           'tindak_lanjut', 'tgl_kontrol', 'tujuan_rujuk', 'edukasi'];

    private const PESAN = [
        'required'             => ':Attribute wajib diisi.',
        'max'                  => ':Attribute maksimal :max karakter.',
        'date'                 => ':Attribute harus berupa tanggal.',
        'in'                   => 'Pilih :attribute.',
        'icd10.required'       => 'Pilih minimal satu kode ICD-10.',
        'icd10.min'            => 'Pilih minimal satu kode ICD-10.',
        'icd10.*.in'           => 'Ada kode ICD-10 yang tidak dikenal.',
        'icd9.*.in'            => 'Ada kode ICD-9 yang tidak dikenal.',
        'tgl_kontrol.after_or_equal' => 'Tanggal kontrol tidak boleh sebelum hari ini.',
    ];

    private const NAMA_ISIAN = [
        'keluhan' => 'keluhan utama', 'diagnosa' => 'diagnosa kerja', 'rencana' => 'rencana / terapi',
        'tindak_lanjut' => 'tindak lanjut', 'tgl_kontrol' => 'tanggal kontrol', 'tujuan_rujuk' => 'tujuan rujukan',
    ];

    public function index(string $noReg)
    {
        $tersimpan = session("pemeriksaan.$noReg"); // simulasi; IT: ambil dari database

        return view('rekam-medis.pemeriksaan', [
            'pasien'     => DataContoh::pasien($noReg),
            'isi'        => $tersimpan['isi'] ?? [],
            'status'     => $tersimpan['status'] ?? null,   // null | 'draf' | 'final'
            'waktu'      => $tersimpan['waktu'] ?? null,
            'ttv'        => DataContoh::observasiTtv()[0],
            'penunjang'  => collect(DataContoh::riwayatPemeriksaan())
                                ->whereIn('jenis', ['lab', 'radiologi'])->take(2)->values()->all(),
            'sebelumnya' => DataContoh::kunjunganSebelumnya(),
            'icd10'      => KatalogIcd::icd10(),
            'icd9'       => KatalogIcd::icd9(),
            'crumb'      => 'Pemeriksaan Rawat Jalan',
            'menuAktif'  => 'pemeriksaan',
        ]);
    }

    public function simpan(Request $request, string $noReg)
    {
        $final = $request->input('aksi') === 'final';

        if ($final) {
            $request->validate($this->aturanFinal(), self::PESAN, self::NAMA_ISIAN);
        }

        // TODO (IT RSCC): simpan ke tabel pemeriksaan_rajal + tabel diagnosa (ICD-10) & tindakan (ICD-9).
        session()->put("pemeriksaan.$noReg", [
            'isi'    => $request->only(self::ISIAN),
            'status' => $final ? 'final' : 'draf',
            'waktu'  => now()->format('H:i'),
        ]);

        $pesan = $final ? 'Pemeriksaan disimpan final.' : 'Draf pemeriksaan tersimpan.';
        if ($final && $request->input('tindak_lanjut') === 'kontrol') {
            $pesan .= ' Jangan lupa membuat surat kontrol.';
        }

        return redirect()->route('rekam-medis.pemeriksaan', $noReg)->with('sukses', $pesan);
    }

    private function aturanFinal(): array
    {
        return [
            'keluhan'       => ['required', 'string', 'max:1000'],
            'rps'           => ['nullable', 'string', 'max:2000'],
            'fisik'         => ['nullable', 'string', 'max:2000'],
            'diagnosa'      => ['required', 'string', 'max:500'],
            'icd10'         => ['required', 'array', 'min:1'],
            'icd10.*'       => [Rule::in(array_keys(KatalogIcd::icd10()))],
            'icd9'          => ['nullable', 'array'],
            'icd9.*'        => [Rule::in(array_keys(KatalogIcd::icd9()))],
            'rencana'       => ['required', 'string', 'max:2000'],
            'tindak_lanjut' => ['required', Rule::in(['pulang', 'kontrol', 'rujuk', 'ranap'])],
            'tgl_kontrol'   => ['exclude_unless:tindak_lanjut,kontrol', 'required', 'date', 'after_or_equal:today'],
            'tujuan_rujuk'  => ['exclude_unless:tindak_lanjut,rujuk', 'required', 'string', 'max:200'],
            'edukasi'       => ['nullable', 'string', 'max:1000'],
        ];
    }
}