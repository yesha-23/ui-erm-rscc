@extends('layouts.rekam-medis')
@section('title', 'Ringkasan')

@section('content')
    <div class="rs-card">
        <h2 class="h5 fw-bold mb-1">Ringkasan pasien</h2>
        <p class="rs-text-muted mb-3">Halaman ini dikerjakan oleh Anggota B (acuan: mockup 05a).</p>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('rekam-medis.dokumen', [$pasien['no_reg'], 'surat']) }}" class="btn btn-primary">
                <i class="bi bi-file-earmark-text me-1" aria-hidden="true"></i>Buat surat
            </a>
            <a href="{{ route('rekam-medis.dokumen', [$pasien['no_reg'], 'persetujuan']) }}" class="btn btn-outline-secondary">
                <i class="bi bi-pen me-1" aria-hidden="true"></i>Buat persetujuan
            </a>
        </div>
    </div>
@endsection