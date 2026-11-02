<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;

class RekamMedisController extends Controller
{
    /** Halaman pertama ruang kerja pasien. Isi Ringkasan dikerjakan Anggota B (mockup 05a). */
    public function show(string $noReg)
    {
        return view('rekam-medis.show', [
            'pasien'    => DataContoh::pasien($noReg),
            'crumb'     => 'Ringkasan',
            'menuAktif' => 'ringkasan',
        ]);
    }
}