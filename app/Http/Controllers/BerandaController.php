<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;

class BerandaController extends Controller
{
    public function __invoke()
    {
        return view('beranda', [
            'ringkasan' => DataContoh::ringkasan(),
            'perUnit'   => DataContoh::kunjunganPerUnit(),
        ]);
    }
}