@php $pilihPenyulit = (array) old('penyulit', $isi['penyulit'] ?? []); @endphp

<form id="formHd" method="POST" action="{{ route('rekam-medis.hd.simpan', [$noReg, 'pasca']) }}"
      class="d-flex flex-column gap-3" novalidate data-rs-hd-form>
    @csrf

    <section class="rs-card" aria-labelledby="judul-pasca-ttv">
        <h3 id="judul-pasca-ttv" class="h6 fw-bold mb-3">Tanda vital & berat badan pasca-HD</h3>
        <div class="rs-hd-grid">
            @include('rekam-medis.hd._angka', ['nama' => 'td_sistolik', 'label' => 'TD sistolik', 'satuan' => 'mmHg'])
            @include('rekam-medis.hd._angka', ['nama' => 'td_diastolik', 'label' => 'TD diastolik', 'satuan' => 'mmHg'])
            @include('rekam-medis.hd._angka', ['nama' => 'nadi', 'label' => 'Nadi', 'satuan' => 'x/menit'])
            @include('rekam-medis.hd._angka', ['nama' => 'bb_post', 'label' => 'BB pasca-HD', 'satuan' => 'kg', 'step' => 0.1, 'data' => 'bb_post'])
            <div>
                <span class="form-label d-block">UF aktual</span>
                <output class="rs-output" data-rs-hd-uf data-bb-pre="{{ $pra['bb_pre'] ?? '' }}" aria-live="polite">—</output>
            </div>
        </div>
        <p class="small rs-text-muted mt-2 mb-0">
            UF aktual = BB pre-HD − BB pasca-HD.
            Target UF: {{ isset($resep['uf_goal']) ? $fmt($resep['uf_goal']) . ' L' : 'belum ada resep' }}.
            @empty($pra['bb_pre']) <strong>Isi Pra-HD dulu agar UF aktual bisa dihitung.</strong> @endempty
        </p>
    </section>

    <section class="rs-card" aria-labelledby="judul-adekuasi">
        <h3 id="judul-adekuasi" class="h6 fw-bold mb-3">Adekuasi dialisis</h3>
        <div class="rs-hd-grid">
            @include('rekam-medis.hd._angka', ['nama' => 'ureum_pre', 'label' => 'Ureum pra-HD', 'satuan' => 'mg/dl', 'step' => 0.1, 'wajib' => false, 'data' => 'ureum_pre'])
            @include('rekam-medis.hd._angka', ['nama' => 'ureum_post', 'label' => 'Ureum pasca-HD', 'satuan' => 'mg/dl', 'step' => 0.1, 'wajib' => false, 'data' => 'ureum_post'])
            <div>
                <span class="form-label d-block">URR</span>
                <output class="rs-output" data-rs-hd-urr aria-live="polite">—</output>
            </div>
            @include('rekam-medis.hd._angka', ['nama' => 'ktv', 'label' => 'Kt/V', 'satuan' => '', 'step' => 0.01, 'wajib' => false])
        </div>
        <p class="small rs-text-muted mt-2 mb-0">URR = (ureum pra − ureum pasca) ÷ ureum pra × 100%. Target ≥ 65%.</p>
    </section>

    <section class="rs-card" aria-labelledby="judul-evaluasi">
        <h3 id="judul-evaluasi" class="h6 fw-bold mb-3">Penyulit & evaluasi</h3>

        <fieldset class="mb-3">
            <legend class="form-label">Penyulit selama HD <span class="rs-text-muted fw-normal">· pilih bila ada</span></legend>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($penyulit as $i => $p)
                    <input type="checkbox" class="btn-check" name="penyulit[]" id="penyulit-{{ $i }}" value="{{ $p }}"
                           @checked(in_array($p, $pilihPenyulit, true))>
                    <label class="btn btn-outline-secondary btn-sm" for="penyulit-{{ $i }}">{{ $p }}</label>
                @endforeach
            </div>
        </fieldset>

        <div class="mb-3">
            <label for="hd-evaluasi" class="form-label">Evaluasi <span class="text-danger">*</span></label>
            <textarea id="hd-evaluasi" name="evaluasi" rows="3" @class(['form-control', 'is-invalid' => $errors->has('evaluasi')])
                      placeholder="mis. HD 4,5 jam berjalan lancar, UF tercapai, KU sedang, pasien pulang">{{ $v('evaluasi') }}</textarea>
            @error('evaluasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="hd-discharge" class="form-label">Discharge planning</label>
            <textarea id="hd-discharge" name="discharge" rows="2" class="form-control"
                      placeholder="mis. batasi minum 600 ml/hari, diet rendah garam & kalium">{{ $v('discharge') }}</textarea>
        </div>

        <div style="max-width: 240px">
            <label for="hd-jadwal" class="form-label">Jadwal HD berikutnya</label>
            <input type="date" id="hd-jadwal" name="jadwal_berikut" min="{{ now()->addDay()->toDateString() }}"
                   value="{{ $v('jadwal_berikut', now()->addDays(3)->toDateString()) }}"
                   @class(['form-control', 'is-invalid' => $errors->has('jadwal_berikut')])>
            @error('jadwal_berikut') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </section>
</form>