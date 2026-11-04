<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menyimpan 5 pasien terakhir yang dibuka (ditampilkan di Beranda).
 * IT RSCC: bisa diganti log akses rekam medis di database.
 */
class CatatPasienTerakhir
{
    public function handle(Request $request, Closure $next): Response
    {
        $noReg = $request->route('noReg');

        if ($noReg && $request->isMethod('GET')) {
            $daftar = array_values(array_diff(session('pasien_terakhir', []), [$noReg]));
            array_unshift($daftar, $noReg);                       // yang terbaru di depan
            session()->put('pasien_terakhir', array_slice($daftar, 0, 5));
        }

        return $next($request);
    }
}