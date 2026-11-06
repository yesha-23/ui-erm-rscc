@extends('layouts.rekam-medis')
@section('title', 'Checklist keselamatan operasi')

@section('content')
@php
    $noReg     = $pasien['no_reg'];
    $info      = $daftarTahap[$tahap];
    $jawabLama = old('jawab', $isi['jawab'] ?? []);
    $terkunci  = $sebelumnya !== null; // tahap sebelumnya belum disimpan
@endphp

{{-- ===== Judul ===== --}}
<div class="mb-3">
    <p class="rs-eyebrow mb-1"><i class="bi bi-scissors me-1" aria-hidden="true"></i>Form rekam medis · Kamar Operasi</p>
    <h2 class="h4 fw-bold mb-1">Checklist keselamatan operasi</h2>
    <p class="rs-text-muted mb-0">{{ $pasien['diagnosa_kerja'] }} · DPJP: {{ $pasien['dpjp_nama'] }}</p>
</div>

{{-- ===== Ringkasan jam tiap tahap ===== --}}
<dl class="rs-hd-ringkas mb-3">
    @foreach ($daftarTahap as $k => $t)
        <div><dt>{{ $t['label'] }}</dt><dd>{{ $data[$k]['isi']['jam'] ?? '—' }}</dd></div>
    @endforeach
    <div><dt>Kepatuhan</dt><dd>{{ $skorTotal === null ? '—' : $skorTotal . '%' }}</dd></div>
</dl>

{{-- ===== Tab tahap ===== --}}
<nav class="rs-doc-tabs rs-hd-tabs mb-3" aria-label="Tahap checklist keselamatan operasi">
    @foreach ($daftarTahap as $k => $t)
        @php $st = isset($data[$k]) ? 'selesai' : 'belum'; @endphp
        <a href="{{ route('rekam-medis.ok-checklist', [$noReg, $k]) }}" @class(['rs-doc-tab', 'active' => $k === $tahap])
           @if ($k === $tahap) aria-current="page" @endif>
            <span class="rs-hd-no" aria-hidden="true">{{ $loop->iteration }}</span>
            {{ $t['label'] }}
            <i class="bi {{ $st === 'selesai' ? 'bi-check-circle-fill' : 'bi-circle' }} rs-hd-st is-{{ $st }}" aria-hidden="true"></i>
            <span class="visually-hidden">({{ $st === 'selesai' ? 'selesai' : 'belum diisi' }})</span>
        </a>
    @endforeach
</nav>

@if ($terkunci)
    <div class="alert alert-warning d-flex gap-2 align-items-start" role="alert">
        <i class="bi bi-lock-fill" aria-hidden="true"></i>
        <div>
            <strong>{{ $info['label'] }} belum bisa diisi.</strong>
            Simpan <a href="{{ route('rekam-medis.ok-checklist', [$noReg, $sebelumnya]) }}">{{ $daftarTahap[$sebelumnya]['label'] }}</a> lebih dulu.
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger d-flex gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        <ul class="mb-0 ps-3">
            @foreach (collect($errors->all())->unique() as $pesan)
                <li>{{ $pesan }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="form-ok-checklist" method="POST" novalidate data-rs-checklist data-positif="ya"
      action="{{ route('rekam-medis.ok-checklist.simpan', [$noReg, $tahap]) }}">
    @csrf
    <fieldset class="d-flex flex-column gap-3" @disabled($terkunci)>

        {{-- Kapan tahap ini dilakukan --}}
        <section class="rs-card">
            <div class="row g-3 align-items-end">
                <div class="col-sm-8">
                    <h3 class="h6 fw-bold mb-1">{{ $info['label'] }}</h3>
                    <p class="small rs-text-muted mb-0">{{ $info['waktu'] }}</p>
                    @if ($tahap === 'sign-in')
                        <p class="small mb-0 mt-2">
                            <i class="bi bi-exclamation-diamond me-1" aria-hidden="true"></i>Alergi tercatat: <strong>{{ $pasien['alergi'] }}</strong>
                        </p>
                    @endif
                </div>
                <div class="col-sm-4">
                    <label for="jam" class="form-label">Jam <span class="text-danger" aria-hidden="true">*</span></label>
                    <input type="time" id="jam" name="jam"
                           value="{{ old('jam', $isi['jam'] ?? now()->format('H:i')) }}"
                           @class(['form-control', 'is-invalid' => $errors->has('jam')])>
                    @error('jam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </section>

        {{-- Skor otomatis tahap ini (dihitung oleh initChecklist di rscc.js) --}}
        <section class="rs-card" aria-live="polite">
            <span class="small rs-text-muted">Kepatuhan {{ $info['label'] }}</span>
            <div class="rs-skor-angka" data-rs-skor>–</div>
            <span class="small rs-text-muted" data-rs-skor-rinci></span>
            <div class="rs-skor-bar mt-2" aria-hidden="true"><span data-rs-skor-bar></span></div>
        </section>

        {{-- Butir --}}
        <section class="rs-card">
            <ol class="rs-cek list-unstyled mb-0">
                @foreach ($info['butir'] as $kode => $teks)
                    <li @class(['rs-cek-baris', 'is-error' => $errors->has("jawab.$kode")])>
                        <span class="rs-cek-teks" id="cek-{{ $kode }}">{{ $teks }}</span>
                        <div class="rs-segmented rs-cek-pilih" role="radiogroup" aria-labelledby="cek-{{ $kode }}">
                            @foreach ($opsi as $nilai => $label)
                                <input type="radio" class="btn-check" autocomplete="off"
                                       name="jawab[{{ $kode }}]" id="cek-{{ $kode }}-{{ $nilai }}" value="{{ $nilai }}"
                                       @checked(($jawabLama[$kode] ?? null) === $nilai)>
                                <label for="cek-{{ $kode }}-{{ $nilai }}" class="rs-segmented-item rs-jawab-{{ $nilai }}">{{ $label }}</label>
                            @endforeach
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>

        <section class="rs-card">
            <label for="catatan" class="form-label">Catatan / tindak lanjut</label>
            <textarea id="catatan" name="catatan" rows="2"
                      @class(['form-control', 'is-invalid' => $errors->has('catatan')])
                      placeholder="Wajib diisi bila ada butir yang dijawab Tidak">{{ old('catatan', $isi['catatan'] ?? '') }}</textarea>
            @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <p class="small rs-text-muted mt-2 mb-0">Diisi oleh: [Nama Petugas]</p>
        </section>
    </fieldset>
</form>

<div class="rs-action-bar">
    <span class="small rs-text-muted">
        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
        {{ isset($data[$tahap]) ? 'Tersimpan · ' . $data[$tahap]['waktu'] : 'Semua butir wajib dijawab. N/A tidak dihitung.' }}
    </span>
    <div class="rs-action-bar-buttons">
        <button type="submit" form="form-ok-checklist" class="btn btn-primary" @disabled($terkunci)>
            <i class="bi bi-floppy me-1" aria-hidden="true"></i>Simpan {{ $info['label'] }}
        </button>
    </div>
</div>
@endsection