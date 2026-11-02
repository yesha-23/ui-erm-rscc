<section class="rs-card">
    <h3 class="h6 fw-bold mb-1">Isi dokumen</h3>
    <p class="small rs-text-muted mb-3">Template umum. Tata letak khusus untuk dokumen ini akan disesuaikan oleh IT RSCC.</p>
    <label for="isi" class="visually-hidden">Isi dokumen</label>
    <textarea id="isi" name="isi" rows="8" class="form-control @error('isi') is-invalid @enderror"
              placeholder="Tulis isi dokumen…">{{ old('isi') }}</textarea>
    @error('isi') <div class="invalid-feedback">{{ $message }}</div> @enderror
</section>