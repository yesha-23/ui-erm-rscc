@props(['pasien'])

@php
    $galat  = $errors->alergi;
    $status = old('status', $pasien['alergi_status']);
    $ket    = old('keterangan', $pasien['alergi_status'] === 'ada' ? $pasien['alergi'] : '');
@endphp

<div class="modal fade" id="modalAlergi" tabindex="-1" aria-labelledby="judulModalAlergi" aria-hidden="true"
     @if ($galat->any()) data-rs-buka-otomatis @endif>
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('rekam-medis.alergi', $pasien['no_reg']) }}"
              class="modal-content rs-modal" novalidate data-rs-form-alergi>
            @csrf
            <div class="modal-header">
                <h2 class="modal-title h5 fw-bold" id="judulModalAlergi">Riwayat alergi</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                <p class="small rs-text-muted">
                    Berlaku untuk semua kunjungan pasien ini (RM {{ $pasien['no_rm'] }}).
                    @if ($pasien['alergi_waktu']) Terakhir diperbarui {{ $pasien['alergi_waktu'] }}. @endif
                </p>

                <fieldset class="mb-3">
                    <legend class="form-label">Apakah pasien punya alergi? <span class="text-danger">*</span></legend>
                    <div class="d-flex flex-wrap gap-2">
                        <input type="radio" class="btn-check" name="status" id="alergi-tidak" value="tidak" @checked($status === 'tidak')>
                        <label class="btn btn-outline-secondary" for="alergi-tidak">Tidak ada alergi</label>
                        <input type="radio" class="btn-check" name="status" id="alergi-ada" value="ada" @checked($status === 'ada')>
                        <label class="btn btn-outline-secondary" for="alergi-ada">Ada alergi</label>
                    </div>
                    @if ($galat->has('status')) <div class="invalid-feedback d-block">{{ $galat->first('status') }}</div> @endif
                </fieldset>

                <div data-rs-alergi-detail @if ($status !== 'ada') hidden @endif>
                    <label for="alergi-ket" class="form-label">Alergi terhadap <span class="text-danger">*</span></label>
                    <textarea id="alergi-ket" name="keterangan" rows="3" maxlength="200"
                              @class(['form-control', 'is-invalid' => $galat->has('keterangan')])
                              placeholder="mis. Amoksisilin (ruam), udang (gatal)">{{ $ket }}</textarea>
                    @if ($galat->has('keterangan')) <div class="invalid-feedback">{{ $galat->first('keterangan') }}</div> @endif
                    <div class="form-text">Tuliskan nama obat/makanan/bahan dan reaksinya.</div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>