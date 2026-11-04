{{--
  Pilihan berbentuk chip.
  <x-form.pilihan name="cara_datang" label="Cara datang" :opsi="[...]" :nilai="..." wajib />
  tipe="checkbox" → boleh pilih banyak (dikirim sebagai name[])
  :warna="['merah' => 'merah']" → chip berwarna (triase)
--}}
@props([
    'name',
    'opsi',
    'nilai'  => null,
    'tipe'   => 'radio',
    'label'  => null,
    'wajib'  => false,
    'warna'  => [],
])

@php
    $banyak   = $tipe === 'checkbox';
    $terpilih = collect($banyak ? (array) $nilai : [$nilai])->map(fn ($v) => (string) $v)->all();
    $idDasar  = 'f-' . trim(str_replace(['[', ']', '.'], '-', $name), '-');
    $kunciErr = str_replace(['[', ']'], ['.', ''], $name); // jatuh[a] → jatuh.a
@endphp

<fieldset {{ $attributes->class(['rs-pilihan-grup']) }}>
    @if ($label)
        <legend class="rs-legend">
            {{ $label }}@if ($wajib) <span class="text-danger" aria-hidden="true">*</span>@endif
        </legend>
    @endif

    <div class="rs-pilihan">
        @foreach ($opsi as $kode => $teks)
            @php $id = $idDasar . '-' . \Illuminate\Support\Str::slug((string) $kode); @endphp
            <input type="{{ $tipe }}" class="btn-check" autocomplete="off"
                   id="{{ $id }}" name="{{ $banyak ? $name . '[]' : $name }}" value="{{ $kode }}"
                   @checked(in_array((string) $kode, $terpilih, true))>
            <label for="{{ $id }}" @class(['rs-pilih', 'rs-pilih-' . ($warna[$kode] ?? '') => isset($warna[$kode])])>
                {{ $teks }}
            </label>
        @endforeach
    </div>

    @error($kunciErr)
        <div class="small text-danger mt-1">{{ $message }}</div>
    @enderror
</fieldset>