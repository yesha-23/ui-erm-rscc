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

            {{-- Pintasan unit --}}
            <section class="rs-card" aria-labelledby="judul-unit">
                <h2 id="judul-unit" class="h6 fw-bold mb-3">Buka unit</h2>
                <div class="row g-2">
                    @foreach ($unit as $u)
                        <div class="col-6">
                            <a href="{{ route($u['route']) }}" class="rs-shortcut">
                                <i class="bi {{ $u['icon'] }}" aria-hidden="true"></i>
                                <span class="fw-bold">{{ $u['label'] }}</span>
                                <small>{{ $u['ket'] }}</small>
                            </a>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
@endsection