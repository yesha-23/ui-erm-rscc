@props(['pasien', 'crumb' => null])

@php
    $inisial = collect(explode(' ', $pasien['nama']))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
    $jkn = $pasien['penjamin'] === 'JKN';
@endphp

<header class="rs-pasien-header">
    <nav aria-label="Breadcrumb" class="rs-crumb">
        <a href="{{ route('beranda') }}">Beranda</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route($pasien['unit_route']) }}">{{ $pasien['unit'] }}</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('rekam-medis.show', $pasien['no_reg']) }}">Rekam Medis</a>
        @if ($crumb)
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ $crumb }}</span>
        @endif
    </nav>

    <div class="d-flex flex-wrap align-items-center gap-3">
        <span class="rs-avatar rs-avatar-pasien rs-avatar-lg" aria-hidden="true">{{ $inisial }}</span>

        <div class="flex-grow-1">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h1 class="h5 fw-bold mb-0">{{ $pasien['nama'] }}</h1>
                <x-badge :jenis="$jkn ? 'jkn' : 'nonjkn'">{{ $jkn ? 'BPJS Kesehatan' : 'Umum' }}</x-badge>
                <x-badge jenis="gagal"><i class="bi bi-exclamation-triangle-fill me-1" aria-hidden="true"></i>Alergi: {{ $pasien['alergi'] }}</x-badge>
            </div>
            <p class="small rs-text-muted mb-0 mt-1">
                RM {{ $pasien['no_rm'] }} · {{ $pasien['jk'] }}, {{ $pasien['umur'] }} · Reg {{ $pasien['no_reg'] }}
                · Datang {{ $pasien['datang'] }} · DPJP {{ $pasien['dpjp_nama'] }}
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary d-lg-none" data-bs-toggle="collapse"
                    data-bs-target="#menuRekamMedis" aria-expanded="false" aria-controls="menuRekamMedis">
                <i class="bi bi-list-ul me-1" aria-hidden="true"></i>Menu
            </button>
            <a href="#" class="btn btn-outline-secondary"><i class="bi bi-clock-history me-1" aria-hidden="true"></i>Riwayat</a>
            <a href="#" class="btn btn-outline-secondary"><i class="bi bi-printer me-1" aria-hidden="true"></i>Cetak</a>
            <button type="button" class="btn btn-success"><i class="bi bi-check2 me-1" aria-hidden="true"></i>Submit Final</button>
        </div>
    </div>
</header>