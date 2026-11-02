<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use App\Support\KatalogDokumen;
use Illuminate\Http\Request;

class CetakController extends Controller
{
    /** Halaman menu "Cetak" di ruang kerja pasien */
    public function index(string $noReg)
    {
        return view('rekam-medis.cetak', [
            'pasien'    => DataContoh::pasien($noReg),
            'tersimpan' => DataContoh::dokumenTersimpan(),
            'crumb'     => 'Cetak',
            'menuAktif' => 'cetak',
        ]);
    }

    /** Lembar A4 untuk surat / persetujuan. ?draf=1 → cap "DRAF" */
    public function dokumen(Request $request, string $noReg, string $jenis, string $slug)
    {
        $dokumen = collect(KatalogDokumen::semua($jenis))->firstWhere('slug', $slug);
        abort_unless($dokumen, 404);

        $pasien = DataContoh::pasien($noReg);

        return view('cetak.dokumen', [
            'pasien'  => $pasien,
            'dokumen' => $dokumen,
            'isi'     => DataContoh::isiDokumen($dokumen, $pasien),
            'draf'    => $request->boolean('draf'),
        ]);
    }

    /** Lembar A4 resume medis */
    public function resume(Request $request, string $noReg)
    {
        $pasien = DataContoh::pasien($noReg);

        return view('cetak.resume', [
            'pasien' => $pasien,
            'resume' => DataContoh::resumeMedis($pasien),
            'draf'   => $request->boolean('draf'),
        ]);
    }
}