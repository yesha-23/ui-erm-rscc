{{--
  Daftar centang (checkbox) bersusun kolom seperti di ERM lama, untuk pilihan berteks panjang
  (diagnosis keperawatan, intervensi, edukasi). Pilihan "lainnya" membuka kotak isian.
  <x-form.centang name="diagnosa" :opsi="$opsi['diagnosa']" :nilai="..." isian="diagnosa_lain" :isian-nilai="..." />
--}}
@props([
    'name',
    'opsi',
    'nilai'       => [],
    'isian'       => null,
    'isianNilai'  => null,
    'isianSaat'   => 'lainnya',
    'placeholder' => 'Jika ada, sebutkan…',
    'label'       => null,
])

@php
    $terpilih  = collect((array) $nilai)->map(fn ($v) => (string) $v)->all();
    $idDasar   = 'f-' . trim(str_replace(['[', ']', '.'], '-', $name), '-');
@endphp

<div {{ $attributes->class(['rs-aak-centang-wadah']) }} role="group"
     @if ($label) aria-label="{{ $label }}" @endif>
    <div class="rs-aak-centang">
        @foreach ($opsi as $kode => $teks)
            @php $id = $idDasar . '-' . \Illuminate\Support\Str::slug((string) $kode); @endphp
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="{{ $id }}" name="{{ $name }}[]" value="{{ $kode }}"
                       @checked(in_array((string) $kode, $terpilih, true))
                       @if ($isian && (string) $kode === (string) $isianSaat) data-rs-buka-isian @endif>
                <label class="form-check-label" for="{{ $id }}">{{ $teks }}</label>
            </div>
        @endforeach
    </div>

    @if ($isian)
        <div @class(['rs-aak-isian', 'is-terisi' => filled($isianNilai)])>
            <label for="{{ $idDasar }}-isian" class="visually-hidden">{{ $label ?? 'Lainnya' }}: sebutkan</label>
            <input type="text" id="{{ $idDasar }}-isian" name="{{ $isian }}" value="{{ $isianNilai }}"
                   placeholder="{{ $placeholder }}" @class(['form-control', 'is-invalid' => $errors->has($isian)])>
            @error($isian)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endif

    @error($name)<div class="small text-danger mt-1">{{ $message }}</div>@enderror
</div>