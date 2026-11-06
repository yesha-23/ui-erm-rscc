@extends('layouts.rekam-medis')
@section('title', 'Sesi Hemodialisa')

@section('content')
    @php
        $noReg   = $pasien['no_reg'];
        $v       = fn (string $k, $bawaan = '') => old($k, $isi[$k] ?? $bawaan);
        $fmt     = fn ($n, int $d = 1) => number_format((float) $n, $d, ',', '.');
        $urutan  = array_keys($daftarTahap);
        $berikut = $urutan[array_search($tahap, $urutan, true) + 1] ?? null;
        $ikonStatus = ['selesai' => 'bi-check-circle-fill', 'draf' => 'bi-record-circle', 'belum' => 'bi-circle'];
        $teksStatus = ['selesai' => 'selesai', 'draf' => 'sedang berjalan', 'belum' => 'belum diisi'];
        $kenaikan = isset($pra['bb_pre'], $pra['bb_kering']) ? $pra['bb_pre'] - $pra['bb_kering'] : null;
    @endphp

    <div class="mb-3">
        <p class="rs-eyebrow mb-1"><i class="bi bi-droplet me-1" aria-hidden="true"></i>Form rekam medis · Hemodialisa</p>
        <h2 class="h4 fw-bold mb-1">Sesi hemodialisa · HD ke-{{ $sesiKe }}</h2>
        <p class="rs-text-muted mb-0">{{ now()->translatedFormat('l, d F Y') }} · Perawat: {{ $pasien['ppa2'] }}</p>
    </div>

    {{-- Angka penting sesi ini --}}
    <dl class="rs-hd-ringkas mb-3">
        <div><dt>BB pre-HD</dt><dd>{{ isset($pra['bb_pre']) ? $fmt($pra['bb_pre']) . ' kg' : '—' }}</dd></div>
        <div><dt>BB kering</dt><dd>{{ isset($pra['bb_kering']) ? $fmt($pra['bb_kering']) . ' kg' : '—' }}</dd></div>
        <div><dt>Kenaikan BB</dt><dd>{{ $kenaikan !== null ? ($kenaikan >= 0 ? '+' : '') . $fmt($kenaikan) . ' kg' : '—' }}</dd></div>
        <div><dt>UF goal</dt><dd>{{ isset($resep['uf_goal']) ? $fmt($resep['uf_goal']) . ' L' : '—' }}</dd></div>
        <div><dt>QB · durasi</dt><dd>{{ isset($resep['qb']) ? $resep['qb'] . ' · ' . $fmt($resep['durasi']) . ' jam' : '—' }}</dd></div>
        <div><dt>Observasi</dt><dd>{{ count($intra) }}</dd></div>
    </dl>

    {{-- Tab tahap (bisa lompat ke mana saja) --}}
    <nav class="rs-doc-tabs rs-hd-tabs mb-3" aria-label="Tahap sesi hemodialisa">
        @foreach ($daftarTahap as $k => $t)
            <a href="{{ route('rekam-medis.hd', [$noReg, $k]) }}" @class(['rs-doc-tab', 'active' => $k === $tahap])
               @if ($k === $tahap) aria-current="page" @endif>
                <span class="rs-hd-no" aria-hidden="true">{{ $loop->iteration }}</span>
                {{ $t['label'] }}
                <i class="bi {{ $ikonStatus[$status[$k]] }} rs-hd-st is-{{ $status[$k] }}" aria-hidden="true"></i>
                <span class="visually-hidden">({{ $teksStatus[$status[$k]] }})</span>
            </a>
        @endforeach
    </nav>

    @if ($errors->any())
        <div class="alert alert-danger rs-flash" role="alert">
            <strong>Belum bisa disimpan.</strong> Periksa {{ $errors->count() }} isian yang ditandai merah.
        </div>
    @endif

    @include('rekam-medis.hd._' . $tahap)

    @if ($tahap !== 'intra')
        <div class="rs-action-bar">
            <span class="small rs-text-muted">
                <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                {{ $simpanTerakhir ? 'Tersimpan · ' . $simpanTerakhir : 'Belum disimpan' }}
            </span>
            <div class="rs-action-bar-buttons">
                @if ($berikut && $status[$tahap] === 'selesai')
                    <a href="{{ route('rekam-medis.hd', [$noReg, $berikut]) }}" class="btn btn-outline-secondary">
                        Lanjut ke {{ $daftarTahap[$berikut]['label'] }} <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                @endif
                <button type="submit" form="formHd" class="btn btn-primary">
                    <i class="bi bi-floppy me-1" aria-hidden="true"></i>Simpan {{ $daftarTahap[$tahap]['label'] }}
                </button>
            </div>
        </div>
    @endif
@endsection