@extends('layouts.rekam-medis')
@section('title', 'Asesmen awal gawat darurat')

@section('content')
@php
    // Langkah yang dibuka pertama: dari error validasi → dari langkah terakhir disimpan → langkah 1
    $petaLangkah = [
        0 => ['cara_datang', 'triase', 'keluhan'],
        1 => ['keadaan_umum', 'gcs_e', 'gcs_v', 'gcs_m', 'td_sistolik', 'td_diastolik',
              'nadi', 'napas', 'suhu', 'spo2', 'nyeri'],
        2 => ['diagnosa'],
    ];
    $langkahAwal = (int) old('langkah', session('langkah', 0));
    foreach ($petaLangkah as $i => $kunci) {
        if ($errors->hasAny($kunci)) { $langkahAwal = $i; break; }
    }

    $keterangan = match ($status) {
        'final' => 'Final · disimpan ' . $waktu,
        'draf'  => 'Draf · terakhir disimpan ' . $waktu,
        default => 'Belum pernah disimpan',
    };
@endphp

{{-- ===== Judul ===== --}}
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <div>
        <p class="rs-eyebrow mb-1">
            <i class="bi bi-clipboard2-pulse me-1" aria-hidden="true"></i>Form rekam medis · {{ $pasien['unit'] }}
        </p>
        <h2 class="h4 fw-bold mb-0">Asesmen awal keperawatan gawat darurat</h2>
        <p class="small rs-text-muted mb-0 mt-1">Diisi oleh perawat · {{ $keterangan }}</p>
    </div>
    @if ($status === 'final')
        <x-badge jenis="sukses">Final</x-badge>
    @elseif ($status === 'draf')
        <x-badge jenis="peringatan">Draf</x-badge>
    @endif
</div>

{{-- ===== Navigasi langkah ===== --}}
<div class="rs-langkah-nav" role="tablist" aria-label="Langkah asesmen">
    @foreach ($langkah as $i => $nama)
        <button type="button" role="tab" class="rs-langkah-tab"
                id="tab-langkah-{{ $i }}" aria-controls="langkah-{{ $i }}" data-rs-langkah-tab>
            <span class="rs-langkah-nomor" aria-hidden="true">{{ $i + 1 }}</span>{{ $nama }}
        </button>
    @endforeach
</div>

@if ($errors->any())
    <div class="alert alert-danger d-flex gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        <div>
            <strong>Belum bisa disimpan final.</strong> Lengkapi isian berikut:
            <ul class="mb-0 ps-3 mt-1">
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form id="form-asesmen" method="POST" novalidate data-rs-asesmen data-rs-langkah-awal="{{ $langkahAwal }}"
      action="{{ route('rekam-medis.asesmen.simpan', $pasien['no_reg']) }}">
    @csrf
    <input type="hidden" name="langkah" value="{{ $langkahAwal }}" data-rs-langkah-input>

    {{-- ================= LANGKAH 1: KEDATANGAN ================= --}}
    <div id="langkah-0" role="tabpanel" aria-labelledby="tab-langkah-0" data-rs-langkah-panel class="d-flex flex-column gap-3">
        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3 rs-langkah-judul" tabindex="-1">Kedatangan & triase</h3>
            <x-form.pilihan name="cara_datang" label="Cara datang" wajib class="mb-3"
                            :opsi="$opsi['cara_datang']" :nilai="old('cara_datang', $isi['cara_datang'] ?? null)" />
            <x-form.pilihan name="triase" label="Triase" wajib
                            :opsi="$opsi['triase']" :nilai="old('triase', $isi['triase'] ?? null)"
                            :warna="['merah' => 'merah', 'kuning' => 'kuning', 'hijau' => 'hijau', 'hitam' => 'hitam']" />
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3">Anamnesis</h3>
            <div class="row g-3">
                <div class="col-12">
                    <label for="keluhan" class="form-label">Keluhan utama <span class="text-danger" aria-hidden="true">*</span></label>
                    <textarea id="keluhan" name="keluhan" rows="2" placeholder="mis. demam 6 hari, mual, nyeri ulu hati"
                              @class(['form-control', 'is-invalid' => $errors->has('keluhan')])>{{ old('keluhan', $isi['keluhan'] ?? '') }}</textarea>
                    @error('keluhan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="riwayat_sekarang" class="form-label">Riwayat penyakit sekarang</label>
                    <textarea id="riwayat_sekarang" name="riwayat_sekarang" rows="3" class="form-control">{{ old('riwayat_sekarang', $isi['riwayat_sekarang'] ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="riwayat_dahulu" class="form-label">Riwayat penyakit dahulu</label>
                    <textarea id="riwayat_dahulu" name="riwayat_dahulu" rows="3" class="form-control"
                              placeholder="mis. hipertensi, DM, asma">{{ old('riwayat_dahulu', $isi['riwayat_dahulu'] ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="alergi" class="form-label">Alergi</label>
                    <input type="text" id="alergi" name="alergi" class="form-control"
                           value="{{ old('alergi', $isi['alergi'] ?? '') }}" placeholder="Tulis 'Tidak ada' bila tidak ada">
                </div>
            </div>
        </section>
    </div>

    {{-- ================= LANGKAH 2: OBJEKTIF ================= --}}
    <div id="langkah-1" role="tabpanel" aria-labelledby="tab-langkah-1" data-rs-langkah-panel class="d-flex flex-column gap-3" hidden>
        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3 rs-langkah-judul" tabindex="-1">Keadaan umum & kesadaran</h3>
            <x-form.pilihan name="keadaan_umum" label="Keadaan umum" wajib class="mb-3"
                            :opsi="$opsi['keadaan_umum']" :nilai="old('keadaan_umum', $isi['keadaan_umum'] ?? null)" />
            <x-form.gcs :opsi="$opsi" :isi="$isi" />
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3">Tanda-tanda vital</h3>
            <x-form.ttv :isi="$isi" />
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3">Nyeri</h3>
            <x-form.skala-nyeri :opsi="$opsi" :isi="$isi" />
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3">Pemeriksaan fisik</h3>
            <x-form.fisik :opsi="$opsi" :isi="$isi" />
        </section>

        <section class="rs-card" data-rs-jatuh>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                <h3 class="h6 fw-bold mb-0">Pengkajian risiko jatuh</h3>
                <span class="rs-jatuh-hasil" data-rs-jatuh-hasil aria-live="polite"></span>
            </div>
            <p class="small rs-text-muted mb-3">Metode Get Up and Go · diisi perawat.</p>
            @foreach ($opsi['risiko_jatuh'] as $kunci => $pertanyaan)
                <x-form.pilihan name="jatuh[{{ $kunci }}]" :label="$pertanyaan" class="mb-3"
                                :opsi="['ya' => 'Ya', 'tidak' => 'Tidak']"
                                :nilai="old('jatuh.' . $kunci, $isi['jatuh'][$kunci] ?? 'tidak')" />
            @endforeach
        </section>
    </div>

    {{-- ================= LANGKAH 3: ASESMEN ================= --}}
    <div id="langkah-2" role="tabpanel" aria-labelledby="tab-langkah-2" data-rs-langkah-panel class="d-flex flex-column gap-3" hidden>
        <section class="rs-card" data-rs-diagnosa>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                <h3 class="h6 fw-bold mb-0 rs-langkah-judul" tabindex="-1">
                    Diagnosis keperawatan <span class="text-danger" aria-hidden="true">*</span>
                </h3>
                <span class="rs-pill-count" aria-live="polite"><span data-rs-diagnosa-jumlah>0</span> dipilih</span>
            </div>
            <p class="small rs-text-muted mb-3">Pilih satu atau lebih sesuai hasil pengkajian.</p>
            <x-form.pilihan name="diagnosa" tipe="checkbox"
                            :opsi="$opsi['diagnosa']" :nilai="old('diagnosa', $isi['diagnosa'] ?? [])" />
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-2">Diagnosis kerja dokter</h3>
            <p class="mb-0">{{ $pasien['diagnosa_kerja'] }}</p>
            <p class="small rs-text-muted mb-0 mt-1">Hanya ditampilkan sebagai acuan; diisi oleh dokter.</p>
        </section>
    </div>

    {{-- ================= LANGKAH 4: RENCANA ================= --}}
    <div id="langkah-3" role="tabpanel" aria-labelledby="tab-langkah-3" data-rs-langkah-panel class="d-flex flex-column gap-3" hidden>
        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3 rs-langkah-judul" tabindex="-1">Intervensi keperawatan</h3>
            @foreach ($opsi['intervensi'] as $kelompok => $daftar)
                <x-form.pilihan name="intervensi" tipe="checkbox" :label="$kelompok" class="mb-3"
                                :opsi="$daftar" :nilai="old('intervensi', $isi['intervensi'] ?? [])" />
            @endforeach
            <label for="edukasi" class="form-label">Edukasi lainnya</label>
            <textarea id="edukasi" name="edukasi" rows="2" class="form-control"
                      placeholder="mis. edukasi minum cukup, tanda bahaya demam">{{ old('edukasi', $isi['edukasi'] ?? '') }}</textarea>
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3">Tanda tangan</h3>
            <div class="row">
                <div class="col-md-6">
                    <p class="form-label mb-2">Perawat yang mengkaji</p>
                    {{-- TODO (IT RSCC): tanda tangan elektronik --}}
                    <div class="rs-signature">Ditandatangani otomatis saat disimpan final</div>
                    <p class="small rs-text-muted mt-2 mb-0">[Nama Perawat], A.Md.Kep</p>
                </div>
            </div>
        </section>
    </div>
</form>

{{-- ===== Bar aksi (di luar form; tombol memakai form="form-asesmen") ===== --}}
<div class="rs-action-bar">
    <span class="small rs-text-muted" data-rs-status-simpan>
        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>{{ $status ? ucfirst($keterangan) : 'Belum disimpan' }}
    </span>
    <div class="rs-action-bar-buttons">
        <button type="button" class="btn btn-outline-secondary" data-rs-langkah-prev>
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Sebelumnya
        </button>
        <button type="submit" form="form-asesmen" name="aksi" value="draf" class="btn btn-outline-secondary">
            <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan draf
        </button>
        <button type="button" class="btn btn-primary" data-rs-langkah-next>
            Lanjut<i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
        </button>
        <button type="submit" form="form-asesmen" name="aksi" value="final" class="btn btn-success" data-rs-langkah-final hidden>
            <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Simpan final
        </button>
    </div>
</div>
@endsection