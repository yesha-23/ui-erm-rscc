@props(['pasien', 'crumb' => null])

@php
    $inisial = collect(explode(' ', $pasien['nama']))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
    $jkn   = $pasien['penjamin'] === 'JKN';
    $sub   = $pasien['unit_sub'] ?? null;
    $final = session("final.{$pasien['no_reg']}");

    // [kelas badge, ikon, teks]
    $alergi = match ($pasien['alergi_status'] ?? null) {
        'ada'   => ['rs-badge-nonjkn', 'bi-exclamation-triangle-fill', 'Alergi: ' . $pasien['alergi']],
        'tidak' => ['rs-badge-jkn', 'bi-check-circle-fill', 'Tidak ada alergi'],
        default => ['rs-badge-belum', 'bi-question-circle-fill', 'Alergi belum dikaji'],
    };
@endphp

<header class="rs-pasien-header">
    <nav aria-label="Breadcrumb" class="rs-crumb">
        <a href="{{ route('beranda') }}">Beranda</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route($pasien['unit_route'], $sub ? [$sub] : []) }}">{{ $pasien['unit'] }}</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('rekam-medis.show', $pasien['no_reg']) }}">Rekam Medis</a>
        @if ($crumb)
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ $crumb }}</span>
        @endif
    </nav>

    <div class="d-flex flex-wrap align-items-center gap-3">
        <span class="rs-avatar rs-avatar-pasien rs-avatar-lg" aria-hidden="true">{{ $inisial }}</span>

        <div class="flex-grow-1" style="min-width: 0">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <h1 class="h5 fw-bold mb-0">{{ $pasien['nama'] }}</h1>
                <x-badge :jenis="$jkn ? 'jkn' : 'nonjkn'">{{ $jkn ? 'BPJS Kesehatan' : 'Umum' }}</x-badge>
                <button type="button" class="rs-badge rs-badge-tombol {{ $alergi[0] }}"
                        data-bs-toggle="modal" data-bs-target="#modalAlergi"
                        title="{{ $alergi[2] }} — klik untuk mengubah">
                    <i class="bi {{ $alergi[1] }}" aria-hidden="true"></i>
                    <span class="rs-badge-teks">{{ $alergi[2] }}</span>
                    <i class="bi bi-pencil" aria-hidden="true"></i>
                    <span class="visually-hidden">(ubah riwayat alergi)</span>
                </button>
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
            <a href="{{ route('rekam-medis.riwayat', $pasien['no_reg']) }}" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-1" aria-hidden="true"></i>Riwayat
            </a>
            <a href="{{ route('rekam-medis.cetak', $pasien['no_reg']) }}" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1" aria-hidden="true"></i>Cetak
            </a>
            @if ($final)
                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalFinal">
                    <i class="bi bi-lock-fill me-1" aria-hidden="true"></i>Final · {{ $final['waktu'] }}
                </button>
            @else
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalFinal">
                    <i class="bi bi-check2 me-1" aria-hidden="true"></i>Submit final
                </button>
            @endif
        </div>
    </div>
</header>