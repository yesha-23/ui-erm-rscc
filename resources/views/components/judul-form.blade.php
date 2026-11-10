{{--
  Judul halaman form di ruang kerja pasien: ikon Kembali di kiri judul + judul yang tetap di atas saat digulir.
  Ikon Kembali naik satu tingkat ke halaman induknya:
    form (menu samping) → Ringkasan pasien (bawaan) · Ringkasan → daftar kunjungan · isi dokumen → daftar dokumen.
  Atur tujuan lain lewat kembali-ke dan label-kembali.
  Pemakaian:
    <x-judul-form ikon="bi-journal-text" kelompok="Catatan terintegrasi" judul="CPPT dan SBAR" sub="Keterangan singkat">
        (opsional) tombol aksi di kanan, mis. <button class="btn btn-primary">…</button>
    </x-judul-form>
--}}
@props([
    'ikon'         => 'bi-file-earmark-text',
    'kelompok'     => '',
    'judul',
    'sub'          => null,
    'kembaliKe'    => null,
    'labelKembali' => 'Kembali ke Ringkasan pasien',
])

@php
    // No. Reg diambil dari URL /rekam-medis/{noReg}/…
    $kembaliKe ??= route('rekam-medis.show', request()->route('noReg'));
@endphp

<div {{ $attributes->merge(['class' => 'rs-judul-form']) }}>
    <div class="rs-judul-form-teks">
        @if ($kelompok)
            <p class="rs-eyebrow mb-1">@if ($ikon)<i class="bi {{ $ikon }} me-1" aria-hidden="true"></i>@endif{{ $kelompok }}</p>
        @endif
        <div class="rs-judul-form-baris">
            <a href="{{ $kembaliKe }}" class="rs-kembali" aria-label="{{ $labelKembali }}" title="{{ $labelKembali }}">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
            </a>
            <h2 class="h4 fw-bold mb-0">{{ $judul }}</h2>
        </div>
        @if ($sub)
            <p class="small rs-text-muted mb-0 mt-1">{{ $sub }}</p>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="rs-judul-form-aksi">{{ $slot }}</div>
    @endif
</div>