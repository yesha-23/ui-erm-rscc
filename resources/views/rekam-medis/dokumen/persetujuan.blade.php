<section class="rs-card">
    <h3 class="h6 fw-bold mb-3">Pernyataan</h3>
    <p class="rs-doc-text">
        Saya yang bertanda tangan di bawah ini menyatakan telah menerima penjelasan yang cukup mengenai
        <strong>{{ mb_strtolower($dokumen['nama']) }}</strong>, termasuk tujuan, manfaat, risiko, dan alternatifnya,
        serta telah diberi kesempatan untuk bertanya. Dengan ini saya menyatakan sesuai pilihan di bawah.
    </p>

    <div class="row g-3">
        <div class="col-12">
            <span class="form-label d-block" id="label-menyatakan">Yang menyatakan</span>
            <div class="btn-group rs-segmented flex-wrap" role="group" aria-labelledby="label-menyatakan">
                @foreach (['Pasien sendiri', 'Keluarga', 'Wali'] as $i => $opsi)
                    <input type="radio" class="btn-check" name="menyatakan" id="menyatakan-{{ $i }}"
                           value="{{ $opsi }}" @checked(old('menyatakan') === $opsi)>
                    <label class="btn btn-outline-secondary" for="menyatakan-{{ $i }}">{{ $opsi }}</label>
                @endforeach
            </div>
            @error('menyatakan') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="col-sm-6">
            <label for="nama_wali" class="form-label">Nama keluarga / wali <span class="rs-text-muted fw-normal">(jika diwakilkan)</span></label>
            <input type="text" id="nama_wali" name="nama_wali" class="form-control" value="{{ old('nama_wali') }}">
        </div>
        <div class="col-sm-6">
            <label for="hubungan" class="form-label">Hubungan dengan pasien</label>
            <input type="text" id="hubungan" name="hubungan" class="form-control" value="{{ old('hubungan') }}" placeholder="mis. orang tua, suami/istri">
        </div>
        <div class="col-12">
            <div class="form-check rs-check-box @error('setuju') is-invalid @enderror">
                <input class="form-check-input @error('setuju') is-invalid @enderror" type="checkbox" id="setuju" name="setuju" value="1" @checked(old('setuju'))>
                <label class="form-check-label" for="setuju">Saya telah membaca, memahami, dan menyetujui pernyataan di atas.</label>
                @error('setuju') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</section>