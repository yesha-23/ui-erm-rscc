<section class="rs-card">
    <h3 class="h6 fw-bold mb-3">Hasil pemeriksaan</h3>
    <div class="row g-3">
        <div class="col-12">
            <label for="keperluan" class="form-label">Keperluan</label>
            <input type="text" id="keperluan" name="keperluan" value="{{ old('keperluan') }}"
                   class="form-control @error('keperluan') is-invalid @enderror" placeholder="mis. melamar pekerjaan, mendaftar sekolah">
            @error('keperluan') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-sm-4">
            <label for="tb" class="form-label">Tinggi badan</label>
            <div class="input-group"><input type="number" id="tb" name="tb" class="form-control" value="{{ old('tb') }}"><span class="input-group-text">cm</span></div>
        </div>
        <div class="col-sm-4">
            <label for="bb" class="form-label">Berat badan</label>
            <div class="input-group"><input type="number" id="bb" name="bb" class="form-control" value="{{ old('bb') }}"><span class="input-group-text">kg</span></div>
        </div>
        <div class="col-sm-4">
            <label for="td" class="form-label">Tekanan darah</label>
            <div class="input-group"><input type="text" id="td" name="td" class="form-control" value="{{ old('td') }}" placeholder="120/80"><span class="input-group-text">mmHg</span></div>
        </div>
        <div class="col-sm-6">
            <label for="kesimpulan" class="form-label">Kesimpulan</label>
            <select id="kesimpulan" name="kesimpulan" class="form-select @error('kesimpulan') is-invalid @enderror">
                <option value="">Pilih kesimpulan</option>
                @foreach (['Sehat / memenuhi syarat', 'Tidak memenuhi syarat'] as $opsi)
                    <option @selected(old('kesimpulan') === $opsi)>{{ $opsi }}</option>
                @endforeach
            </select>
            @error('kesimpulan') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</section>