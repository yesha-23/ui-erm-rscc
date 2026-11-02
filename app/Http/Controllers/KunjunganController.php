<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use App\Support\KonfigurasiUnit;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class KunjunganController extends Controller
{
    private const PER_HALAMAN = 8;

    public function igd(Request $r)         { return $this->tampil($r, 'igd', 'IGD'); }
    public function hemodialisa(Request $r) { return $this->tampil($r, 'hd', 'Hemodialisa'); }
    public function rehabMedik(Request $r)  { return $this->tampil($r, 'rm', 'Rehabilitasi Medik'); }
    public function rawatJalan(Request $r)  { return $this->tampil($r, 'rajal', 'Rawat Jalan'); }

    public function rawatInap(Request $r, string $ruang = 'umum')
    {
        $nama = ['umum' => 'R. Inap Umum', 'kebidanan' => 'R. Inap Kebidanan', 'icu' => 'ICU', 'hcu' => 'HCU'][$ruang];
        return $this->tampil($r, 'ranap', "Rawat Inap · {$nama}");
    }

    public function ok(Request $r, string $jenis = 'mayor')
    {
        return $this->tampil($r, 'ok', 'Kamar Operasi · OK ' . ucfirst($jenis));
    }

    /**
     * Satu fungsi untuk semua unit.
     * Catatan untuk IT: penyaringan di bawah memakai collection;
     * saat data sudah dari database, pindahkan ke query (where/like/paginate).
     */
    private function tampil(Request $request, string $unit, string $judul)
    {
        $konf = KonfigurasiUnit::ambil($unit);
        $data = collect(DataContoh::kunjungan($unit));

        // 1. Pencarian nama / No. RM
        if ($cari = trim((string) $request->query('q'))) {
            $data = $data->filter(fn ($r) =>
                str_contains(mb_strtolower($r['nama']), mb_strtolower($cari)) || str_contains($r['no_rm'], $cari));
        }

        // 2. Filter lanjutan
        if ($request->query('bayar') === 'belum') $data = $data->where('terbayar', false);
        if ($request->query('bayar') === 'sudah') $data = $data->where('terbayar', true);
        if ($unitTujuan = $request->query('unit')) $data = $data->where('unit_tujuan', $unitTujuan);
        if ($ppa = $request->query('ppa'))         $data = $data->where('ppa1', $ppa);

        // 3. Hitung jumlah per tab (dari data yang sudah difilter)
        $tabs = collect($konf['tab'])->map(fn ($t) => [
            'label'  => $t['label'],
            'jumlah' => $data->filter($t['saring'])->count(),
        ]);

        // 4. Terapkan tab yang aktif
        $tabAktif = array_key_exists($request->query('status'), $konf['tab']) ? $request->query('status') : 'semua';
        $data = $data->filter($konf['tab'][$tabAktif]['saring'])->values();

        // 5. Paginasi
        $halaman = LengthAwarePaginator::resolveCurrentPage();
        $rows = new LengthAwarePaginator(
            $data->forPage($halaman, self::PER_HALAMAN)->values(),
            $data->count(), self::PER_HALAMAN, $halaman,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // 6. Chip filter aktif
        $namaFilter = ['q' => 'Cari', 'bayar' => 'Status bayar', 'unit' => 'Unit', 'ppa' => 'PPA'];
        $nilaiBayar = ['belum' => 'Belum terbayar', 'sudah' => 'Sudah terbayar'];
        $chips = [];
        foreach ($namaFilter as $key => $nama) {
            $nilai = $request->query($key);
            if (filled($nilai)) {
                $chips[] = [
                    'label' => $nama . ': ' . ($key === 'bayar' ? ($nilaiBayar[$nilai] ?? $nilai) : $nilai),
                    'hapus' => $request->fullUrlWithoutQuery([$key, 'page']),
                ];
            }
        }

        // 7. Pilihan untuk filter Unit tujuan & PPA
        $semuaData = collect(DataContoh::kunjungan($unit));
        $pilihan = [
            'unit' => $konf['filter']['unit'] ? $semuaData->pluck('unit_tujuan')->unique()->sort()->values() : null,
            'ppa'  => $konf['filter']['ppa'] ? $semuaData->pluck('ppa1')->filter()->unique()->sort()->values() : null,
        ];

        $dari   = $request->query('dari', now()->toDateString());
        $sampai = $request->query('sampai', now()->toDateString());

        return view('kunjungan.index', [
            'judul'    => $judul,
            'kolom'    => $konf['kolom'],
            'cek'      => $konf['cek'],
            'tabs'     => $tabs,
            'tabAktif' => $tabAktif,
            'rows'     => $rows,
            'chips'    => $chips,
            'pilihan'  => $pilihan,
            'dari'     => $dari,
            'sampai'   => $sampai,
            'rentang'  => Carbon::parse($dari)->translatedFormat('d M Y')
                          . ($dari !== $sampai ? ' – ' . Carbon::parse($sampai)->translatedFormat('d M Y') : ''),
        ]);
    }
}