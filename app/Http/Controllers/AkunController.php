<?php

namespace App\Http\Controllers;

use App\Support\DataContoh;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    /** Nilai bawaan pengaturan pengguna. */
    public const PENGATURAN_AWAL = [
        'halaman_awal' => 'beranda',
        'baris'        => '25',
        'kepadatan'    => 'nyaman',
        'notif'        => ['inbox', 'brm'],
    ];

    public function profil()
    {
        return view('akun.profil', [
            'pengguna' => DataContoh::pengguna(),
            'riwayat'  => DataContoh::riwayatMasuk(),
        ]);
    }

    public function inbox(?int $id = null)
    {
        $pesan = collect(DataContoh::pesan());
        $terpilih = $id ? $pesan->firstWhere('id', $id) : null;
        abort_if($id && ! $terpilih, 404);

        return view('akun.inbox', [
            'pesan'      => $pesan,
            'terpilih'   => $terpilih,
            'belumDibaca'=> $pesan->where('dibaca', false)->count(),
        ]);
    }

    public function pengaturan(Request $request)
    {
        return view('akun.pengaturan', [
            'pengaturan' => $request->session()->get('pengaturan', self::PENGATURAN_AWAL),
        ]);
    }

    public function simpanPassword(Request $request)
    {
        $request->validate([
            'password_lama' => ['required'],
            'password'      => ['required', 'min:8', 'regex:/[A-Za-z]/', 'regex:/[0-9]/', 'confirmed'],
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password.required'      => 'Password baru wajib diisi.',
            'password.min'           => 'Password baru minimal 8 karakter.',
            'password.regex'         => 'Password baru harus berisi huruf dan angka.',
            'password.confirmed'     => 'Ulangi password tidak sama dengan password baru.',
        ]);

        // TODO (IT RSCC): cocokkan password lama, lalu simpan hash password baru ke database.
        return back()->with('sukses', 'Password berhasil diubah.');
    }

    public function simpanTampilan(Request $request)
    {
        $data = $request->validate([
            'halaman_awal' => ['required', 'in:beranda,igd,rajal'],
            'baris'        => ['required', 'in:25,50,100'],
            'kepadatan'    => ['required', 'in:nyaman,padat'],
            'notif'        => ['array'],
            'notif.*'      => ['in:inbox,brm,kontrol'],
        ]);
        $data['notif'] = $data['notif'] ?? [];

        // TODO (IT RSCC): simpan ke tabel pengaturan pengguna (sekarang hanya di session).
        $request->session()->put('pengaturan', $data);

        return back()->with('sukses', 'Pengaturan tersimpan.');
    }
}