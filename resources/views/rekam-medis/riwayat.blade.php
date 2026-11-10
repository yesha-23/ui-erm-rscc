@extends('layouts.rekam-medis')
@section('title', 'Riwayat pemeriksaan')

@section('content')
{{-- ===== Judul ===== --}}
<x-judul-form ikon="bi-clock-history" kelompok="Riwayat" judul="Riwayat pemeriksaan"
              :sub="'Semua kunjungan dan hasil pemeriksaan ' . $pasien['nama'] . ', terbaru di atas.'" />

{{-- ===== Cari + filter jenis ===== --}}
<div class="rs-card mb-3">
    <x-search label="Cari riwayat" placeholder="Cari pemeriksaan, diagnosis, atau unit…"
              data-rs-filter-list="#daftar-riwayat" data-rs-filter-empty="#riwayat-kosong" />

    <nav class="d-flex flex-wrap gap-2 mt-3" aria-label="Saring jenis riwayat">
        <a href="{{ route('rekam-medis.riwayat', $pasien['no_reg']) }}"
           @class(['rs-pill-filter', 'active' => ! $filter]) @if (! $filter) aria-current="page" @endif>
            Semua <span class="rs-pill-filter-count">{{ $total }}</span>
        </a>
        @foreach ($jenis as $kunci => [$label, $ikon])
            <a href="{{ route('rekam-medis.riwayat', [$pasien['no_reg'], 'jenis' => $kunci]) }}"
               @class(['rs-pill-filter', 'active' => $filter === $kunci]) @if ($filter === $kunci) aria-current="page" @endif>
                <i class="bi {{ $ikon }}" aria-hidden="true"></i>{{ $label }}
                <span class="rs-pill-filter-count">{{ $jumlah[$kunci] ?? 0 }}</span>
            </a>
        @endforeach
    </nav>
</div>

{{-- ===== Linimasa ===== --}}
@if ($kelompok->isEmpty())
    <div class="rs-card rs-empty">
        <p class="rs-text-muted mb-0">Belum ada riwayat untuk jenis ini.</p>
    </div>
@else
    <div id="daftar-riwayat">
        @foreach ($kelompok as $tanggal => $daftarHari)
            <section class="rs-timeline-hari" aria-label="{{ $tanggal }}">
                <h3 class="rs-timeline-tanggal">{{ $tanggal }}</h3>
                <ul class="rs-timeline-list">
                    @foreach ($daftarHari as $r)
                        <li class="rs-timeline-item"
                            data-rs-filter-text="{{ mb_strtolower($r['judul'] . ' ' . $r['ringkasan'] . ' ' . $r['unit'] . ' ' . $jenis[$r['jenis']][0]) }}">
                            <span class="rs-timeline-ikon rs-jenis-{{ $r['jenis'] }}" aria-hidden="true">
                                <i class="bi {{ $jenis[$r['jenis']][1] }}"></i>
                            </span>
                            <article class="rs-card">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
                                    <div>
                                        <span class="rs-format-tag">{{ $jenis[$r['jenis']][0] }}</span>
                                        <h4 class="h6 fw-bold mb-0">{{ $r['judul'] }}</h4>
                                    </div>
                                    <span class="small rs-text-muted">{{ $r['jam'] }} · {{ $r['unit'] }}</span>
                                </div>
                                <p class="mb-1 mt-2">{{ $r['ringkasan'] }}</p>
                                <p class="small rs-text-muted mb-0">{{ $r['oleh'] }}</p>

                                @if ($r['detail'])
                                    <details class="rs-detail-hasil mt-2">
                                        <summary>Lihat hasil ({{ count($r['detail']) }} pemeriksaan)</summary>
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Pemeriksaan</th>
                                                        <th scope="col">Hasil</th>
                                                        <th scope="col">Nilai rujukan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($r['detail'] as [$nama, $hasil, $rujukan, $flag])
                                                        <tr>
                                                            <td>{{ $nama }}</td>
                                                            <td @class(['fw-bold' => $flag])>
                                                                {{ $hasil }}
                                                                @if ($flag === 'tinggi')
                                                                    <span class="rs-flag rs-flag-tinggi">↑ tinggi</span>
                                                                @elseif ($flag === 'rendah')
                                                                    <span class="rs-flag rs-flag-rendah">↓ rendah</span>
                                                                @endif
                                                            </td>
                                                            <td class="rs-text-muted">{{ $rujukan }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </details>
                                @endif
                            </article>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>
    <p id="riwayat-kosong" class="rs-card rs-empty rs-text-muted" hidden>Tidak ada riwayat yang cocok dengan pencarian.</p>
@endif
@endsection