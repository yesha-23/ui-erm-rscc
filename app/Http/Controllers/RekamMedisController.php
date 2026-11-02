<?php

namespace App\Http\Controllers;

class RekamMedisController extends Controller
{
    public function show(string $noReg)
    {
        return view('rekam-medis.show', ['noReg' => $noReg]);
    }
}