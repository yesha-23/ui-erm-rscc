<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use App\Support\KatalogDokumen;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    public function index(string $noReg, string $jenis = 'surat', ?string $slug = null)
    {
        $daftar  = KatalogDokumen::semua($jenis);
        $dokumen = $slug ? collect($daftar)->firstWhere('slug', $slug) : null;
        abort_if($slug && ! $dokumen, 404);

        return view('rekam-medis.dokumen', [
            'pasien'    => DataContoh::pasien($noReg),
            'jenis'     => $jenis,
            'daftar'    => $daftar,
            'dokumen'   => $dokumen, // null = belum memilih → pemilih terbuka
            'jumlah'    => KatalogDokumen::jumlah(),
            'crumb'     => $jenis === 'surat' ? 'Pembuatan Surat' : 'Persetujuan',
            'menuAktif' => $jenis,
        ]);
    }

    public function simpan(Request $request, string $noReg, string $jenis, string $slug)
    {
        $dokumen = collect(KatalogDokumen::semua($jenis))->firstWhere('slug', $slug);
        abort_unless($dokumen, 404);

        $final = $request->input('aksi') === 'final';

        // Draf boleh belum lengkap; "Simpan & tanda tangani" wajib lengkap.
        if ($final) {
            $aturan = match ($dokumen['form']) {
                'sakit'       => ['mulai' => 'required|date', 'sampai' => 'required|date|after_or_equal:mulai'],
                'sehat'       => ['keperluan' => 'required', 'kesimpulan' => 'required'],
                'rujukan'     => ['tujuan' => 'required', 'alasan' => 'required'],
                'persetujuan' => ['menyatakan' => 'required', 'setuju' => 'accepted'],
                default       => ['isi' => 'required'],
            };

            $request->validate($aturan, [
                'required'              => 'Kolom ini wajib diisi.',
                'sampai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
                'setuju.accepted'       => 'Centang pernyataan ini sebelum menandatangani.',
            ]);
        }

        // TODO (IT RSCC): simpan isi dokumen + tanda tangan ke database.
        return back()->withInput()->with('sukses', $final
            ? "{$dokumen['nama']} disimpan dan ditandatangani."
            : "{$dokumen['nama']} disimpan sebagai draf.");
    }
}