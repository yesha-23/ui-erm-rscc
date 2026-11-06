<?php

namespace App\Http\Middleware;

use App\Support\KonfigurasiRekamMedis;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Untuk setiap halaman rekam medis (GET):
 * 1. Menyimpan 5 pasien terakhir yang dibuka (ditampilkan di Beranda).
 * 2. Mengingat unit kunjungan saat tombol Buka diklik (?unit=...&sub=...).
 * IT RSCC: (1) bisa diganti log akses; (2) unit diambil dari tabel kunjungan.
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

            $unit = $request->query('unit');
            if (is_string($unit)) {
                $sub = $request->query('sub');
                KonfigurasiRekamMedis::simpanUnit($noReg, $unit, is_string($sub) ? $sub : null);
            }
        }

        return $next($request);
    }
}