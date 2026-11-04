<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use App\Support\RentangTtv;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class TtvController extends Controller
{
    public const KESADARAN = [
        'cm'       => 'Compos mentis',
        'apatis'   => 'Apatis',
        'somnolen' => 'Somnolen',
        'sopor'    => 'Sopor',
        'koma'     => 'Koma',
    ];

    public function index(string $noReg)
    {
        // Simulasi: observasi baru (session) + contoh, terbaru di atas
        $observasi = collect(array_merge(session("ttv.$noReg", []), DataContoh::observasiTtv()))
            ->sortByDesc('stempel')
            ->values()
            ->map(function ($o) {
                $o['waktu'] = Carbon::parse($o['stempel'])->locale('id')->translatedFormat('d M · H:i');
                return $o;
            });

        // Grafik: urut dari yang paling lama
        $urut = $observasi->reverse()->values();
        $grafik = [
            'label' => $urut->map(fn ($o) => Carbon::parse($o['stempel'])->format('H:i'))->all(),
            'seri'  => collect(RentangTtv::KOLOM)->mapWithKeys(fn ($k) => [$k['name'] => [
                'label' => $k['label'],
                'unit'  => $k['unit'],
                'min'   => $k['min'],
                'max'   => $k['max'],
                'data'  => $urut->map(fn ($o) => filled($o[$k['name']] ?? null) ? (float) $o[$k['name']] : null)->all(),
            ]])->all(),
        ];

        return view('rekam-medis.ttv', [
            'pasien'     => DataContoh::pasien($noReg),
            'kolom'      => RentangTtv::KOLOM,
            'kesadaran'  => self::KESADARAN,
            'observasi'  => $observasi,
            'terakhir'   => $observasi->first(),
            'sebelumnya' => $observasi->get(1),
            'grafik'     => $grafik,
            'crumb'      => 'Tanda Vital',
            'menuAktif'  => 'ttv',
        ]);
    }

    public function simpan(Request $request, string $noReg)
    {
        $data = $request->validate([
            'waktu'        => ['required', 'date_format:Y-m-d\TH:i'],
            'kesadaran'    => ['required', Rule::in(array_keys(self::KESADARAN))],
            'nyeri'        => ['nullable', 'integer', 'between:0,10'],
            'td_sistolik'  => ['required', 'integer', 'between:40,300', 'gt:td_diastolik'],
            'td_diastolik' => ['required', 'integer', 'between:20,200'],
            'nadi'         => ['required', 'integer', 'between:20,250'],
            'napas'        => ['required', 'integer', 'between:5,80'],
            'suhu'         => ['required', 'numeric', 'between:30,45'],
            'spo2'         => ['required', 'integer', 'between:50,100'],
        ], [
            'required'       => ':Attribute wajib diisi.',
            'integer'        => ':Attribute harus berupa angka bulat.',
            'numeric'        => ':Attribute harus berupa angka.',
            'between'        => ':Attribute harus antara :min dan :max.',
            'date_format'    => 'Format waktu tidak valid.',
            'td_sistolik.gt' => 'TD sistolik harus lebih besar dari diastolik.',
        ], [
            'td_sistolik' => 'TD sistolik', 'td_diastolik' => 'TD diastolik', 'nadi' => 'nadi',
            'napas' => 'frekuensi napas', 'suhu' => 'suhu', 'spo2' => 'SpO₂',
            'kesadaran' => 'kesadaran', 'nyeri' => 'skala nyeri', 'waktu' => 'waktu',
        ]);

        // TODO (IT RSCC): simpan ke tabel observasi_ttv; petugas dari akun yang login.
        $baru = collect($data)->except('waktu')->all() + [
            'stempel' => Carbon::createFromFormat('Y-m-d\TH:i', $data['waktu'])->format('Y-m-d H:i'),
            'oleh'    => '[Nama Pengguna]',
        ];
        session()->put("ttv.$noReg", array_merge([$baru], session("ttv.$noReg", [])));

        $luar = collect(RentangTtv::KOLOM)->filter(fn ($k) => RentangTtv::status($k['name'], $data[$k['name']]))->count();

        return redirect()
            ->route('rekam-medis.ttv', $noReg)
            ->with('sukses', 'Observasi tersimpan' . ($luar ? " · $luar nilai di luar batas normal." : ' · semua dalam batas normal.'));
    }
}