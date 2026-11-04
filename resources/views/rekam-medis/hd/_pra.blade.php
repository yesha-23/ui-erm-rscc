<form id="formHd" method="POST" action="{{ route('rekam-medis.hd.simpan', [$noReg, 'pra']) }}"
      class="d-flex flex-column gap-3" novalidate data-rs-hd-form>
    @csrf

    <section class="rs-card" aria-labelledby="judul-pra-ttv">
        <h3 id="judul-pra-ttv" class="h6 fw-bold mb-3">Keadaan & tanda vital pra-HD</h3>

        <div class="mb-3">
            <label for="hd-keluhan" class="form-label">Keluhan</label>
            <textarea id="hd-keluhan" name="keluhan" rows="2" class="form-control"
                      placeholder="mis. sesak saat berbaring, kaki bengkak — kosongkan bila tidak ada">{{ $v('keluhan') }}</textarea>
        </div>

        <fieldset class="mb-3">
            <legend class="form-label">Keadaan umum <span class="text-danger">*</span></legend>
            <div class="d-flex flex-wrap gap-2">
                @foreach (['baik' => 'Baik', 'sedang' => 'Sedang', 'buruk' => 'Buruk'] as $k => $label)
                    <input type="radio" class="btn-check" name="keadaan_umum" id="ku-{{ $k }}" value="{{ $k }}" @checked($v('keadaan_umum') === $k)>
                    <label class="btn btn-outline-secondary" for="ku-{{ $k }}">{{ $label }}</label>
                @endforeach
            </div>
            @error('keadaan_umum') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </fieldset>

        <div class="rs-hd-grid">
            @include('rekam-medis.hd._angka', ['nama' => 'td_sistolik', 'label' => 'TD sistolik', 'satuan' => 'mmHg'])
            @include('rekam-medis.hd._angka', ['nama' => 'td_diastolik', 'label' => 'TD diastolik', 'satuan' => 'mmHg'])
            @include('rekam-medis.hd._angka', ['nama' => 'nadi', 'label' => 'Nadi', 'satuan' => 'x/menit'])
            @include('rekam-medis.hd._angka', ['nama' => 'napas', 'label' => 'Napas', 'satuan' => 'x/menit'])
            @include('rekam-medis.hd._angka', ['nama' => 'suhu', 'label' => 'Suhu', 'satuan' => '°C', 'step' => 0.1])
        </div>
    </section>

    <section class="rs-card" aria-labelledby="judul-pra-bb">
        <h3 id="judul-pra-bb" class="h6 fw-bold mb-3">Berat badan & akses vaskular</h3>

        <div class="rs-hd-grid">
            @include('rekam-medis.hd._angka', ['nama' => 'bb_pre', 'label' => 'BB pre-HD', 'satuan' => 'kg', 'step' => 0.1, 'data' => 'bb_pre'])
            @include('rekam-medis.hd._angka', ['nama' => 'bb_kering', 'label' => 'BB kering', 'satuan' => 'kg', 'step' => 0.1,
                                               'data' => 'bb_kering', 'nilai' => $v('bb_kering', $lalu['bb_kering'])])
            <div>
                <span class="form-label d-block">Kenaikan BB antar-sesi</span>
                <output class="rs-output" data-rs-hd-idwg aria-live="polite">—</output>
            </div>
        </div>
        <p class="small rs-text-muted mt-2 mb-3">BB kering diambil dari sesi sebelumnya. Ubah bila ada penetapan baru dari dokter.</p>

        <fieldset>
            <legend class="form-label">Akses vaskular <span class="text-danger">*</span></legend>
            <div class="d-flex flex-wrap gap-2">
                @foreach (['avf' => 'AV-fistula', 'cdl' => 'Kateter HD (CDL)', 'femoral' => 'Kateter femoral'] as $k => $label)
                    <input type="radio" class="btn-check" name="akses" id="akses-{{ $k }}" value="{{ $k }}" @checked($v('akses') === $k)>
                    <label class="btn btn-outline-secondary" for="akses-{{ $k }}">{{ $label }}</label>
                @endforeach
            </div>
            @error('akses') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </fieldset>
    </section>

    <section class="rs-card" aria-labelledby="judul-morse" data-rs-morse>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h3 id="judul-morse" class="h6 fw-bold mb-0">Risiko jatuh (skala Morse)</h3>
            <span class="rs-pill-count" data-rs-morse-hasil aria-live="polite">Skor 0 · Tidak berisiko</span>
        </div>

        @foreach ($morse as $k => [$tanya, $opsi])
            <fieldset class="rs-hd-morse">
                <legend>{{ $loop->iteration }}. {{ $tanya }}</legend>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($opsi as $teks => $skor)
                        <input type="radio" class="btn-check" name="jatuh[{{ $k }}]" id="jatuh-{{ $k }}-{{ $skor }}" value="{{ $skor }}"
                               @checked((string) old("jatuh.$k", $isi['jatuh'][$k] ?? '') === (string) $skor)>
                        <label class="btn btn-outline-secondary btn-sm" for="jatuh-{{ $k }}-{{ $skor }}">
                            {{ $teks }} <span class="rs-hd-skor">{{ $skor }}</span>
                        </label>
                    @endforeach
                </div>
            </fieldset>
        @endforeach
        <p class="small rs-text-muted mb-0 mt-2">0–24 tidak berisiko · 25–44 risiko rendah · ≥ 45 risiko tinggi.</p>
        @if ($errors->first('jatuh') ?: $errors->first('jatuh.*'))
            <div class="invalid-feedback d-block">{{ $errors->first('jatuh') ?: $errors->first('jatuh.*') }}</div>
        @endif
    </section>
</form>