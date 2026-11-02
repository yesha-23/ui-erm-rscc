<section class="rs-card">
    <h3 class="h6 fw-bold mb-3">Isi rujukan</h3>
    <div class="row g-3">
        <div class="col-sm-6">
            <label for="tujuan" class="form-label">Dirujuk ke</label>
            <input type="text" id="tujuan" name="tujuan" value="{{ old('tujuan') }}"
                   class="form-control @error('tujuan') is-invalid @enderror" placeholder="Nama RS / faskes / unit">
            @error('tujuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-sm-6">
            <label for="poli" class="form-label">Poli / spesialis tujuan</label>
            <input type="text" id="poli" name="poli" value="{{ old('poli') }}" class="form-control">
        </div>
        <div class="col-sm-6">
            <label for="alasan" class="form-label">Alasan rujukan</label>
            <select id="alasan" name="alasan" class="form-select @error('alasan') is-invalid @enderror">
                <option value="">Pilih alasan</option>
                @foreach (['Butuh pemeriksaan / penanganan lanjutan', 'Sarana atau tenaga tidak tersedia', 'Permintaan pasien / keluarga', 'Kontrol ke faskes asal'] as $opsi)
                    <option @selected(old('alasan') === $opsi)>{{ $opsi }}</option>
                @endforeach
            </select>
            @error('alasan') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-sm-6">
            <label for="diagnosa" class="form-label">Diagnosa</label>
            <input type="text" id="diagnosa" name="diagnosa" class="form-control" value="{{ old('diagnosa', $pasien['diagnosa_kerja']) }}">
        </div>
        <div class="col-12">
            <label for="terapi" class="form-label">Terapi / tindakan yang sudah diberikan</label>
            <textarea id="terapi" name="terapi" rows="3" class="form-control">{{ old('terapi') }}</textarea>
        </div>
    </div>
</section>