@extends('layouts.guest')
@section('title', 'Masuk')

@section('content')
<div class="container-fluid">
    <div class="row min-vh-100">

        <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-between p-5 rs-login-hero">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/logo-rscc.png') }}" alt="Logo RS Condong Catur" width="48" height="40" class="rs-logo">
                <div>
                    <div class="fw-bold">RS Condong Catur</div>
                    <div class="small text-secondary">Electronic Medical Record</div>
                </div>
            </div>
            <div>
                <h1 class="display-5 fw-bold mb-3">Kita Bisa<br>Luar Biasa.</h1>
                <p class="text-secondary fs-5">Satu tempat untuk rekam medis IGD, Hemodialisa, Rehabilitasi Medik, Rawat Jalan, Rawat Inap dan Kamar Operasi.</p>
            </div>
            <span class="small text-secondary">© {{ date('Y') }} Rumah Sakit Condong Catur · IT RSCC</span>
        </div>

        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 bg-white">
            <form method="POST" action="{{ route('login.proses') }}" class="w-100 rs-login-form">
                @csrf
                <h2 class="h3 fw-bold mb-1">Masuk ke akun Anda</h2>
                <p class="text-secondary mb-4">Gunakan nomor HP yang terdaftar.</p>

                <div class="mb-3">
                    <label for="no_hp" class="form-label">Nomor HP</label>
                    <input id="no_hp" name="no_hp" type="tel"
                           class="form-control @error('no_hp') is-invalid @enderror"
                           value="{{ old('no_hp') }}" placeholder="08xx xxxx xxxx">
                    @error('no_hp') <div class="invalid-feedback">Nomor HP wajib diisi.</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" name="password" type="password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password') <div class="invalid-feedback">Password wajib diisi.</div> @enderror
                </div>

                <div class="mb-4">
                    <label for="captcha" class="form-label">Kode verifikasi</label>
                    <div class="d-flex gap-2">
                        <div class="rs-captcha d-flex align-items-center justify-content-center">[KODE]</div>
                        <input id="captcha" name="captcha" type="text"
                               class="form-control @error('captcha') is-invalid @enderror" placeholder="Ketik kode">
                    </div>
                    @error('captcha') <div class="text-danger small mt-1">Kode verifikasi wajib diisi.</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">Masuk</button>
            </form>
        </div>

    </div>
</div>
@endsection