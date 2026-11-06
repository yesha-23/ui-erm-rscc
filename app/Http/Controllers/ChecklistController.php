<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use App\Support\KatalogChecklist;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;

class ChecklistController extends Controller
{
    public function index(string $noReg, string $kategori, ?string $slug = null)
    {
        $daftar = KatalogChecklist::daftar($kategori);
        $slug ??= $daftar[0]['slug']; // tanpa slug → buka form pertama
        $form = KatalogChecklist::form($kategori, $slug);
        abort_unless($form, 404);

        $tersimpan = session("checklist.$noReg.$kategori.$slug");

        return view('rekam-medis.checklist', [
            'pasien'    => DataContoh::pasien($noReg),
            'kategori'  => $kategori,
            'info'      => KatalogChecklist::KATEGORI[$kategori],
            'daftar'    => $daftar,
            'form'      => $form,
            'isi'       => $tersimpan['isi'] ?? [],
            'waktu'     => $tersimpan['waktu'] ?? null,
            'crumb'     => KatalogChecklist::KATEGORI[$kategori]['judul'],
            'menuAktif' => $kategori,
        ]);
    }

    public function simpan(Request $request, string $noReg, string $kategori, string $slug)
    {
        $form = KatalogChecklist::form($kategori, $slug);
        abort_unless($form, 404);

        $info  = KatalogChecklist::KATEGORI[$kategori];
        $opsi  = array_keys($info['opsi']);
        $kode  = collect($form['bagian'])->flatMap(fn ($butir) => array_keys($butir))->all();

        $aturan = [
            'jawab'   => ['required', 'array'],
            'tanggal' => ['required', 'date'],
            'shift'   => ['required', Rule::in(['pagi', 'siang', 'malam'])],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
        foreach ($kode as $k) {
            $aturan["jawab.$k"] = ['required', Rule::in($opsi)];
        }

        $request->validate($aturan, [
            'jawab.required'   => 'Masih ada butir yang belum dijawab.',
            'jawab.*.required' => 'Masih ada butir yang belum dijawab.',
            'jawab.*.in'       => 'Ada jawaban yang tidak valid.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'shift.required'   => 'Pilih shift.',
        ]);

        // Hanya butir yang ada di form ini yang disimpan & dihitung
        $jawab = Arr::only($request->input('jawab'), $kode);
        $skor  = KatalogChecklist::skor($kategori, $jawab);

        // TODO (IT RSCC): simpan ke tabel checklist (no_reg, kategori, form, jawaban, petugas, waktu).
        session()->put("checklist.$noReg.$kategori.$slug", [
            'isi'   => ['jawab' => $jawab, ...$request->only(['tanggal', 'shift', 'catatan'])],
            'waktu' => now()->format('H:i'),
        ]);

        return redirect()
            ->route('rekam-medis.checklist', [$noReg, $kategori, $slug])
            ->with('sukses', sprintf('%s tersimpan · %s %s.', $form['nama'], strtolower($info['skor']),
                $skor === null ? '–' : "$skor%"));
    }
}