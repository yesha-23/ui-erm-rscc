@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')
<div class="rs-login">

    {{-- Kolom kiri: foto gedung RS (hanya layar besar) --}}
    <section class="rs-login-hero d-none d-lg-flex">
        <picture class="rs-login-photo">
            <source srcset="{{ asset('images/gedung-rscc.webp') }}" type="image/webp">
            <img src="{{ asset('images/gedung-rscc.jpg') }}" alt="Gedung Rumah Sakit Condong Catur">
        </picture>

        <div class="rs-login-brand">
            <img src="{{ asset('images/logo-rscc.png') }}" alt="" width="36" height="30" class="rs-logo">
            <span>
                <strong>RS Condong Catur</strong>
                <small>Electronic Medical Record</small>
            </span>
        </div>

        <div class="rs-login-caption">
            <h1 class="rs-login-title">Kita Bisa<br>Luar Biasa.</h1>
            <p class="rs-login-lead">
                Satu tempat untuk rekam medis IGD, Hemodialisa, Rehabilitasi Medik,
                Rawat Jalan, Rawat Inap dan Kamar Operasi.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <span class="rs-chip-glass">6 unit layanan</span>
                <span class="rs-chip-glass">Terintegrasi BPJS / SEP</span>
            </div>
            <span class="rs-login-copy">© {{ date('Y') }} Rumah Sakit Condong Catur · IT RSCC</span>
        </div>
    </section>

    {{-- Kolom kanan: form --}}
    <section class="rs-login-panel">
        <form method="POST" action="{{ route('login.proses') }}" class="rs-login-form w-100" data-rs-loading novalidate>
            @csrf

            {{-- Logo untuk layar kecil --}}
            <div class="d-flex d-lg-none align-items-center gap-2 mb-4">
                <img src="{{ asset('images/logo-rscc.png') }}" alt="" width="44" height="37" class="rs-logo">
                <div>
                    <div class="fw-bold">RS Condong Catur</div>
                    <div class="small rs-text-muted">Electronic Medical Record</div>
                </div>
            </div>

            <h2 class="h3 fw-bold mb-1">Masuk ke akun Anda</h2>
            <p class="rs-text-muted mb-4">Gunakan nomor HP yang terdaftar di SIMRS.</p>

            @if (session('error'))
                <div class="alert alert-danger d-flex gap-2 align-items-start" role="alert">
                    <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            {{-- Nomor HP --}}
            <div class="mb-3">
                <label for="no_hp" class="form-label">Nomor HP</label>
                <div class="input-group has-validation">
                    <span class="input-group-text"><i class="bi bi-phone" aria-hidden="true"></i></span>
                    <input id="no_hp" name="no_hp" type="tel" inputmode="numeric" autocomplete="username"
                           class="form-control @error('no_hp') is-invalid @enderror"
                           value="{{ old('no_hp') }}" placeholder="08xx xxxx xxxx">
                    @error('no_hp') <div class="invalid-feedback">Nomor HP wajib diisi.</div> @enderror
                </div>
            </div>

            {{-- Password --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-baseline">
                    <label for="password" class="form-label">Password</label>
                    <a href="#" class="small fw-semibold text-decoration-none">Lupa password?</a>
                </div>
                <div class="input-group has-validation">
                    <span class="input-group-text"><i class="bi bi-lock" aria-hidden="true"></i></span>
                    <input id="password" name="password" type="password" autocomplete="current-password"
                           class="form-control @error('password') is-invalid @enderror">
                    <button type="button" class="btn btn-outline-secondary" data-rs-toggle-password="password"
                            aria-label="Tampilkan password" aria-pressed="false">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                    </button>
                    @error('password') <div class="invalid-feedback">Password wajib diisi.</div> @enderror
                </div>
            </div>

            {{-- Captcha --}}
            <div class="mb-4">
                <label for="captcha" class="form-label">Kode verifikasi</label>
                <div class="d-flex gap-2">
                    {{-- TODO (IT RSCC): ganti [KODE] dengan gambar captcha dari server --}}
                    <div class="rs-captcha d-flex align-items-center justify-content-center">[KODE]</div>
                    <button type="button" class="btn btn-outline-secondary" aria-label="Muat ulang kode">
                        <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
                    </button>
                    <input id="captcha" name="captcha" type="text" autocomplete="off"
                           class="form-control @error('captcha') is-invalid @enderror" placeholder="Ketik kode">
                </div>
                @error('captcha') <div class="text-danger small mt-1">Kode verifikasi wajib diisi.</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 rs-btn-lg">
                Masuk <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
            </button>

            <p class="small rs-text-muted text-center mt-4 mb-0">Kendala login? Hubungi IT RSCC di ext. [NOMOR EXT]</p>
        </form>
    </section>
</div>
@endsection