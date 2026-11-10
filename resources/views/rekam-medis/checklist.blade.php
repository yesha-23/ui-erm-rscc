@extends('layouts.rekam-medis')
@section('title', $form['nama'])

@section('content')
@php
    $opsi      = $info['opsi'];
    $positif   = array_key_first($opsi); // 'ya' atau 'baik'
    $jawabLama = old('jawab', $isi['jawab'] ?? []);
    $total     = collect($form['bagian'])->sum(fn ($butir) => count($butir));
@endphp

{{-- ===== Judul ===== --}}
<x-judul-form :ikon="$info['ikon']" :kelompok="'Mutu · ' . $info['panjang']" :judul="$form['nama']"
              :sub="$total . ' butir · ' . ($waktu ? 'Terakhir disimpan ' . $waktu : 'Belum pernah diisi')" />

{{-- ===== Pilih form ===== --}}
<nav class="rs-card mb-3" aria-label="Pilih form {{ $info['judul'] }}">
    <p class="small fw-semibold mb-2">{{ count($daftar) }} form {{ $info['judul'] }}</p>
    <div class="d-flex flex-wrap gap-2">
        @foreach ($daftar as $d)
            <a href="{{ route('rekam-medis.checklist', [$pasien['no_reg'], $kategori, $d['slug']]) }}"
               @class(['rs-pill-filter', 'active' => $d['slug'] === $form['slug']])
               @if ($d['slug'] === $form['slug']) aria-current="page" @endif>{{ $d['nama'] }}</a>
        @endforeach
    </div>
</nav>

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

<form id="form-checklist" method="POST" novalidate data-rs-checklist data-positif="{{ $positif }}"
      action="{{ route('rekam-medis.checklist.simpan', [$pasien['no_reg'], $kategori, $form['slug']]) }}"
      class="d-flex flex-column gap-3">
    @csrf

    {{-- Waktu pengisian --}}
    <section class="rs-card">
        <div class="row g-3 align-items-end">
            <div class="col-sm-4">
                <label for="tanggal" class="form-label">Tanggal <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="date" id="tanggal" name="tanggal"
                       value="{{ old('tanggal', $isi['tanggal'] ?? now()->toDateString()) }}"
                       @class(['form-control', 'is-invalid' => $errors->has('tanggal')])>
                @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-8">
                <x-form.pilihan name="shift" label="Shift" wajib
                                :opsi="['pagi' => 'Pagi', 'siang' => 'Siang', 'malam' => 'Malam']"
                                :nilai="old('shift', $isi['shift'] ?? null)" />
            </div>
        </div>
    </section>

    {{-- Skor otomatis --}}
    <section class="rs-card" aria-live="polite">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <span class="small rs-text-muted">{{ $info['skor'] }}</span>
                <div class="rs-skor-angka" data-rs-skor>–</div>
                <span class="small rs-text-muted" data-rs-skor-rinci></span>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm" data-rs-tandai-semua>
                <i class="bi bi-check2-all me-1" aria-hidden="true"></i>Isi yang kosong dengan "{{ $opsi[$positif] }}"
            </button>
        </div>
        <div class="rs-skor-bar mt-2" aria-hidden="true"><span data-rs-skor-bar></span></div>
    </section>

    {{-- Butir per bagian --}}
    @foreach ($form['bagian'] as $judul => $butir)
        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3">{{ $judul }}</h3>
            <ol class="rs-cek list-unstyled mb-0">
                @foreach ($butir as $kode => $teks)
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
    @endforeach

    <section class="rs-card">
        <label for="catatan" class="form-label">Catatan <span class="rs-text-muted fw-normal">(opsional)</span></label>
        <textarea id="catatan" name="catatan" rows="2" class="form-control"
                  placeholder="mis. temuan, tindak lanjut">{{ old('catatan', $isi['catatan'] ?? '') }}</textarea>
        <p class="small rs-text-muted mt-2 mb-0">Diisi oleh: [Nama Petugas]</p>
    </section>
</form>

<div class="rs-action-bar">
    <span class="small rs-text-muted">
        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>Semua butir wajib dijawab. N/A tidak dihitung dalam skor.
    </span>
    <div class="rs-action-bar-buttons">
        <button type="submit" form="form-checklist" class="btn btn-primary">
            <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan checklist
        </button>
    </div>
</div>
@endsection