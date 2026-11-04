<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;

class AsesmenController extends Controller
{
    /** Asesmen Awal Keperawatan Gawat Darurat */
    public function index(string $noReg)
    {
        return view('rekam-medis.asesmen', [
            'pasien'    => DataContoh::pasien($noReg),
            'langkah'   => ['Kedatangan', 'Objektif', 'Asesmen', 'Rencana'],
            'crumb'     => 'Asesmen Awal Gawat Darurat',
            'menuAktif' => 'asesmen', // sama dengan 'key' di menu-rm
        ]);
    }
}