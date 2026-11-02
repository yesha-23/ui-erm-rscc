@extends('layouts.rekam-medis')
@section('title', 'Cetak')

@section('content')
<div class="mb-3">
    <p class="rs-eyebrow mb-1"><i class="bi bi-printer me-1" aria-hidden="true"></i>Dokumen · Cetak</p>
    <h2 class="h4 fw-bold mb-0">Cetak dokumen</h2>
</div>

{{-- Resume medis --}}
<section class="rs-card d-flex flex-wrap align-items-center gap-3 mb-3">
    <span class="rs-cetak-icon"><i class="bi bi-file-earmark-medical" aria-hidden="true"></i></span>
    <div class="flex-grow-1">
        <h3 class="h6 fw-bold mb-1">Resume medis</h3>
        <p class="small rs-text-muted mb-0">Ringkasan kunjungan ini: keluhan, pemeriksaan, diagnosa, tindakan dan terapi.</p>
    </div>
    <a href="{{ route('rekam-medis.cetak.resume', $pasien['no_reg']) }}" target="_blank" rel="noopener"
       class="btn btn-primary">
        <i class="bi bi-printer me-1" aria-hidden="true"></i>Pratinjau &amp; cetak
    </a>
</section>

{{-- Dokumen kunjungan ini --}}
<section class="rs-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h3 class="h6 fw-bold mb-0">Dokumen kunjungan ini</h3>
        <a href="{{ route('rekam-medis.dokumen', [$pasien['no_reg'], 'surat']) }}" class="small fw-semibold text-decoration-none">
            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Buat dokumen baru
        </a>
    </div>

    @if (count($tersimpan))
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Dokumen</th>
                        <th scope="col">Dibuat</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-end"><span class="visually-hidden">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tersimpan as $d)
                        <tr>
                            <td>
                                <span class="fw-semibold">{{ $d['nama'] }}</span>
                                <span class="d-block small rs-text-muted">{{ $d['jenis'] === 'surat' ? 'Surat' : 'Persetujuan' }}</span>
                            </td>
                            <td class="small">
                                {{ $d['waktu'] }}
                                <span class="d-block rs-text-muted">{{ $d['oleh'] }}</span>
                            </td>
                            <td>
                                @if ($d['final'])
                                    <x-badge jenis="sukses">Ditandatangani</x-badge>
                                @else
                                    <x-badge jenis="peringatan">Draf</x-badge>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                @unless ($d['final'])
                                    <a href="{{ route('rekam-medis.dokumen', [$pasien['no_reg'], $d['jenis'], $d['slug']]) }}"
                                       class="btn btn-sm btn-outline-secondary">Lanjutkan</a>
                                @endunless
                                <a href="{{ route('rekam-medis.dokumen.cetak', [$pasien['no_reg'], $d['jenis'], $d['slug']] + ($d['final'] ? [] : ['draf' => 1])) }}"
                                   target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-printer me-1" aria-hidden="true"></i>Pratinjau
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="rs-empty">
            <span class="rs-empty-icon"><i class="bi bi-file-earmark" aria-hidden="true"></i></span>
            <p class="rs-text-muted mb-0">Belum ada dokumen yang dibuat pada kunjungan ini.</p>
        </div>
    @endif
</section>
@endsection