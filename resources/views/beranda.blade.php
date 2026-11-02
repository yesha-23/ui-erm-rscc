@extends('layouts.app')
@section('title', 'Beranda')

@section('content')
    <p class="text-secondary mb-1">{{ now()->translatedFormat('l, d F Y') }}</p>
    <h1 class="h3 fw-bold mb-4">Selamat datang</h1>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <x-stat-card label="Kunjungan hari ini" :value="$ringkasan['kunjungan']" icon="bi-people" />
        </div>
        <div class="col-md-4">
            <x-stat-card label="Rawat jalan" :value="$ringkasan['rawat_jalan']" icon="bi-clipboard2-pulse" />
        </div>
        <div class="col-md-4">
            <x-stat-card label="Rawat inap" :value="$ringkasan['rawat_inap']" icon="bi-hospital" />
        </div>
    </div>

    <div class="rs-card">
        <h2 class="h6 fw-bold mb-3">Kunjungan per unit</h2>
        @foreach ($perUnit as $u)
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span>{{ $u['unit'] }}</span>
                <span class="fw-bold">{{ $u['jumlah'] }}</span>
            </div>
        @endforeach
    </div>
@endsection