{{-- Tanda-tanda vital. Rentang normal = acuan dewasa; sesuaikan dengan SPO RS. --}}
@props(['isi' => []])

@php
    $kolom = [
        ['name' => 'td_sistolik',  'label' => 'TD sistolik',  'unit' => 'mmHg',    'min' => 90, 'max' => 139,  'step' => '1'],
        ['name' => 'td_diastolik', 'label' => 'TD diastolik', 'unit' => 'mmHg',    'min' => 60, 'max' => 89,   'step' => '1'],
        ['name' => 'nadi',         'label' => 'Nadi',         'unit' => 'x/menit', 'min' => 60, 'max' => 100,  'step' => '1'],
        ['name' => 'napas',        'label' => 'Napas',        'unit' => 'x/menit', 'min' => 12, 'max' => 20,   'step' => '1'],
        ['name' => 'suhu',         'label' => 'Suhu',         'unit' => '°C',      'min' => 36, 'max' => 37.5, 'step' => '0.1'],
        ['name' => 'spo2',         'label' => 'SpO₂',         'unit' => '%',       'min' => 95, 'max' => 100,  'step' => '1'],
    ];
    $angka = fn ($n) => str_replace('.', ',', (string) $n);
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