{{-- Skala nyeri numerik 0–10. Detail (lokasi, sifat, frekuensi) hanya tampil bila > 0 (CSS :has). --}}
@props(['opsi', 'isi' => []])

@php $nilai = (string) old('nyeri', $isi['nyeri'] ?? '0'); @endphp

<div data-rs-nyeri>
    <fieldset class="rs-pilihan-grup">
        <legend class="rs-legend">Skala nyeri (0–10) <span class="text-danger" aria-hidden="true">*</span></legend>
        <div class="rs-nyeri">
            @for ($n = 0; $n <= 10; $n++)
                <input type="radio" class="btn-check" name="nyeri" id="nyeri-{{ $n }}" value="{{ $n }}"
                       autocomplete="off" @checked($nilai === (string) $n)>
                <label for="nyeri-{{ $n }}" class="rs-nyeri-angka">{{ $n }}</label>
            @endfor
        </div>
        <div class="rs-nyeri-skala small rs-text-muted" aria-hidden="true">
            <span>Tidak nyeri</span><span>Ringan</span><span>Sedang</span><span>Berat</span>
        </div>
    </fieldset>
    <p class="small fw-semibold mt-2 mb-0" data-rs-nyeri-label aria-live="polite"></p>

    <div class="rs-nyeri-detail">
        <div class="row g-3">
            <div class="col-md-5">
                <label for="nyeri_lokasi" class="form-label">Lokasi nyeri</label>
                <input type="text" id="nyeri_lokasi" name="nyeri_lokasi" class="form-control"
                       value="{{ old('nyeri_lokasi', $isi['nyeri_lokasi'] ?? '') }}" placeholder="mis. ulu hati">
            </div>
            <div class="col-md-7">
                <x-form.pilihan name="nyeri_sifat" label="Sifat nyeri" :opsi="$opsi['nyeri_sifat']"
                                :nilai="old('nyeri_sifat', $isi['nyeri_sifat'] ?? null)" />
            </div>
            <div class="col-12">
                <x-form.pilihan name="nyeri_waktu" label="Frekuensi" :opsi="$opsi['nyeri_waktu']"
                                :nilai="old('nyeri_waktu', $isi['nyeri_waktu'] ?? null)" />
            </div>
        </div>
    </div>
</div>