<section class="rs-card">
    <h3 class="h6 fw-bold mb-3">Isi surat</h3>
    <div class="row g-3" data-rs-hitung-hari>
        <div class="col-sm-4">
            <label for="mulai" class="form-label">Istirahat mulai</label>
            <input type="date" id="mulai" name="mulai" data-rs-mulai
                   value="{{ old('mulai', now()->toDateString()) }}"
                   class="form-control @error('mulai') is-invalid @enderror">
            @error('mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-sm-4">
            <label for="sampai" class="form-label">Sampai dengan</label>
            <input type="date" id="sampai" name="sampai" data-rs-sampai
                   value="{{ old('sampai', now()->addDays(2)->toDateString()) }}"
                   class="form-control @error('sampai') is-invalid @enderror">
            @error('sampai') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-sm-4">
            <span class="form-label d-block">Lama istirahat</span>
            <output class="rs-output" data-rs-lama aria-live="polite">3 hari</output>
        </div>
        <div class="col-12">
            <label for="diagnosa" class="form-label">Diagnosa</label>
            <input type="text" id="diagnosa" name="diagnosa" class="form-control"
                   value="{{ old('diagnosa', $pasien['diagnosa_kerja']) }}">
            <div class="form-text">Kosongkan bila pasien tidak ingin diagnosa dicantumkan.</div>
        </div>
        <div class="col-12">
            <label for="keterangan" class="form-label">Keterangan tambahan</label>
            <textarea id="keterangan" name="keterangan" rows="3" class="form-control"
                      placeholder="mis. perlu kontrol ulang setelah 3 hari">{{ old('keterangan') }}</textarea>
        </div>
    </div>
</section>