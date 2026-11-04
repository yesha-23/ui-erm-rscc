@extends('layouts.rekam-medis')
@section('title', 'Asesmen awal gawat darurat')

@section('content')
{{-- ===== Judul ===== --}}
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <div>
        <p class="rs-eyebrow mb-1">
            <i class="bi bi-clipboard2-pulse me-1" aria-hidden="true"></i>Form rekam medis · {{ $pasien['unit'] }}
        </p>
        <h2 class="h4 fw-bold mb-0">Asesmen awal keperawatan gawat darurat</h2>
        <p class="small rs-text-muted mb-0 mt-1">Diisi oleh perawat · belum pernah disimpan</p>
    </div>
</div>

{{-- ===== Penanda langkah (dibuat bisa diklik di tahap berikutnya) ===== --}}
<nav class="rs-doc-tabs mb-3" aria-label="Langkah asesmen">
    @foreach ($langkah as $i => $nama)
        <span @class(['rs-doc-tab', 'active' => $i === 0])>{{ $i + 1 }}. {{ $nama }}</span>
    @endforeach
</nav>

{{-- ===== Sementara ===== --}}
<section class="rs-card rs-empty">
    <span class="rs-empty-icon"><i class="bi bi-tools" aria-hidden="true"></i></span>
    <h3 class="h6 fw-bold mb-1">Halaman sedang dibangun</h3>
    <p class="rs-text-muted mb-0">Komponen form klinis (tanda vital, GCS, skala nyeri, pemeriksaan fisik) ditambahkan di tahap berikutnya.</p>
</section>
@endsection