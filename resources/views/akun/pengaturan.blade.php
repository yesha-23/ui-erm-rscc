@extends('layouts.app')
@section('title', 'Pengaturan')

@section('content')
<div class="d-flex flex-column gap-3">

    <div>
        <h1 class="h3 fw-bold mb-1">Pengaturan</h1>
        <p class="rs-text-muted mb-0">Atur keamanan akun, tampilan kerja, dan pengingat.</p>
    </div>

    {{-- ===== Ubah password ===== --}}
    <section class="rs-card rs-setelan" id="password" aria-labelledby="judul-password">
        <div class="rs-setelan-info">
            <span class="rs-setelan-ikon" aria-hidden="true"><i class="bi bi-shield-lock"></i></span>
            <div>
                <h2 id="judul-password" class="h6 fw-bold mb-1">Ubah password</h2>
                <p class="small rs-text-muted mb-0">Minimal 8 karakter, gabungan huruf dan angka. Jangan pakai tanggal lahir atau nomor HP.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('akun.password') }}" data-rs-loading novalidate>
            @csrf
            @foreach ([
                ['password-lama',  'password_lama',         'Password lama',        'current-password'],
                ['password-baru',  'password',              'Password baru',        'new-password'],
                ['password-ulang', 'password_confirmation', 'Ulangi password baru', 'new-password'],
            ] as [$id, $nama, $label, $auto])
                <div class="mb-3">
                    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
                    <div class="input-group rs-input-password has-validation">
                        <input id="{{ $id }}" name="{{ $nama }}" type="password" autocomplete="{{ $auto }}"
                               class="form-control @error($nama) is-invalid @enderror">
                        <button type="button" class="btn" data-rs-toggle-password="{{ $id }}"
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

            <div class="rs-setelan-aksi">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-key me-1" aria-hidden="true"></i>Simpan password
                </button>
            </div>
        </form>
    </section>

    {{-- ===== Tampilan kerja + notifikasi (satu form) ===== --}}
    <form method="POST" action="{{ route('akun.tampilan') }}" class="d-flex flex-column gap-3" data-rs-loading>
        @csrf

        <section class="rs-card rs-setelan" id="tampilan" aria-labelledby="judul-tampilan">
            <div class="rs-setelan-info">
                <span class="rs-setelan-ikon" aria-hidden="true"><i class="bi bi-layout-text-window-reverse"></i></span>
                <div>
                    <h2 id="judul-tampilan" class="h6 fw-bold mb-1">Tampilan kerja</h2>
                    <p class="small rs-text-muted mb-0">Atur halaman yang terbuka pertama kali dan kepadatan tabel.</p>
                </div>
            </div>

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
                            <option value="{{ $nilai }}" @selected((string) $pengaturan['baris'] === $nilai)>{{ $nilai }} baris</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <fieldset class="rs-pilihan-grup">
                        <legend class="rs-legend">Kepadatan tabel</legend>
                        <div class="rs-segmented rs-segmented-inline">
                            @foreach (['nyaman' => ['Nyaman', 'bi-distribute-vertical'], 'padat' => ['Padat', 'bi-list']] as $nilai => [$label, $ikon])
                                <input type="radio" class="btn-check" name="kepadatan" id="kp-{{ $nilai }}"
                                       value="{{ $nilai }}" autocomplete="off" @checked($pengaturan['kepadatan'] === $nilai)>
                                <label class="rs-segmented-item" for="kp-{{ $nilai }}">
                                    <i class="bi {{ $ikon }} me-1" aria-hidden="true"></i>{{ $label }}
                                </label>
                            @endforeach
                        </div>
                        <div class="form-text">Nyaman: baris lebih lega. Padat: lebih banyak pasien terlihat tanpa menggulir.</div>
                    </fieldset>
                </div>
            </div>
        </section>

        <section class="rs-card rs-setelan" id="notifikasi" aria-labelledby="judul-notifikasi">
            <div class="rs-setelan-info">
                <span class="rs-setelan-ikon" aria-hidden="true"><i class="bi bi-bell"></i></span>
                <div>
                    <h2 id="judul-notifikasi" class="h6 fw-bold mb-1">Notifikasi</h2>
                    <p class="small rs-text-muted mb-0">Pilih pengingat yang ingin Anda terima.</p>
                </div>
            </div>

            <div class="rs-setelan-daftar">
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
        </section>

        <div class="rs-setelan-simpan">
            <span class="small rs-text-muted">
                <i class="bi bi-info-circle me-1" aria-hidden="true"></i>Tampilan dan notifikasi disimpan bersamaan.
            </span>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan pengaturan
            </button>
        </div>
    </form>
</div>
@endsection