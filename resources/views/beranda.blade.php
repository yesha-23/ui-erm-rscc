@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
    {{-- Sapaan --}}
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <p class="rs-text-muted mb-1">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h1 class="h3 fw-bold mb-0">{{ $sapaan }}, {{ $namaPengguna }}</h1>
        </div>
        <span class="rs-chip-static"><i class="bi bi-calendar3" aria-hidden="true"></i>Data hari ini</span>
    </div>

    {{-- Kartu angka --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <x-stat-card label="Kunjungan hari ini" :value="$ringkasan['kunjungan']" icon="bi-people" warna="biru" />
        </div>
        <div class="col-md-4">
            <x-stat-card label="Rawat jalan" :value="$ringkasan['rawat_jalan']" icon="bi-clipboard2-pulse" warna="hijau" />
        </div>
        <div class="col-md-4">
            <x-stat-card label="Rawat inap" :value="$ringkasan['rawat_inap']" icon="bi-hospital" warna="oranye" />
        </div>
    </div>

    <div class="row g-3">
        {{-- Grafik --}}
        <div class="col-xl-8">
            <section class="rs-card h-100" aria-labelledby="judul-grafik">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <h2 id="judul-grafik" class="h6 fw-bold mb-1">Kunjungan per unit</h2>
                        <p class="small rs-text-muted mb-0">Hari ini · diurutkan dari terbanyak</p>
                    </div>
                    <span class="small rs-text-muted">Total {{ $perUnit->sum('jumlah') }} kunjungan</span>
                </div>

                <div class="rs-chart-unit">
                    <canvas data-rs-chart="bar-unit"
                            data-labels="{{ json_encode($perUnit->pluck('unit')) }}"
                            data-values="{{ json_encode($perUnit->pluck('jumlah')) }}"
                            role="img"
                            aria-label="Grafik batang kunjungan per unit. Terbanyak {{ $perUnit->first()['unit'] }} dengan {{ $perUnit->first()['jumlah'] }} kunjungan."></canvas>
                </div>

                {{-- Versi tabel untuk pembaca layar --}}
                <table class="visually-hidden">
                    <caption>Kunjungan per unit hari ini</caption>
                    <thead><tr><th scope="col">Unit</th><th scope="col">Jumlah</th></tr></thead>
                    <tbody>
                        @foreach ($perUnit as $u)
                            <tr><td>{{ $u['unit'] }}</td><td>{{ $u['jumlah'] }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </section>
        </div>

        {{-- Kolom kanan: ikut setinggi grafik --}}
        <div class="col-xl-4 d-flex flex-column gap-3">
            {{-- Tugas hari ini --}}
            <section class="rs-card" aria-labelledby="judul-tugas">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 id="judul-tugas" class="h6 fw-bold mb-0">Tugas hari ini</h2>
                    <span class="small rs-text-muted">{{ collect($tugas)->sum('jumlah') }} belum selesai</span>
                </div>
                <ul class="rs-task-list">
                    @foreach ($tugas as $t)
                        <li>
                            <a href="{{ route($t['route'], $t['query']) }}" class="rs-task">
                                <span class="rs-task-icon"><i class="bi {{ $t['ikon'] }}" aria-hidden="true"></i></span>
                                <span>{{ $t['label'] }}</span>
                                <span class="rs-task-count">{{ $t['jumlah'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Terakhir dibuka: memanjang sampai sejajar bawah grafik --}}
            <section class="rs-card flex-grow-1 d-flex flex-column" aria-labelledby="judul-pasien-terakhir">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h2 id="judul-pasien-terakhir" class="h6 fw-bold mb-0">Terakhir dibuka</h2>
                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none rs-kbd"
                            data-bs-toggle="modal" data-bs-target="#cariMenu">
                        Cari menu <kbd>Ctrl</kbd><kbd>K</kbd>
                    </button>
                </div>

                @if (empty($pasienTerakhir))
                    <div class="rs-terakhir-kosong flex-grow-1">
                        <i class="bi bi-clock-history" aria-hidden="true"></i>
                        <p class="mb-0">Pasien yang Anda buka akan muncul di sini, supaya bisa kembali dengan sekali klik.</p>
                    </div>
                @else
                    <ul class="rs-terakhir list-unstyled mb-0">
                        @foreach ($pasienTerakhir as $p)
                            @php
                                $inisial = collect(explode(' ', $p['nama']))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            @endphp
                            <li>
                                <a href="{{ route('rekam-medis.show', $p['no_reg']) }}" class="rs-terakhir-item">
                                    <span class="rs-avatar rs-avatar-pasien" aria-hidden="true">{{ $inisial }}</span>
                                    <span class="min-w-0 flex-grow-1">
                                        <span class="rs-terakhir-nama">{{ $p['nama'] }}</span>
                                        <span class="rs-terakhir-info">RM {{ $p['no_rm'] }} · {{ $p['unit'] }} · {{ $p['diagnosa_kerja'] }}</span>
                                    </span>
                                    <i class="bi bi-chevron-right rs-text-muted" aria-hidden="true"></i>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        {{-- Perlu tindak lanjut (lebar penuh) --}}
        <div class="col-12">
            <section class="rs-card p-0 overflow-hidden" aria-labelledby="judul-tindak">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 px-3 pt-3 pb-2">
                    <div>
                        <h2 id="judul-tindak" class="h6 fw-bold mb-1">Perlu tindak lanjut</h2>
                        <p class="small rs-text-muted mb-0">Pasien hari ini yang rekam medisnya belum lengkap</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="rs-status rs-status-proses">{{ $jumlahTindak }} pasien</span>
                        <a href="{{ route('kunjungan.igd', ['bayar' => 'semua']) }}" class="small fw-semibold text-decoration-none">Lihat semua →</a>
                    </div>
                </div>

                @if ($tindakLanjut->isEmpty())
                    <div class="rs-empty border-top">
                        <p class="rs-text-muted mb-0"><i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Semua rekam medis hari ini sudah lengkap.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table rs-table rs-table-kartu rs-table-ringkas mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Pasien</th>
                                    <th scope="col">Datang</th>
                                    <th scope="col">Dokter / perawat</th>
                                    <th scope="col">Yang belum</th>
                                    <th scope="col"><span class="visually-hidden">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tindakLanjut as $r)
                                    @php
                                        $inisial = collect(explode(' ', $r['nama']))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                                    @endphp
                                    <tr>
                                        <td class="rs-td-utama">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="rs-avatar rs-avatar-pasien" aria-hidden="true">{{ $inisial }}</span>
                                                <div>
                                                    <div class="fw-semibold">{{ $r['nama'] }}</div>
                                                    <div class="small rs-text-muted">RM {{ $r['no_rm'] }} · IGD</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Datang"><span>{{ $r['jam'] }}</span></td>
                                        <td data-label="Dokter / perawat">
                                            <div>
                                                <div>{{ $r['dokter_isi'] ?: '—' }}</div>
                                                <div class="small rs-text-muted">{{ $r['perawat'] ?: '—' }}</div>
                                            </div>
                                        </td>
                                        <td data-label="Yang belum">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($r['masalah'] as $m)
                                                    <span class="rs-status rs-status-proses">{{ $m }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="rs-td-aksi text-end">
                                            <a href="{{ route('rekam-medis.show', [$r['no_reg'], 'unit' => 'igd']) }}" class="btn btn-primary btn-sm"
                                               aria-label="Buka rekam medis {{ $r['nama'] }}">Buka</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection