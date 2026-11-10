@extends('layouts.rekam-medis')
@section('title', 'E-Resep')

@section('content')
@php
    // Nomor baris berikutnya untuk JS (melanjutkan nomor baris yang sudah ada)
    $indeksAwal = $baris ? max(array_map('intval', array_keys($baris))) + 1 : 0;
    $keterangan = match ($status) {
        'terkirim' => 'Terkirim ke farmasi · ' . $waktu,
        'draf'     => 'Draf · disimpan ' . $waktu,
        default    => 'Belum disimpan',
    };
@endphp

{{-- ===== Judul ===== --}}
<x-judul-form ikon="bi-capsule" kelompok="Farmasi" judul="E-Resep" :sub="$pasien['dpjp_nama'] . ' · ' . $keterangan">
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalRiwayatObat">
        <i class="bi bi-clock-history me-1" aria-hidden="true"></i>Riwayat pemberian obat
    </button>
</x-judul-form>

@if ($errors->any())
    <div class="alert alert-danger d-flex gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        <ul class="mb-0 ps-3">
            @foreach (collect($errors->all())->unique() as $pesan)
                <li>{{ $pesan }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ===== Tambah obat ===== --}}
<section class="rs-card mb-3">
    <h3 class="h6 fw-bold mb-2">Tambah obat / alkes</h3>
    <div class="rs-cari-obat" data-rs-cari-obat-wadah>
        <x-search label="Cari obat atau alkes" placeholder="Ketik nama obat, mis. parasetamol"
                  autocomplete="off" data-rs-cari-obat />
        <ul class="rs-obat-hasil list-unstyled" data-rs-hasil-obat hidden></ul>
    </div>
    <p class="small rs-text-muted mt-2 mb-0">Tekan ↑ ↓ untuk memilih, lalu Enter untuk menambahkan.</p>

    @if ($sebelumnya)
        <div class="rs-salin mt-3">
            <span>
                <i class="bi bi-arrow-repeat me-1" aria-hidden="true"></i>Resep sebelumnya:
                <strong>{{ $sebelumnya['tanggal'] }}</strong> · {{ $sebelumnya['unit'] }} · {{ count($sebelumnya['items']) }} item
            </span>
            <button type="button" class="btn btn-outline-secondary btn-sm" data-rs-salin-resep>Salin ke resep ini</button>
        </div>
        <p class="small text-success mt-2 mb-0" data-rs-salin-info aria-live="polite"></p>
    @endif
</section>

{{-- ===== Daftar resep ===== --}}
<form id="form-resep" method="POST" novalidate data-rs-resep data-rs-indeks-awal="{{ $indeksAwal }}"
      action="{{ route('rekam-medis.resep.simpan', $pasien['no_reg']) }}" class="d-flex flex-column gap-3">
    @csrf

    <section class="rs-card p-0 overflow-hidden">
        <div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-2">
            <h3 class="h6 fw-bold mb-0">Daftar resep</h3>
            <span class="rs-pill-count" aria-live="polite"><span data-rs-jumlah-item>{{ count($baris) }}</span> item</span>
        </div>

        <p class="rs-empty rs-text-muted mb-0" data-rs-resep-kosong @if ($baris) hidden @endif>
            Belum ada obat. Cari obat di atas untuk menambahkan.
        </p>

        <div class="table-responsive" data-rs-tabel-resep @unless ($baris) hidden @endunless>
            <table class="table rs-table rs-table-kartu rs-table-ringkas rs-table-resep mb-0">
                <thead>
                    <tr>
                        <th scope="col">Obat / alkes</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Aturan pakai</th>
                        <th scope="col">Rute</th>
                        <th scope="col">Catatan</th>
                        <th scope="col"><span class="visually-hidden">Aksi</span></th>
                    </tr>
                </thead>
                <tbody data-rs-daftar-resep>
                    @foreach ($baris as $i => $b)
                        @include('rekam-medis.resep._baris', ['i' => $i, 'baris' => $b])
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="rs-card">
        <label for="catatan_resep" class="form-label">
            Catatan untuk apoteker <span class="rs-text-muted fw-normal">(opsional)</span>
        </label>
        <textarea id="catatan_resep" name="catatan_resep" rows="2" class="form-control"
                  placeholder="mis. pasien sulit menelan tablet">{{ $catatan }}</textarea>
    </section>

    <datalist id="daftar-signa">
        <option value="1 x 1"></option>
        <option value="2 x 1"></option>
        <option value="3 x 1"></option>
        <option value="3 x 1 sdm"></option>
        <option value="1 x 1 malam"></option>
        <option value="Bila perlu"></option>
    </datalist>
</form>

{{-- Cetakan baris baru + data untuk JavaScript (di LUAR form) --}}
<template id="tpl-baris-resep">
    @include('rekam-medis.resep._baris', ['i' => '__I__', 'baris' => []])
</template>
<script type="application/json" id="data-obat">@json($katalog)</script>
<script type="application/json" id="data-resep-sebelumnya">@json($sebelumnya['items'] ?? [])</script>

{{-- ===== Bar aksi ===== --}}
<div class="rs-action-bar">
    <span class="small rs-text-muted">
        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>Draf boleh belum lengkap. Kirim memerlukan jumlah dan aturan pakai di setiap baris.
    </span>
    <div class="rs-action-bar-buttons">
        <button type="submit" form="form-resep" name="aksi" value="draf" class="btn btn-outline-secondary">
            <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan draf
        </button>
        <button type="submit" form="form-resep" name="aksi" value="kirim" class="btn btn-primary">
            <i class="bi bi-send me-1" aria-hidden="true"></i>Kirim ke farmasi
        </button>
    </div>
</div>
@endsection

{{-- ===== Popup riwayat pemberian obat ===== --}}
@push('modal')
<div class="modal fade" id="modalRiwayatObat" tabindex="-1" aria-labelledby="judulRiwayatObat" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title h5 fw-bold" id="judulRiwayatObat">Riwayat pemberian obat</h2>
                    <p class="small rs-text-muted mb-0">{{ $pasien['nama'] }} · RM {{ $pasien['no_rm'] }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table rs-table rs-table-kartu rs-table-ringkas rs-table-tanpa-aksi mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Obat</th>
                                <th scope="col">Waktu</th>
                                <th scope="col">Dosis</th>
                                <th scope="col">Rute</th>
                                <th scope="col">Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($riwayat as $r)
                                <tr>
                                    <td class="rs-td-utama"><span class="fw-semibold">{{ $r['obat'] }}</span></td>
                                    <td data-label="Waktu"><span>{{ $r['waktu'] }}</span></td>
                                    <td data-label="Dosis"><span>{{ $r['dosis'] }}</span></td>
                                    <td data-label="Rute"><span>{{ $r['rute'] }}</span></td>
                                    <td data-label="Petugas"><span>{{ $r['oleh'] }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endpush