@extends('layouts.rekam-medis')
@section('title', 'Permintaan ' . $info['judul'])

@section('content')
@php
    $prioritas  = old('prioritas', 'rutin');
    $labelStatus = ['terkirim' => 'Terkirim', 'proses' => 'Diproses', 'hasil' => 'Hasil keluar'];
@endphp

{{-- ===== Judul + tab jenis ===== --}}
<x-judul-form ikon="bi-eyedropper" kelompok="Penunjang" judul="Permintaan pemeriksaan">
    <nav class="rs-doc-tabs" aria-label="Jenis pemeriksaan">
        @foreach ($semuaJenis as $k => $j)
            <a href="{{ route('rekam-medis.permintaan', [$pasien['no_reg'], $k]) }}"
               @class(['rs-doc-tab', 'active' => $jenis === $k])
               @if ($jenis === $k) aria-current="page" @endif>
                <i class="bi {{ $j['ikon'] }}" aria-hidden="true"></i>{{ $j['judul'] }}
            </a>
        @endforeach
    </nav>
</x-judul-form>

@if ($errors->any())
    <div class="alert alert-danger d-flex gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $pesan)
                <li>{{ $pesan }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="form-permintaan" method="POST" novalidate data-rs-pemeriksaan
      action="{{ route('rekam-medis.permintaan.kirim', [$pasien['no_reg'], $jenis]) }}"
      class="rs-permintaan">
    @csrf

    {{-- ===== Kiri: katalog pemeriksaan ===== --}}
    <section class="rs-card" aria-label="Daftar pemeriksaan {{ $info['tujuan'] }}">
        <x-search label="Cari pemeriksaan" autocomplete="off" data-rs-cari-pemeriksaan
                  placeholder="Cari pemeriksaan, mis. {{ $info['contoh'] }}" />

        @foreach ($grup as $g)
            <fieldset class="rs-grup-periksa" data-rs-grup>
                <legend class="rs-grup-judul">{{ $g['judul'] }}</legend>
                <div class="rs-tile-grid">
                    @foreach ($g['items'] as $it)
                        <div data-rs-item="{{ mb_strtolower($it['nama']) }}">
                            <input type="checkbox" class="btn-check" autocomplete="off"
                                   id="periksa-{{ $it['kode'] }}" name="items[]" value="{{ $it['kode'] }}"
                                   data-nama="{{ $it['nama'] }}" data-catatan="{{ $it['catatan'] }}"
                                   @checked(in_array($it['kode'], $dipilih, true))>
                            <label class="rs-tile" for="periksa-{{ $it['kode'] }}">
                                <span class="rs-tile-box" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                                <span class="rs-tile-teks">
                                    {{ $it['nama'] }}
                                    @if ($it['catatan'])
                                        <small>{{ $it['catatan'] }}</small>
                                    @endif
                                </span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </fieldset>
        @endforeach

        <p class="small rs-text-muted text-center py-4 mb-0" data-rs-cari-kosong hidden>
            Pemeriksaan tidak ditemukan. Coba kata lain.
        </p>
    </section>

    {{-- ===== Kanan: ringkasan & kirim ===== --}}
    <aside class="rs-card rs-permintaan-ringkasan" aria-label="Ringkasan permintaan">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="h6 fw-bold mb-0">Ringkasan permintaan</h3>
            <span class="rs-pill-count" aria-live="polite"><span data-rs-jumlah>0</span> dipilih</span>
        </div>

        <ul class="rs-ringkasan list-unstyled mb-0" data-rs-ringkasan hidden></ul>
        <p class="rs-ringkasan-kosong" data-rs-ringkasan-kosong>
            Belum ada pemeriksaan dipilih. Klik pemeriksaan di daftar.
        </p>
        @error('items')
            <div class="small text-danger mt-2">{{ $message }}</div>
        @enderror

        <fieldset class="mt-3">
            <legend class="form-label fs-6 mb-2">Prioritas</legend>
            <div class="rs-segmented">
                @foreach (['cito' => 'Cito', 'rutin' => 'Rutin'] as $nilai => $label)
                    <input type="radio" class="btn-check" name="prioritas" id="prio-{{ $nilai }}"
                           value="{{ $nilai }}" autocomplete="off" @checked($prioritas === $nilai)>
                    <label for="prio-{{ $nilai }}" @class(['rs-segmented-item', 'is-cito' => $nilai === 'cito'])>
                        @if ($nilai === 'cito')<i class="bi bi-lightning-charge-fill me-1" aria-hidden="true"></i>@endif{{ $label }}
                    </label>
                @endforeach
            </div>
            <div class="form-text">Cito = hasil dibutuhkan segera (kondisi gawat).</div>
        </fieldset>

        <div class="mt-3">
            <label for="diagnosa" class="form-label">Diagnosa / catatan klinis</label>
            <textarea id="diagnosa" name="diagnosa" rows="3"
                      @class(['form-control', 'is-invalid' => $errors->has('diagnosa')])>{{ old('diagnosa', $pasien['diagnosa_kerja']) }}</textarea>
            @error('diagnosa')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-3">
            <label for="catatan" class="form-label">Catatan untuk petugas <span class="rs-text-muted fw-normal">(opsional)</span></label>
            <textarea id="catatan" name="catatan" rows="2" placeholder="mis. pasien di bed 3, sulit diambil darah"
                      @class(['form-control', 'is-invalid' => $errors->has('catatan')])>{{ old('catatan') }}</textarea>
            @error('catatan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-3">
            <i class="bi bi-send me-1" aria-hidden="true"></i>Kirim ke {{ $info['tujuan'] }}
        </button>
        <p class="small rs-text-muted text-center mt-2 mb-0">
            Permintaan laboratorium dan radiologi dikirim terpisah.
        </p>
    </aside>
</form>

{{-- ===== Riwayat permintaan kunjungan ini ===== --}}
<section class="rs-card p-0 overflow-hidden mt-3">
    <div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-2">
        <h3 class="h6 fw-bold mb-0">Permintaan kunjungan ini</h3>
        <span class="rs-pill-count">{{ count($riwayat) }}</span>
    </div>

    @if (count($riwayat))
        <div class="table-responsive">
            <table class="table rs-table rs-table-kartu rs-table-ringkas mb-0">
                <thead>
                    <tr>
                        <th scope="col">Pemeriksaan</th>
                        <th scope="col">Waktu</th>
                        <th scope="col">Prioritas</th>
                        <th scope="col">Status</th>
                        <th scope="col"><span class="visually-hidden">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($riwayat as $p)
                        <tr>
                            <td class="rs-td-utama">
                                <div>
                                    <div class="fw-semibold">
                                        <i class="bi {{ $semuaJenis[$p['jenis']]['ikon'] }} me-1 rs-text-muted" aria-hidden="true"></i>{{ implode(', ', $p['items']) }}
                                    </div>
                                    <div class="small rs-text-muted">{{ $semuaJenis[$p['jenis']]['judul'] }} · {{ $p['oleh'] }}</div>
                                </div>
                            </td>
                            <td data-label="Waktu"><span>{{ $p['waktu'] }}</span></td>
                            <td data-label="Prioritas">
                                @if ($p['prioritas'] === 'cito')
                                    <span class="rs-prio-cito"><i class="bi bi-lightning-charge-fill me-1" aria-hidden="true"></i>Cito</span>
                                @else
                                    <span>Rutin</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                <span class="rs-status rs-status-{{ $p['status'] }}">{{ $labelStatus[$p['status']] }}</span>
                            </td>
                            <td class="rs-td-aksi text-end text-nowrap">
                                @if ($p['status'] === 'hasil')
                                    {{-- TODO: arahkan ke halaman Riwayat pemeriksaan / hasil --}}
                                    <a href="#" class="btn btn-outline-secondary btn-sm">Lihat hasil</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="rs-empty">
            <p class="rs-text-muted mb-0">Belum ada permintaan pada kunjungan ini.</p>
        </div>
    @endif
</section>
@endsection