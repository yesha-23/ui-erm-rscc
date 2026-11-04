<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResepController extends Controller
{
    public const RUTE = [
        'oral' => 'Oral', 'iv' => 'IV', 'im' => 'IM', 'sc' => 'SC',
        'topikal' => 'Topikal', 'inhalasi' => 'Inhalasi', 'rektal' => 'Rektal',
    ];

    public function index(string $noReg)
    {
        $tersimpan = session("resep.$noReg"); // simulasi; IT: ambil dari database

        return view('rekam-medis.resep', [
            'pasien'     => DataContoh::pasien($noReg),
            'katalog'    => DataContoh::katalogObat(),
            'rute'       => self::RUTE,
            'baris'      => old('items', $tersimpan['items'] ?? []),
            'catatan'    => old('catatan_resep', $tersimpan['catatan'] ?? ''),
            'status'     => $tersimpan['status'] ?? null, // null | 'draf' | 'terkirim'
            'waktu'      => $tersimpan['waktu'] ?? null,
            'sebelumnya' => DataContoh::resepSebelumnya(),
            'riwayat'    => DataContoh::riwayatPemberianObat(),
            'crumb'      => 'E-Resep',
            'menuAktif'  => 'resep',
        ]);
    }

    public function simpan(Request $request, string $noReg)
    {
        $kirim   = $request->input('aksi') === 'kirim';
        $katalog = collect(DataContoh::katalogObat())->keyBy('kode');

        // Draf boleh belum lengkap; "Kirim ke farmasi" wajib lengkap.
        $request->validate([
            'items'           => [$kirim ? 'required' : 'nullable', 'array', $kirim ? 'min:1' : 'min:0'],
            'items.*.kode'    => ['required', Rule::in($katalog->keys()->all())],
            'items.*.jumlah'  => [$kirim ? 'required' : 'nullable', 'integer', 'min:1', 'max:999'],
            'items.*.signa'   => [$kirim ? 'required' : 'nullable', 'string', 'max:50'],
            'items.*.rute'    => ['nullable', Rule::in(array_keys(self::RUTE))],
            'items.*.catatan' => ['nullable', 'string', 'max:100'],
            'catatan_resep'   => ['nullable', 'string', 'max:500'],
        ], [
            'items.required'          => 'Tambahkan minimal satu obat sebelum dikirim.',
            'items.min'               => 'Tambahkan minimal satu obat sebelum dikirim.',
            'items.*.kode.in'         => 'Ada obat yang tidak dikenal.',
            'items.*.jumlah.required' => 'Jumlah obat baris ke-:position wajib diisi.',
            'items.*.jumlah.integer'  => 'Jumlah obat baris ke-:position harus angka bulat.',
            'items.*.jumlah.min'      => 'Jumlah obat baris ke-:position minimal 1.',
            'items.*.signa.required'  => 'Aturan pakai baris ke-:position wajib diisi.',
        ]);

        $items = collect($request->input('items', []))->values()->map(fn ($b) => [
            'kode'    => $b['kode'],
            'nama'    => $katalog[$b['kode']]['nama'],
            'satuan'  => $katalog[$b['kode']]['satuan'],
            'jumlah'  => $b['jumlah'] ?? '',
            'signa'   => $b['signa'] ?? '',
            'rute'    => $b['rute'] ?? 'oral',
            'catatan' => $b['catatan'] ?? '',
        ])->all();

        // TODO (IT RSCC): simpan ke tabel resep + resep_detail, kirim ke depo farmasi.
        session()->put("resep.$noReg", [
            'items'   => $items,
            'catatan' => $request->input('catatan_resep'),
            'status'  => $kirim ? 'terkirim' : 'draf',
            'waktu'   => now()->format('H:i'),
        ]);

        return redirect()
            ->route('rekam-medis.resep', $noReg)
            ->with('sukses', $kirim
                ? 'Resep terkirim ke farmasi (' . count($items) . ' item).'
                : 'Draf resep tersimpan.');
    }
}