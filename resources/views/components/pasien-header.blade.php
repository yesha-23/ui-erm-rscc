@props(['pasien', 'crumb' => null])

{{--
  Header ruang kerja pasien (revisi 10 Nov, masukan RS):
  - Kiri  : tombol buka/tutup "Menu rekam medis", selebar kolom menu.
  - Tengah: nama pasien besar + No. RM dan No. Reg yang menonjol, sejajar dengan isi halaman.
            Chip alergi kecil di baris nomor (bisa diklik untuk mengubah).
  - Kanan : Riwayat, Cetak, Submit final.
  Saat isi halaman digulir, header menjadi ringkas (class is-ringkas, diatur initHeaderPasien di rscc.js).
  Data lain (JK, umur, datang, DPJP, penjamin) ada di kartu Identitas pasien pada Ringkasan.
--}}

@php
    $sub   = $pasien['unit_sub'] ?? null;
    $final = session("final.{$pasien['no_reg']}");

    // [kelas badge, ikon, teks]
    $alergi = match ($pasien['alergi_status'] ?? null) {
        'ada'   => ['rs-badge-nonjkn', 'bi-exclamation-triangle-fill', 'Alergi: ' . $pasien['alergi']],
        'tidak' => ['rs-badge-jkn', 'bi-check-circle-fill', 'Tidak ada alergi'],
        default => ['rs-badge-belum', 'bi-question-circle-fill', 'Alergi belum dikaji'],
    };
@endphp

<header class="rs-pasien-header" data-rs-pasien-header>
    <div class="rs-ph">
        {{-- Tombol menu rekam medis (status awal aria-expanded diperbaiki oleh initMenuRmBukaTutup) --}}
        <div class="rs-ph-menu">
            <button type="button" class="rs-ph-menu-tombol" data-rs-rmmenu-toggle
                    aria-controls="menuRekamMedis" aria-expanded="true">
                <i class="bi bi-list-ul" aria-hidden="true"></i>
                <span class="flex-grow-1 text-start">Menu rekam medis</span>
                <i class="bi bi-chevron-down rs-ph-menu-panah" aria-hidden="true"></i>
            </button>
        </div>

        <div class="rs-ph-identitas">
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

            <h1 class="rs-ph-nama">{{ $pasien['nama'] }}</h1>

            <div class="rs-ph-id">
                <span class="rs-ph-nomor">
                    <span class="rs-ph-nomor-label">No. RM</span>
                    <span class="rs-ph-nomor-isi">{{ $pasien['no_rm'] }}</span>
                </span>
                <span class="rs-ph-nomor">
                    <span class="rs-ph-nomor-label">No. Reg</span>
                    <span class="rs-ph-nomor-isi">{{ $pasien['no_reg'] }}</span>
                </span>
                <button type="button" class="rs-badge rs-badge-tombol rs-ph-alergi {{ $alergi[0] }}"
                        data-bs-toggle="modal" data-bs-target="#modalAlergi"
                        title="{{ $alergi[2] }} — klik untuk mengubah">
                    <i class="bi {{ $alergi[1] }}" aria-hidden="true"></i>
                    <span class="rs-badge-teks">{{ $alergi[2] }}</span>
                    <span class="visually-hidden">(ubah riwayat alergi)</span>
                </button>
            </div>
        </div>

        <div class="rs-ph-aksi">
            <a href="{{ route('rekam-medis.riwayat', $pasien['no_reg']) }}" class="btn btn-outline-secondary rs-ph-aksi-sekunder">
                <i class="bi bi-clock-history me-1" aria-hidden="true"></i>Riwayat
            </a>
            <a href="{{ route('rekam-medis.cetak', $pasien['no_reg']) }}" class="btn btn-outline-secondary rs-ph-aksi-sekunder">
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