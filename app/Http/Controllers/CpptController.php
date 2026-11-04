<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CpptController extends Controller
{
    public const PROFESI = [
        'dokter'  => 'Dokter',
        'perawat' => 'Perawat',
        'bidan'   => 'Bidan',
        'farmasi' => 'Apoteker',
        'gizi'    => 'Ahli gizi',
    ];

    public const FORMAT = [
        'soap' => [
            'judul'  => 'CPPT (SOAP)',
            'bagian' => [
                's' => ['Subjective', 'Keluhan pasien'],
                'o' => ['Objective', 'Hasil pemeriksaan, TTV'],
                'a' => ['Assessment', 'Masalah / diagnosis'],
                'p' => ['Plan', 'Rencana tindakan'],
            ],
        ],
        'sbar' => [
            'judul'  => 'SBAR',
            'bagian' => [
                's' => ['Situation', 'Kondisi pasien saat ini'],
                'b' => ['Background', 'Riwayat dan latar belakang'],
                'a' => ['Assessment', 'Penilaian Anda'],
                'r' => ['Recommendation', 'Usulan / permintaan'],
            ],
        ],
    ];

    /** Isian wajib per format (dipakai validasi & tanda * di form) */
    public const WAJIB = ['soap.s', 'soap.a', 'sbar.s', 'sbar.r'];

    public function index(Request $request, string $noReg)
    {
        $verifikasi = session("cppt_verif.$noReg", []); // simulasi: [id => waktu]

        // Simulasi: catatan baru (session) di atas catatan contoh
        $semua = collect(array_merge(session("cppt.$noReg", []), DataContoh::cppt()))
            ->map(function ($c) use ($verifikasi) {
                if (! $c['verifikasi'] && isset($verifikasi[$c['id']])) {
                    $c['verifikasi'] = ['oleh' => 'dr. [Nama DPJP]', 'waktu' => $verifikasi[$c['id']]];
                }
                return $c;
            });

        $menunggu = fn ($c) => $c['perlu_verifikasi'] && ! $c['verifikasi'];

        $jumlah = [
            'semua'    => $semua->count(),
            'menunggu' => $semua->filter($menunggu)->count(),
            'dokter'   => $semua->where('profesi', 'dokter')->count(),
            'perawat'  => $semua->where('profesi', 'perawat')->count(),
        ];

        $tampil = array_key_exists((string) $request->query('tampil'), $jumlah) ? $request->query('tampil') : 'semua';

        $catatan = match ($tampil) {
            'menunggu'          => $semua->filter($menunggu),
            'dokter', 'perawat' => $semua->where('profesi', $tampil),
            default             => $semua,
        };

        return view('rekam-medis.cppt', [
            'pasien'        => DataContoh::pasien($noReg),
            'formatCatatan' => self::FORMAT,
            'profesiList'   => self::PROFESI,
            'wajib'         => self::WAJIB,
            'catatan'       => $catatan->values(),
            'jumlah'        => $jumlah,
            'tampil'        => $tampil,
            'ttvTerakhir'   => $this->ttvTerakhir($noReg),
            'crumb'         => 'CPPT / SBAR',
            'menuAktif'     => 'cppt',
        ]);
    }

    public function simpan(Request $request, string $noReg)
    {
        $data = $request->validate([
            'format'    => ['required', Rule::in(array_keys(self::FORMAT))],
            'profesi'   => ['required', Rule::in(array_keys(self::PROFESI))],
            'soap.s'    => ['nullable', 'required_if:format,soap', 'string', 'max:2000'],
            'soap.o'    => ['nullable', 'string', 'max:2000'],
            'soap.a'    => ['nullable', 'required_if:format,soap', 'string', 'max:2000'],
            'soap.p'    => ['nullable', 'string', 'max:2000'],
            'sbar.s'    => ['nullable', 'required_if:format,sbar', 'string', 'max:2000'],
            'sbar.b'    => ['nullable', 'string', 'max:2000'],
            'sbar.a'    => ['nullable', 'string', 'max:2000'],
            'sbar.r'    => ['nullable', 'required_if:format,sbar', 'string', 'max:2000'],
            'instruksi' => ['nullable', 'string', 'max:1000'],
        ], [
            'soap.s.required_if' => 'Subjective wajib diisi.',
            'soap.a.required_if' => 'Assessment wajib diisi.',
            'sbar.s.required_if' => 'Situation wajib diisi.',
            'sbar.r.required_if' => 'Recommendation wajib diisi.',
            'profesi.required'   => 'Pilih profesi PPA.',
            'max'                => 'Isian terlalu panjang (maksimal :max karakter).',
        ]);

        $format = $data['format'];
        $dokter = $data['profesi'] === 'dokter';
        $waktu  = now()->locale('id')->translatedFormat('d M · H:i');

        // TODO (IT RSCC): simpan ke tabel cppt; PPA & profesi diambil dari akun yang login.
        $baru = [
            'id'               => (string) Str::ulid(),
            'waktu'            => $waktu,
            'ppa'              => '[Nama PPA]',
            'profesi'          => $data['profesi'],
            'format'           => $format,
            'isi'              => array_filter($data[$format] ?? [], 'filled'),
            'instruksi'        => $data['instruksi'] ?? null,
            'perlu_verifikasi' => ! $dokter,
            'verifikasi'       => $dokter ? ['oleh' => '[Nama PPA]', 'waktu' => $waktu] : null,
        ];
        session()->put("cppt.$noReg", array_merge([$baru], session("cppt.$noReg", [])));

        return redirect()
            ->route('rekam-medis.cppt', $noReg)
            ->with('sukses', 'Catatan ' . self::FORMAT[$format]['judul'] . ' tersimpan'
                . ($dokter ? '.' : ' dan menunggu verifikasi DPJP.'));
    }

    public function verifikasi(string $noReg, string $id)
    {
        // TODO (IT RSCC): hanya DPJP pasien ini yang boleh memverifikasi.
        session()->put("cppt_verif.$noReg.$id", now()->locale('id')->translatedFormat('d M · H:i'));

        return back()->with('sukses', 'Catatan terverifikasi.');
    }

    /** TTV terakhir: dari Asesmen GD (bila sudah diisi), kalau belum pakai contoh. */
    private function ttvTerakhir(string $noReg): string
    {
         // Observasi terbaru dari halaman Tanda vital; kalau belum ada, dari Asesmen GD
        $a = collect(session("ttv.$noReg", []))->sortByDesc('stempel')->first()
            ?? session("asesmen.$noReg.isi", []);

        if (filled($a['td_sistolik'] ?? null)) {
            return sprintf(
                'TD %s/%s mmHg, N %s x/mnt, RR %s x/mnt, S %s °C, SpO₂ %s %%',
                $a['td_sistolik'], $a['td_diastolik'] ?? '-', $a['nadi'] ?? '-',
                $a['napas'] ?? '-', str_replace('.', ',', (string) ($a['suhu'] ?? '-')), $a['spo2'] ?? '-'
            );
        }

        return 'TD 120/70 mmHg, N 102 x/mnt, RR 22 x/mnt, S 40,1 °C, SpO₂ 98 %';
    }
}