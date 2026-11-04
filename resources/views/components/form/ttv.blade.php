{{-- Tanda-tanda vital. Rentang normal diambil dari App\Support\RentangTtv (acuan dewasa; sesuaikan SPO RS). --}}
@props(['isi' => []])

@php
    $kolom = \App\Support\RentangTtv::KOLOM;
    $angka = fn ($n) => \App\Support\RentangTtv::angka($n);
@endphp

<div class="rs-ttv-grid">
    @foreach ($kolom as $k)
        <div class="rs-ttv" data-rs-ttv data-min="{{ $k['min'] }}" data-max="{{ $k['max'] }}">
            <label for="ttv-{{ $k['name'] }}" class="rs-ttv-label">
                {{ $k['label'] }} <span class="text-danger" aria-hidden="true">*</span>
            </label>
            <div class="rs-ttv-input">
                <input type="number" inputmode="decimal" step="{{ $k['step'] }}"
                       id="ttv-{{ $k['name'] }}" name="{{ $k['name'] }}"
                       value="{{ old($k['name'], $isi[$k['name']] ?? '') }}"
                       aria-describedby="ttv-{{ $k['name'] }}-catatan"
                       @class(['form-control', 'is-invalid' => $errors->has($k['name'])])>
                <span class="rs-ttv-unit">{{ $k['unit'] }}</span>
            </div>
            <div class="rs-ttv-catatan" id="ttv-{{ $k['name'] }}-catatan" aria-live="polite">
                Normal {{ $angka($k['min']) }}–{{ $angka($k['max']) }}
            </div>
            @error($k['name'])
                <div class="small text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>
    @endforeach
</div>
<p class="small rs-text-muted mt-2 mb-0">Rentang normal dewasa sebagai acuan; sesuaikan dengan SPO RS.</p>