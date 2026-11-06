<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Sesi hemodialisa dalam 4 tahap sesuai urutan kerja perawat HD.
 * Tiap tahap disimpan sendiri karena diisi pada jam yang berbeda.
 * Simulasi di session "hd.{noReg}". IT RSCC: tabel sesi_hd (pra, resep, pasca) + sesi_hd_observasi (intra).
 */
class HemodialisaController extends Controller
{
    public const TAHAP = [
        'pra'   => ['label' => 'Pra-HD'],
        'resep' => ['label' => 'Resep HD'],
        'intra' => ['label' => 'Monitoring intra-HD'],
        'pasca' => ['label' => 'Pasca-HD & evaluasi'],
    ];

    /** Skala risiko jatuh Morse: kunci => [pertanyaan, [pilihan => skor]] */
    public const MORSE = [
        'riwayat'   => ['Riwayat jatuh baru atau dalam 3 bulan terakhir', ['Tidak' => 0, 'Ya' => 25]],
        'diagnosis' => ['Diagnosis medis sekunder lebih dari satu', ['Tidak' => 0, 'Ya' => 15]],
        'alat'      => ['Alat bantu jalan', ['Tidak ada / bed rest / dibantu perawat' => 0, 'Kruk / tongkat / walker' => 15, 'Berpegangan pada furnitur' => 30]],
        'infus'     => ['Terpasang infus / heparin lock', ['Tidak' => 0, 'Ya' => 20]],
        'berjalan'  => ['Cara berjalan / berpindah', ['Normal / bed rest / kursi roda' => 0, 'Lemah' => 10, 'Terganggu' => 20]],
        'mental'    => ['Status mental', ['Orientasi sesuai kemampuan' => 0, 'Lupa keterbatasan diri' => 15]],
    ];

    public const PENYULIT = [
        'Masalah akses', 'Perdarahan', 'First use syndrome', 'Sakit kepala', 'Mual / muntah', 'Kram otot',
        'Hipotensi', 'Hipertensi', 'Hiperkalemia', 'Nyeri dada', 'Aritmia', 'Gatal-gatal', 'Demam', 'Menggigil',
    ];

    private const PESAN = [
        'required'         => ':Attribute wajib diisi.',
        'required_with'    => ':Attribute wajib diisi bila ureum pasca-HD diisi.',
        'integer'          => ':Attribute harus angka bulat.',
        'numeric'          => ':Attribute harus angka.',
        'between'          => ':Attribute harus antara :min dan :max.',
        'in'               => 'Pilih :attribute.',
        'max'              => ':Attribute maksimal :max karakter.',
        'date'             => ':Attribute harus berupa tanggal.',
        'date_format'      => 'Format :attribute harus JJ:MM.',
        'after'            => ':Attribute harus setelah hari ini.',
        'td_sistolik.gt'   => 'TD sistolik harus lebih besar dari diastolik.',
        'ureum_post.lt'    => 'Ureum pasca-HD harus lebih kecil dari ureum pra-HD.',
        'jatuh.*.required' => 'Lengkapi semua butir risiko jatuh.',
        'jatuh.required'   => 'Lengkapi semua butir risiko jatuh.',
    ];

    private const NAMA = [
        'keadaan_umum' => 'keadaan umum', 'td_sistolik' => 'TD sistolik', 'td_diastolik' => 'TD diastolik',
        'napas' => 'frekuensi napas', 'bb_pre' => 'BB pre-HD', 'bb_kering' => 'BB kering', 'akses' => 'akses vaskular',
        'jenis' => 'jenis HD', 'qb' => 'QB', 'qd' => 'QD', 'uf_goal' => 'UF goal', 'heparin' => 'metode heparin',
        'heparin_awal' => 'dosis awal heparin', 'heparin_lanjut' => 'heparin lanjutan', 'uf_rate' => 'UF rate',
        'bb_post' => 'BB pasca-HD', 'ureum_pre' => 'ureum pra-HD', 'ureum_post' => 'ureum pasca-HD', 'ktv' => 'Kt/V',
        'discharge' => 'discharge planning', 'jadwal_berikut' => 'jadwal HD berikutnya',
    ];

    public function index(string $noReg, string $tahap = 'pra')
    {
        $sesi  = session("hd.$noReg", []);
        $pra   = $sesi['pra']['isi'] ?? [];
        $resep = $sesi['resep']['isi'] ?? [];
        $intra = $sesi['intra'] ?? [];

        $saranUf = isset($pra['bb_pre'], $pra['bb_kering'])
            ? max(0, round($pra['bb_pre'] - $pra['bb_kering'], 1)) : null;

        return view('rekam-medis.hd', [
            'pasien'      => DataContoh::pasien($noReg),
            'tahap'       => $tahap,
            'daftarTahap' => self::TAHAP,
            'status'      => [
                'pra'   => isset($sesi['pra']) ? 'selesai' : 'belum',
                'resep' => isset($sesi['resep']) ? 'selesai' : 'belum',
                'intra' => count($intra) ? 'draf' : 'belum',
                'pasca' => isset($sesi['pasca']) ? 'selesai' : 'belum',
            ],
            'isi'            => $tahap === 'intra' ? [] : ($sesi[$tahap]['isi'] ?? []),
            'simpanTerakhir' => $sesi[$tahap]['waktu'] ?? null,
            'pra'         => $pra,
            'resep'       => $resep,
            'intra'       => $intra,
            'saranUf'     => $saranUf,
            'lalu'        => DataContoh::hdSesiSebelumnya(),
            'sesiKe'      => 49,
            'morse'       => self::MORSE,
            'penyulit'    => self::PENYULIT,
            'crumb'       => 'Sesi Hemodialisa · ' . self::TAHAP[$tahap]['label'],
            'menuAktif'   => "hd-$tahap",
        ]);
    }

    public function simpan(Request $request, string $noReg, string $tahap)
    {
        $aturan = $this->aturan($tahap);
        $request->validate($aturan, self::PESAN, self::NAMA);

        $isian = collect($aturan)->keys()->reject(fn ($k) => str_contains($k, '.'))->all();
        $sesi  = session("hd.$noReg", []);

        if ($tahap === 'intra') {
            $sesi['intra'][] = $request->only($isian);
            usort($sesi['intra'], fn ($a, $b) => strcmp($a['jam'], $b['jam']));
            $pesan = 'Observasi jam ' . $request->input('jam') . ' ditambahkan.';
        } else {
            $sesi[$tahap] = ['isi' => $request->only($isian), 'waktu' => now()->format('H:i')];
            $pesan = self::TAHAP[$tahap]['label'] . ' tersimpan.';
        }

        session()->put("hd.$noReg", $sesi); // TODO (IT RSCC): simpan ke tabel sesi_hd

        return redirect()->route('rekam-medis.hd', [$noReg, $tahap])->with('sukses', $pesan);
    }

    public function hapusObservasi(string $noReg, int $indeks)
    {
        $sesi = session("hd.$noReg", []);
        if (isset($sesi['intra'][$indeks])) {
            array_splice($sesi['intra'], $indeks, 1);
            session()->put("hd.$noReg", $sesi);
        }

        return redirect()->route('rekam-medis.hd', [$noReg, 'intra'])->with('sukses', 'Observasi dihapus.');
    }

    private function aturan(string $tahap): array
    {
        $ttv = [
            'td_sistolik'  => ['required', 'integer', 'between:40,300', 'gt:td_diastolik'],
            'td_diastolik' => ['required', 'integer', 'between:20,200'],
            'nadi'         => ['required', 'integer', 'between:20,250'],
        ];

        return match ($tahap) {
            'pra' => $ttv + [
                'keluhan'      => ['nullable', 'string', 'max:500'],
                'keadaan_umum' => ['required', Rule::in(['baik', 'sedang', 'buruk'])],
                'napas'        => ['required', 'integer', 'between:5,80'],
                'suhu'         => ['required', 'numeric', 'between:30,45'],
                'bb_pre'       => ['required', 'numeric', 'between:20,250'],
                'bb_kering'    => ['required', 'numeric', 'between:20,250'],
                'akses'        => ['required', Rule::in(['avf', 'cdl', 'femoral'])],
                'jatuh'        => ['required', 'array'],
            ] + collect(self::MORSE)
                ->mapWithKeys(fn ($m, $k) => ["jatuh.$k" => ['required', Rule::in(array_values($m[1]))]])
                ->all(),

            'resep' => [
                'jenis'          => ['required', Rule::in(['inisiasi', 'rutin', 'akut', 'sled'])],
                'durasi'         => ['required', 'numeric', 'between:1,8'],
                'qb'             => ['required', 'integer', 'between:50,500'],
                'qd'             => ['required', 'integer', 'between:300,800'],
                'uf_goal'        => ['required', 'numeric', 'between:0,6'],
                'dialisat'       => ['required', Rule::in(['bikarbonat', 'asetat'])],
                'heparin'        => ['required', Rule::in(['reguler', 'minimal', 'bebas'])],
                'heparin_awal'   => ['nullable', 'integer', 'between:0,10000'],
                'heparin_lanjut' => ['nullable', 'integer', 'between:0,5000'],
            ],

            'intra' => $ttv + [
                'jam'     => ['required', 'date_format:H:i'],
                'qb'      => ['required', 'integer', 'between:50,500'],
                'uf_rate' => ['nullable', 'numeric', 'between:0,3'],
                'suhu'    => ['nullable', 'numeric', 'between:30,45'],
                'intake'  => ['nullable', 'integer', 'between:0,3000'],
                'catatan' => ['nullable', 'string', 'max:200'],
            ],

            'pasca' => $ttv + [
                'bb_post'        => ['required', 'numeric', 'between:20,250'],
                'ureum_pre'      => ['nullable', 'required_with:ureum_post', 'numeric', 'between:1,500'],
                'ureum_post'     => ['nullable', 'numeric', 'between:1,500', 'lt:ureum_pre'],
                'ktv'            => ['nullable', 'numeric', 'between:0,5'],
                'penyulit'       => ['nullable', 'array'],
                'penyulit.*'     => [Rule::in(self::PENYULIT)],
                'evaluasi'       => ['required', 'string', 'max:2000'],
                'discharge'      => ['nullable', 'string', 'max:1000'],
                'jadwal_berikut' => ['nullable', 'date', 'after:today'],
            ],
        };
    }
}