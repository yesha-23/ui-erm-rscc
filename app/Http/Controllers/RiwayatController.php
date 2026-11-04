<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    /** kunci => [label, ikon] */
    public const JENIS = [
        'kunjungan' => ['Kunjungan', 'bi-hospital'],
        'lab'       => ['Laboratorium', 'bi-droplet'],
        'radiologi' => ['Radiologi', 'bi-lungs'],
        'ekg'       => ['EKG', 'bi-heart-pulse'],
        'cppt'      => ['CPPT', 'bi-journal-text'],
        'kontrol'   => ['Surat kontrol', 'bi-calendar-check'],
    ];

    public function index(Request $request, string $noReg)
    {
        $semua  = collect(DataContoh::riwayatPemeriksaan());
        $filter = array_key_exists((string) $request->query('jenis'), self::JENIS) ? $request->query('jenis') : null;
        $tampil = $filter ? $semua->where('jenis', $filter) : $semua;

        return view('rekam-medis.riwayat', [
            'pasien'    => DataContoh::pasien($noReg),
            'jenis'     => self::JENIS,
            'filter'    => $filter,
            'kelompok'  => $tampil->groupBy('tanggal'), // dikelompokkan per tanggal
            'jumlah'    => $semua->countBy('jenis'),
            'total'     => $semua->count(),
            'crumb'     => 'Riwayat Pemeriksaan',
            'menuAktif' => 'riwayat',
        ]);
    }
}