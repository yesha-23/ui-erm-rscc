@extends('layouts.app')
@section('title', 'Profil')

@section('content')
    <h1 class="h3 fw-bold mb-4">Profil saya</h1>

    <div class="row g-3">
        {{-- Kartu identitas --}}
        <div class="col-lg-4">
            <section class="rs-card h-100 d-flex flex-column align-items-center text-center gap-2">
                <span class="rs-avatar rs-avatar-xl" aria-hidden="true">{{ $pengguna['inisial'] }}</span>
                <h2 class="h5 fw-bold mt-2 mb-0">{{ $pengguna['nama'] }}</h2>
                <p class="rs-text-muted mb-1">{{ $pengguna['jabatan'] }} · {{ $pengguna['unit'] }}</p>
                <span class="rs-chip-static">Akses: {{ $pengguna['akses'] }}</span>
                <button type="button" class="btn btn-outline-secondary btn-sm mt-2">
                    <i class="bi bi-camera me-1" aria-hidden="true"></i>Ganti foto
                </button>
            </section>
        </div>

        <div class="col-lg-8 d-flex flex-column gap-3">
            {{-- Data akun --}}
            <section class="rs-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 fw-bold mb-0">Data akun</h2>
                    <a href="{{ route('akun.pengaturan') }}#password" class="small fw-semibold text-decoration-none">Ubah password</a>
                </div>
                <dl class="row g-3 mb-0 rs-dl">
                    @foreach ([
                        'Nama lengkap' => $pengguna['nama'],
                        'NIP'          => $pengguna['nip'],
                        'Profesi'      => $pengguna['jabatan'],
                        'Unit'         => $pengguna['unit'],
                        'Nomor HP'     => $pengguna['no_hp'],
                        'Email'        => $pengguna['email'],
                    ] as $label => $nilai)
                        <div class="col-sm-6">
                            <dt>{{ $label }}</dt>
                            <dd>{{ $nilai }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>

            {{-- Tanda tangan digital --}}
            <section class="rs-card">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <h2 class="h6 fw-bold mb-1">Tanda tangan digital</h2>
                        <p class="small rs-text-muted mb-0">Dipakai otomatis di asesmen, surat, dan persetujuan yang Anda tanda tangani.</p>
                    </div>
                    <div class="col-md-4">
                        <div class="rs-signature">[Gambar tanda tangan]</div>
                    </div>
                    <div class="col-md-3 text-md-end">
                        {{-- TODO (IT RSCC): proses unggah dan simpan gambar tanda tangan --}}
                        <label class="btn btn-primary btn-sm mb-0" for="ttd">
                            <i class="bi bi-upload me-1" aria-hidden="true"></i>Perbarui
                        </label>
                        <input type="file" id="ttd" accept="image/png,image/jpeg" class="visually-hidden">
                    </div>
                </div>
            </section>

            {{-- Riwayat masuk --}}
            <section class="rs-card p-0 overflow-hidden">
                <h2 class="h6 fw-bold mb-0 px-4 pt-4 pb-3">Riwayat masuk</h2>
                <div class="table-responsive">
                    <table class="table rs-table rs-table-compact mb-0">
                        <thead>
                            <tr><th scope="col">Waktu</th><th scope="col">Perangkat</th><th scope="col">Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($riwayat as $r)
                                <tr>
                                    <td>{{ $r['waktu'] }}</td>
                                    <td class="rs-text-muted">{{ $r['perangkat'] }}</td>
                                    <td>
                                        @if ($r['berhasil'])
                                            <x-badge jenis="sukses">Berhasil</x-badge>
                                        @else
                                            <x-badge jenis="gagal">{{ $r['alasan'] }}</x-badge>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection