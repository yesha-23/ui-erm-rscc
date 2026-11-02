<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;

class KunjunganController extends Controller
{
    private function tampil(string $judul, string $pj)
    {
        return view('kunjungan.index', [
            'judul' => $judul,
            'pj'    => $pj,
            'rows'  => DataContoh::kunjungan(),
        ]);
    }

    public function igd()         { return $this->tampil('IGD', 'B'); }
    public function hemodialisa() { return $this->tampil('Hemodialisa', 'A'); }
    public function rehabMedik()  { return $this->tampil('Rehabilitasi Medik', 'A'); }
    public function rawatJalan()  { return $this->tampil('Rawat Jalan', 'A'); }
    public function rawatInap()   { return $this->tampil('Rawat Inap', 'B'); }
    public function ok()          { return $this->tampil('Kamar Operasi', 'B'); }
}