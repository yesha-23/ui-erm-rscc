@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')
    <h1 class="h3 fw-bold mb-4">Pengaturan</h1>

    <div class="d-flex flex-column gap-3 rs-narrow">

        {{-- Ubah password --}}
        <section class="rs-card" id="password">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h2 class="h6 fw-bold mb-1">Ubah password</h2>
                    <p class="small rs-text-muted mb-0">Minimal 8 karakter, gabungan huruf dan angka. Jangan pakai tanggal lahir atau nomor HP.</p>
                </div>
                <div class="col-lg-8">
                    <form method="POST" action="{{ route('akun.password') }}" data-rs-loading novalidate>
                        @csrf
                        @foreach ([
                            ['password-lama',  'password_lama',         'Password lama',          'current-password'],
                            ['password-baru',  'password',              'Password baru',          'new-password'],
                            ['password-ulang', 'password_confirmation', 'Ulangi password baru',   'new-password'],
                        ] as [$id, $nama, $label, $auto])
                            <div class="mb-3">
                                <label for="{{ $id }}" class="form-label">{{ $label }}</label>
                                <div class="input-group has-validation">
                                    <input id="{{ $id }}" name="{{ $nama }}" type="password" autocomplete="{{ $auto }}"
                                           class="form-control @error($nama) is-invalid @enderror">
                                    <button type="button" class="btn btn-outline-secondary" data-rs-toggle-password="{{ $id }}"
                                            aria-label="Tampilkan password" aria-pressed="false">
                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                    </button>
                                    @error($nama) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                @if ($id === 'password-baru')
                                    <div class="rs-strength mt-2" data-rs-strength-for="password-baru" aria-live="polite">
                                        <div class="rs-strength-bar" aria-hidden="true"><span></span><span></span><span></span><span></span></div>
                                        <span class="rs-strength-text small rs-text-muted">Minimal 8 karakter, gabungan huruf dan angka.</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        <button type="submit" class="btn btn-primary">Simpan password</button>
                    </form>
                </div>
            </div>
        </section>

        {{-- Tampilan kerja + notifikasi (satu form) --}}
        <form method="POST" action="{{ route('akun.tampilan') }}" class="d-flex flex-column gap-3" data-rs-loading>
            @csrf

            <section class="rs-card">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <h2 class="h6 fw-bold mb-1">Tampilan kerja</h2>
                        <p class="small rs-text-muted mb-0">Atur halaman yang terbuka pertama kali dan kepadatan tabel.</p>
                    </div>
                    <div class="col-lg-8">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label for="halaman-awal" class="form-label">Halaman awal setelah masuk</label>
                                <select id="halaman-awal" name="halaman_awal" class="form-select">
                                    @foreach (['beranda' => 'Beranda', 'igd' => 'Daftar IGD', 'rajal' => 'Daftar Rawat Jalan'] as $nilai => $label)
                                        <option value="{{ $nilai }}" @selected($pengaturan['halaman_awal'] === $nilai)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="baris" class="form-label">Baris per halaman</label>
                                <select id="baris" name="baris" class="form-select">
                                    @foreach (['25', '50', '100'] as $nilai)
                                        <option value="{{ $nilai }}" @selected((string) $pengaturan['baris'] === $nilai)>{{ $nilai }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <span class="form-label d-block" id="label-kepadatan">Kepadatan tabel</span>
                                <div class="btn-group rs-segmented" role="group" aria-labelledby="label-kepadatan">
                                    @foreach (['nyaman' => 'Nyaman', 'padat' => 'Padat'] as $nilai => $label)
                                        <input type="radio" class="btn-check" name="kepadatan" id="kp-{{ $nilai }}"
                                               value="{{ $nilai }}" @checked($pengaturan['kepadatan'] === $nilai)>
                                        <label class="btn btn-outline-secondary" for="kp-{{ $nilai }}">{{ $label }}</label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rs-card">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <h2 class="h6 fw-bold mb-1">Notifikasi</h2>
                        <p class="small rs-text-muted mb-0">Pilih pengingat yang ingin Anda terima.</p>
                    </div>
                    <div class="col-lg-8">
                        @foreach ([
                            'inbox'   => ['Pesan baru di Inbox', 'Tampilkan angka di menu Inbox'],
                            'brm'     => ['BRM belum lengkap', 'Pengingat pasien hari ini yang rekam medisnya belum terisi'],
                            'kontrol' => ['Surat kontrol perlu dibuat', 'Pengingat saat pasien pulang tanpa surat kontrol'],
                        ] as $nilai => [$judul, $ket])
                            <div class="form-check form-switch rs-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="notif-{{ $nilai }}"
                                       name="notif[]" value="{{ $nilai }}" @checked(in_array($nilai, $pengaturan['notif']))>
                                <label class="form-check-label" for="notif-{{ $nilai }}">
                                    <span class="fw-semibold d-block">{{ $judul }}</span>
                                    <span class="small rs-text-muted">{{ $ket }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">Simpan pengaturan</button>
            </div>
        </form>
    </div>
@endsection