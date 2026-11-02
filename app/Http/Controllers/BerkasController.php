<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Illuminate\Validation\Rule;

class BerkasController extends Controller
{
    public const JENIS = [
        'penunjang-luar' => 'Hasil penunjang dari luar',
        'rujukan'        => 'Surat rujukan',
        'identitas'      => 'Identitas / kartu penjamin',
        'lainnya'        => 'Lainnya',
    ];

    public function index(Request $request, string $noReg)
    {
        // Simulasi: berkas yang baru diunggah (session) tampil paling atas
        $semua = collect(array_merge(session("berkas.$noReg", []), DataContoh::berkasDigital()));

        $filter = $request->query('jenis');
        if (! array_key_exists((string) $filter, self::JENIS)) {
            $filter = null;
        }

        return view('rekam-medis.berkas', [
            'pasien'      => DataContoh::pasien($noReg),
            'jenisBerkas' => self::JENIS,
            'filter'      => $filter,
            'berkas'      => $filter ? $semua->where('jenis', $filter)->values() : $semua,
            'jumlah'      => $semua->countBy('jenis'),
            'total'       => $semua->count(),
            'crumb'       => 'Berkas Digital',
            'menuAktif'   => 'berkas',
        ]);
    }

    public function simpan(Request $request, string $noReg)
    {
        $data = $request->validate([
            'berkas'     => ['required', 'array', 'min:1', 'max:10'],
            'berkas.*'   => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // 10240 KB = 10 MB
            'jenis'      => ['required', Rule::in(array_keys(self::JENIS))],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'berkas.required' => 'Pilih minimal satu berkas.',
            'berkas.min'      => 'Pilih minimal satu berkas.',
            'berkas.max'      => 'Maksimal 10 berkas sekali unggah.',
            'berkas.*.file'   => 'Ada berkas yang gagal terunggah. Coba pilih ulang.',
            'berkas.*.mimes'  => 'Hanya berkas PDF, JPG, atau PNG yang diizinkan.',
            'berkas.*.max'    => 'Ada berkas yang lebih dari 10 MB.',
            'jenis.required'  => 'Pilih jenis berkas.',
            'jenis.in'        => 'Jenis berkas tidak dikenal.',
            'keterangan.max'  => 'Keterangan maksimal 255 karakter.',
        ]);

        // TODO (IT RSCC): simpan fisik berkas, mis. $file->store("berkas/$noReg", 'local'),
        // lalu catat ke tabel berkas (no_reg, jenis, path, ukuran, pengunggah).
        $baru = collect($request->file('berkas'))->map(fn ($file) => [
            'nama'       => $file->getClientOriginalName(),
            'jenis'      => $data['jenis'],
            'tipe'       => str_starts_with((string) $file->getMimeType(), 'image/') ? 'gambar' : 'pdf',
            'ukuran'     => Number::fileSize($file->getSize(), 1),
            'waktu'      => now()->locale('id')->translatedFormat('d M Y, H:i'),
            'oleh'       => '[Nama Pengguna]',
            'keterangan' => $data['keterangan'] ?? null,
        ])->all();

        session()->put("berkas.$noReg", array_merge($baru, session("berkas.$noReg", [])));

        return redirect()
            ->route('rekam-medis.berkas', $noReg)
            ->with('sukses', count($baru) . ' berkas berhasil diunggah.');
    }
}