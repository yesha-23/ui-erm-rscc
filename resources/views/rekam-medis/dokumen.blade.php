@extends('layouts.rekam-medis')
@section('title', $dokumen['nama'] ?? 'Pilih dokumen')

@section('content')
@php
    $terbuka   = ! $dokumen;
    $namaJenis = $jenis === 'surat' ? 'surat' : 'dokumen persetujuan';
    $populer   = collect($daftar)->where('populer', true);
@endphp

{{-- ===== Judul halaman ===== --}}
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <div>
        <p class="rs-eyebrow mb-1">
            <i class="bi bi-folder2-open me-1" aria-hidden="true"></i>
            Dokumen · {{ $jenis === 'surat' ? 'Pembuatan surat' : 'Persetujuan' }}
        </p>
        <h2 class="h4 fw-bold mb-0">{{ $dokumen['nama'] ?? 'Pilih dokumen' }}</h2>
    </div>
    @if ($dokumen)
        <x-badge jenis="peringatan">Draf · belum ditandatangani</x-badge>
    @endif
</div>

{{-- ===== Pemilih dokumen ===== --}}
<section class="rs-card rs-doc-picker mb-3" aria-label="Pilih dokumen">
    <div class="d-flex flex-wrap align-items-center gap-2">
        <nav class="rs-doc-tabs" aria-label="Jenis dokumen">
            @foreach (['surat' => 'Surat', 'persetujuan' => 'Persetujuan'] as $k => $label)
                <a href="{{ route('rekam-medis.dokumen', [$pasien['no_reg'], $k]) }}"
                   @class(['rs-doc-tab', 'active' => $jenis === $k])
                   @if ($jenis === $k) aria-current="page" @endif>
                    {{ $label }} <span class="rs-tab-count">{{ $jumlah[$k] }}</span>
                </a>
            @endforeach
        </nav>

        @if ($dokumen)
            <span class="small rs-text-muted d-none d-md-inline">
                Dokumen aktif: <strong class="text-body">{{ $dokumen['nama'] }}</strong>
            </span>
        @endif

        <button type="button" @class(['btn btn-outline-secondary btn-sm ms-auto', 'collapsed' => ! $terbuka])
                data-bs-toggle="collapse" data-bs-target="#pilihDokumen"
                aria-expanded="{{ $terbuka ? 'true' : 'false' }}" aria-controls="pilihDokumen">
            <i class="bi bi-arrow-left-right me-1" aria-hidden="true"></i>{{ $dokumen ? 'Ganti dokumen' : 'Daftar dokumen' }}
        </button>
    </div>

    <div id="pilihDokumen" @class(['collapse', 'show' => $terbuka]) data-rs-focus-on-show>
        <div class="pt-3">
            <x-search label="Cari dokumen" placeholder="Cari dari {{ count($daftar) }} {{ $namaJenis }}…"
                      data-rs-filter-list="#daftar-dokumen" data-rs-filter-empty="#dokumen-kosong" />

            @if ($populer->isNotEmpty())
                <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                    <span class="small rs-text-muted">Sering dipakai:</span>
                    @foreach ($populer as $p)
                        <a href="{{ route('rekam-medis.dokumen', [$pasien['no_reg'], $jenis, $p['slug']]) }}" class="rs-chip">{{ $p['nama'] }}</a>
                    @endforeach
                </div>
            @endif

            <ul id="daftar-dokumen" class="rs-doc-grid list-unstyled mt-3 mb-0">
                @foreach ($daftar as $d)
                    @php $aktif = $dokumen && $d['slug'] === $dokumen['slug']; @endphp
                    <li data-rs-filter-text="{{ mb_strtolower($d['nama']) }}">
                        <a href="{{ route('rekam-medis.dokumen', [$pasien['no_reg'], $jenis, $d['slug']]) }}"
                           @class(['rs-doc-item', 'active' => $aktif]) @if ($aktif) aria-current="page" @endif>
                            <i class="bi bi-file-earmark-text" aria-hidden="true"></i>{{ $d['nama'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <p id="dokumen-kosong" class="small rs-text-muted text-center py-3 mb-0" hidden>Dokumen tidak ditemukan.</p>
        </div>
    </div>
</section>

@if (! $dokumen)
    {{-- ===== Belum memilih dokumen ===== --}}
    <div class="rs-card rs-empty">
        <span class="rs-empty-icon"><i class="bi bi-file-earmark-plus" aria-hidden="true"></i></span>
        <h3 class="h6 fw-bold mb-1">Pilih {{ $namaJenis }} yang akan dibuat</h3>
        <p class="rs-text-muted mb-0">Ketik di kotak cari, klik salah satu yang sering dipakai, atau pilih dari daftar di atas.</p>
    </div>
@else
    {{-- ===== Isi dokumen ===== --}}
    <form id="form-dokumen" method="POST" novalidate
          action="{{ route('rekam-medis.dokumen.simpan', [$pasien['no_reg'], $jenis, $dokumen['slug']]) }}"
          class="d-flex flex-column gap-3">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger d-flex gap-2 mb-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                <div>Masih ada isian yang perlu dilengkapi sebelum ditandatangani.</div>
            </div>
        @endif

        <section class="rs-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h6 fw-bold mb-0">Data pasien</h3>
                <span class="small rs-text-muted">Terisi otomatis dari pendaftaran</span>
            </div>
            <dl class="row g-3 mb-0 rs-dl">
                @foreach ([
                    'Nama'               => $pasien['nama'],
                    'No. RM'             => $pasien['no_rm'],
                    'Tanggal lahir/usia' => $pasien['tgl_lahir'] . ' · ' . $pasien['umur'],
                    'Jenis kelamin'      => $pasien['jk'],
                    'Penjamin'           => $pasien['penjamin'] === 'JKN' ? 'BPJS Kesehatan' : 'Umum',
                    'Alamat'             => $pasien['alamat'],
                ] as $label => $nilai)
                    <div class="col-sm-6 col-lg-4">
                        <dt>{{ $label }}</dt>
                        <dd>{{ $nilai }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        @include('rekam-medis.dokumen.' . $dokumen['form'])

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3">Tanda tangan</h3>
            <div class="row g-3">
                @php
                    $penandatangan = $jenis === 'surat'
                        ? [['Dokter pemeriksa', $pasien['dpjp_nama'], 'Ditandatangani otomatis saat disimpan']]
                        : [['Yang menyatakan', $pasien['nama'], 'Tanda tangan di layar / tablet'],
                           ['Petugas yang menjelaskan', '[Nama Petugas]', 'Ditandatangani otomatis saat disimpan']];
                @endphp
                @foreach ($penandatangan as [$peran, $nama, $petunjuk])
                    <div class="col-md-6">
                        <p class="form-label mb-2">{{ $peran }}</p>
                        {{-- TODO (IT RSCC): kanvas tanda tangan / tanda tangan elektronik --}}
                        <div class="rs-signature">{{ $petunjuk }}</div>
                        <p class="small rs-text-muted mt-2 mb-0">{{ $nama }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    </form>

    {{-- ===== Bar aksi: selebar area konten, menempel di bawah ===== --}}
    <div class="rs-action-bar">
        <span class="small rs-text-muted d-none d-md-inline">
            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>Draf boleh belum lengkap. Tanda tangan memerlukan semua isian wajib.
        </span>
        <div class="rs-action-bar-buttons">
            <button type="submit" form="form-dokumen" name="aksi" value="draf" class="btn btn-outline-secondary">
                <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan draf
            </button>
            <a href="#" class="btn btn-outline-secondary">
                <i class="bi bi-eye me-1" aria-hidden="true"></i>Pratinjau cetak
            </a>
            <button type="submit" form="form-dokumen" name="aksi" value="final" class="btn btn-primary">
                <i class="bi bi-pen me-1" aria-hidden="true"></i>Simpan &amp; tanda tangani
            </button>
        </div>
    </div>
@endif
@endsection