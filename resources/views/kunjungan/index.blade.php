@extends('layouts.app')
@section('title', 'Kunjungan ' . $judul)

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Kunjungan {{ $judul }}</h1>
            <p class="rs-text-muted mb-0">{{ $rows->total() }} kunjungan · {{ $rentang }}</p>
        </div>
        <button type="button" class="btn btn-outline-secondary">
            <i class="bi bi-download me-1" aria-hidden="true"></i>Ekspor
        </button>
    </div>

    <x-filter-kunjungan :chips="$chips" :pilihan="$pilihan" :dari="$dari" :sampai="$sampai" />
    <x-tab-status :tabs="$tabs" :aktif="$tabAktif" />
    <x-tabel-kunjungan :kolom="$kolom" :cek="$cek" :rows="$rows" />

    <p class="small rs-text-muted mt-3 mb-0">Nama dan nomor pada tabel adalah data contoh.</p>
@endsection