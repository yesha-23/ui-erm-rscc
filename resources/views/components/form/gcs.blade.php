{{-- Glasgow Coma Scale: E (1–4) + V (1–5) + M (1–6). Total & kesadaran dihitung JS. --}}
@props(['opsi', 'isi' => []])

<div class="rs-gcs" data-rs-gcs>
    <div class="row g-2">
        @foreach (['gcs_e' => 'Mata (E)', 'gcs_v' => 'Verbal (V)', 'gcs_m' => 'Motorik (M)'] as $name => $label)
            <div class="col-md-4">
                <label for="{{ $name }}" class="form-label small fw-semibold">
                    {{ $label }} <span class="text-danger" aria-hidden="true">*</span>
                </label>
                <select id="{{ $name }}" name="{{ $name }}" data-rs-gcs-nilai
                        @class(['form-select', 'is-invalid' => $errors->has($name)])>
                    <option value="">Pilih…</option>
                    @foreach ($opsi[$name] as $skor => $teks)
                        <option value="{{ $skor }}" @selected((string) old($name, $isi[$name] ?? '') === (string) $skor)>
                            {{ $skor }} · {{ $teks }}
                        </option>
                    @endforeach
                </select>
                @error($name)
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @endforeach
    </div>

    <div class="rs-gcs-total" aria-live="polite">
        <span>Total GCS</span>
        <strong data-rs-gcs-total>–</strong>
        <span data-rs-gcs-kesadaran>Lengkapi E, V, dan M</span>
    </div>
</div>