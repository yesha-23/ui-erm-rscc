@extends('layouts.app')
@section('title', 'Kunjungan ' . $judul)

@section('content')
    <h1 class="h3 fw-bold mb-1">Kunjungan {{ $judul }}</h1>
    <p class="text-secondary mb-4">Halaman sementara — dikerjakan oleh Anggota {{ $pj }}.</p>

    <div class="rs-card">
        @foreach ($rows as $r)
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 py-2 border-bottom">
                <div>
                    <div class="fw-bold">{{ $r['nama'] }}</div>
                    <div class="small text-secondary">RM {{ $r['no_rm'] }} · Reg {{ $r['no_reg'] }}</div>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <x-badge :jenis="$r['penjamin'] === 'JKN' ? 'jkn' : 'nonjkn'">{{ $r['penjamin'] }}</x-badge>
                    <x-badge :jenis="$r['sudah_datang'] ? 'datang' : 'belum'">
                        {{ $r['sudah_datang'] ? 'Sudah datang' : 'Belum datang' }}
                    </x-badge>
                    <a href="{{ route('rekam-medis.show', $r['no_reg']) }}" class="btn btn-primary btn-sm">Rekam Medis</a>
                </div>
            </div>
        @endforeach
    </div>
@endsection