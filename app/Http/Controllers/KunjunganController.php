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

    /** Pilihan Status bayar (label ikut sistem lama). Bawaan: belum terbayar. */
    private const STATUS_BAYAR = [
        'semua'    => 'Semua',
        'belum'    => 'Belum terbayar',
        'terbayar' => 'Terbayar',
    ];

    public function igd(Request $r)         { return $this->tampil($r, 'igd', 'IGD'); }
    public function hemodialisa(Request $r) { return $this->tampil($r, 'hd', 'Hemodialisa'); }
    public function rehabMedik(Request $r)  { return $this->tampil($r, 'rm', 'Rehabilitasi Medik'); }
    public function rawatJalan(Request $r)  { return $this->tampil($r, 'rajal', 'Rawat Jalan'); }

    public function rawatInap(Request $r, string $ruang = 'umum')
    {
        $nama = ['umum' => 'R. Inap Umum', 'kebidanan' => 'R. Inap Kebidanan', 'icu' => 'ICU', 'hcu' => 'HCU'][$ruang];
        return $this->tampil($r, 'ranap', "Rawat Inap · {$nama}", $ruang);
    }

    public function ok(Request $r, string $jenis = 'mayor')
    {
        return $this->tampil($r, 'ok', 'Kamar Operasi · OK ' . ucfirst($jenis), $jenis);
    }

    /**
     * Satu fungsi untuk semua unit.
     * Catatan untuk IT: penyaringan di bawah memakai collection;
     * saat data sudah dari database, pindahkan ke query (where/whereBetween/like/paginate).
     */
    private function tampil(Request $request, string $unit, string $judul, ?string $sub = null)
    {
        $konf = KonfigurasiUnit::ambil($unit);

        // Semua data unit ini, urut tanggal & jam daftar (seperti sistem lama)
        $semuaData = collect(DataContoh::kunjungan($unit, $sub))
            ->sortBy(fn ($r) => $r['tanggal'] . ' ' . $r['jam'])
            ->values();

        // 1. Baca isian filter (nilai aneh di URL diganti nilai bawaan)
        $dari   = $this->tanggal($request->query('dari'), now()->subDays($konf['rentang_hari'])->toDateString());
        $sampai = $this->tanggal($request->query('sampai'), now()->toDateString());
        if ($dari > $sampai) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        $nilai = [
            'q'       => $this->teks($request, 'q'),
            'bayar'   => $this->statusBayar($request),
            'unit'    => $konf['filter']['unit'] ? $this->teks($request, 'unit') : '',
            'ppa'     => $konf['filter']['ppa'] ? $this->teks($request, 'ppa') : '',
            'bangsal' => $konf['filter']['ruang'] ? $this->teks($request, 'bangsal') : '',
        ];

        // 2. Saring data
        $cari = mb_strtolower($nilai['q']);
        $data = $semuaData
            ->filter(fn ($r) => $r['tanggal'] >= $dari && $r['tanggal'] <= $sampai)
            ->when($cari !== '', fn ($c) => $c->filter(fn ($r) =>
                str_contains(mb_strtolower($r['nama']), $cari)
                || str_contains($r['no_rm'], $cari)
                || str_contains($r['no_reg'], $cari)))
            ->when($nilai['bayar'] !== 'semua', fn ($c) => $c->where('terbayar', $nilai['bayar'] === 'terbayar'))
            ->when($nilai['unit'] !== '', fn ($c) => $c->where('unit_tujuan', $nilai['unit']))
            ->when($nilai['ppa'] !== '', fn ($c) => $c->filter(fn ($r) => in_array($nilai['ppa'], [$r['ppa1'], $r['ppa2']], true)))
            ->when($nilai['bangsal'] !== '', fn ($c) => $c->where('ruang', $nilai['bangsal']));

        // 3. Jumlah per tab (dari data yang sudah difilter), lalu terapkan tab aktif
        $tabs = collect($konf['tab'])->map(fn ($t) => [
            'label'  => $t['label'],
            'jumlah' => $data->filter($t['saring'])->count(),
        ]);
        $status   = $request->query('status');
        $tabAktif = is_string($status) && array_key_exists($status, $konf['tab']) ? $status : 'semua';
        $data     = $data->filter($konf['tab'][$tabAktif]['saring'])->values();

        // 4. Paginasi
        $halaman = LengthAwarePaginator::resolveCurrentPage();
        $rows = new LengthAwarePaginator(
            $data->forPage($halaman, self::PER_HALAMAN)->values(),
            $data->count(), self::PER_HALAMAN, $halaman,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // 5. Pilihan dropdown (diambil dari semua data unit ini, urut abjad)
        $urut = fn ($daftar) => $daftar->filter()->unique()->sortBy(fn ($v) => mb_strtolower($v))->values()->all();
        $pilihan = [
            'bayar'   => self::STATUS_BAYAR,
            'unit'    => $konf['filter']['unit'] ? $urut($semuaData->pluck('unit_tujuan')) : [],
            'ppa'     => $konf['filter']['ppa'] ? $urut($semuaData->pluck('ppa1')->merge($semuaData->pluck('ppa2'))) : [],
            'bangsal' => $konf['filter']['ruang'] ? $urut($semuaData->pluck('ruang')) : [],
        ];
        if (count($pilihan['bangsal']) < 2) {
            $pilihan['bangsal'] = []; // ICU/HCU hanya satu ruang → filter tidak perlu tampil
        }

        // Jumlah filter yang tidak bawaan (angka di tombol "Filter lain" versi HP)
        $jumlahFilter = collect([$nilai['bayar'] !== 'belum', $nilai['unit'], $nilai['ppa'], $nilai['bangsal']])
            ->filter()->count();

        $formatTgl = fn ($t) => Carbon::parse($t)->translatedFormat('d M Y');

        return view('kunjungan.index', [
            'judul'        => $judul,
            'kolom'        => $konf['kolom'],
            'cek'          => $konf['cek'],
            'info'         => $konf['info'],
            'tabs'         => $tabs,
            'tabAktif'     => $tabAktif,
            'rows'         => $rows,
            'pilihan'      => $pilihan,
            'nilai'        => $nilai,
            'jumlahFilter' => $jumlahFilter,
            'dari'         => $dari,
            'sampai'       => $sampai,
            'keterangan'   => $nilai['bayar'] === 'semua'
                              ? 'semua status bayar'
                              : mb_strtolower(self::STATUS_BAYAR[$nilai['bayar']]),
            'rentang'      => $formatTgl($dari) . ($dari !== $sampai ? ' – ' . $formatTgl($sampai) : ''),
        ]);
    }

    /** Ambil isian teks dari URL (selain teks dianggap kosong). */
    private function teks(Request $request, string $kunci): string
    {
        $isi = $request->query($kunci);
        return is_string($isi) ? trim($isi) : '';
    }

    private function statusBayar(Request $request): string
    {
        $isi = $this->teks($request, 'bayar');
        if ($isi === 'sudah') {
            return 'terbayar'; // tautan lama
        }
        return array_key_exists($isi, self::STATUS_BAYAR) ? $isi : 'belum';
    }

    /** Tanggal harus berbentuk Y-m-d yang valid; kalau tidak, pakai bawaan. */
    private function tanggal(mixed $isi, string $bawaan): string
    {
        if (is_string($isi) && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $isi, $m)
            && checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            return $isi;
        }
        return $bawaan;
    }
}