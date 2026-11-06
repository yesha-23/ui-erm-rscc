{{-- Isian angka + satuan.
     Param: $nama, $label, $satuan; opsional: $nilai, $step (bawaan 1), $wajib (bawaan true), $data (data-rs-hd) --}}
<div>
    <label for="hd-{{ $nama }}" class="form-label">
        {{ $label }} @if ($wajib ?? true)<span class="text-danger">*</span>@endif
    </label>
    <div class="input-group">
        <input type="number" id="hd-{{ $nama }}" name="{{ $nama }}" value="{{ $nilai ?? $v($nama) }}"
               step="{{ $step ?? 1 }}" inputmode="decimal"
               @isset($data) data-rs-hd="{{ $data }}" @endisset
               @class(['form-control', 'is-invalid' => $errors->has($nama)])>
        <span class="input-group-text">{{ $satuan }}</span>
    </div>
    @error($nama) <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>