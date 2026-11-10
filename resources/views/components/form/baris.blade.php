{{--
  Satu baris pertanyaan seperti di ERM lama: pertanyaan di kiri, pilihan (chip) di kanan,
  dan isian keterangan yang muncul bila pilihan tertentu dipilih (mis. "Ya" atau "Lainnya").
  <x-form.baris label="1. Penggunaan alat bantu" name="alat_bantu" :opsi="$opsi['ya_tidak']" :nilai="..."
                isian="alat_bantu_ket" :isian-nilai="..." isian-saat="ya" />
  tipe="checkbox"          → boleh pilih banyak (dikirim sebagai name[])
  isian tanpa isian-saat   → kotak keterangan selalu tampil
--}}
@props([
    'label',
    'name',
    'opsi',
    'nilai'       => null,
    'tipe'        => 'radio',
    'wajib'       => false,
    'isian'       => null,
    'isianNilai'  => null,
    'isianSaat'   => null,
    'placeholder' => 'Sebutkan…',
])

@php
    $banyak     = $tipe === 'checkbox';
    $terpilih   = collect($banyak ? (array) $nilai : [$nilai])->filter(fn ($v) => $v !== null)
                    ->map(fn ($v) => (string) $v)->all();
    $idDasar    = 'f-' . trim(str_replace(['[', ']', '.'], '-', $name), '-');
    $kunciErr   = str_replace(['[', ']'], ['.', ''], $name);   // jatuh[a] → jatuh.a
    $kunciIsian = $isian ? str_replace(['[', ']'], ['.', ''], $isian) : null;
@endphp

<div {{ $attributes->class(['rs-aak-baris']) }} role="group" aria-labelledby="{{ $idDasar }}-tanya">
    <div class="rs-aak-tanya" id="{{ $idDasar }}-tanya">
        {{ $label }}@if ($wajib) <span class="text-danger" aria-hidden="true">*</span>@endif
    </div>

    <div class="rs-aak-jawab">
        <div class="rs-pilihan">
            @foreach ($opsi as $kode => $teks)
                @php $id = $idDasar . '-' . \Illuminate\Support\Str::slug((string) $kode); @endphp
                <input type="{{ $tipe }}" class="btn-check" autocomplete="off" id="{{ $id }}"
                       name="{{ $banyak ? $name . '[]' : $name }}" value="{{ $kode }}"
                       @checked(in_array((string) $kode, $terpilih, true))
                       @if ($isianSaat !== null && (string) $kode === (string) $isianSaat) data-rs-buka-isian @endif>
                <label for="{{ $id }}" class="rs-pilih">{{ $teks }}</label>
            @endforeach
        </div>

        @if ($isian)
            <div @class(['rs-aak-isian', 'is-terisi' => filled($isianNilai), 'is-selalu' => $isianSaat === null])>
                <label for="{{ $idDasar }}-isian" class="visually-hidden">{{ $label }} (keterangan)</label>
                <input type="text" id="{{ $idDasar }}-isian" name="{{ $isian }}" value="{{ $isianNilai }}"
                       placeholder="{{ $placeholder }}"
                       @class(['form-control', 'is-invalid' => $errors->has($kunciIsian)])>
                @error($kunciIsian)<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        @endif

        @error($kunciErr)<div class="small text-danger mt-1">{{ $message }}</div>@enderror
    </div>
</div>