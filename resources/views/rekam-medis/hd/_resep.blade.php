<form id="formHd" method="POST" action="{{ route('rekam-medis.hd.simpan', [$noReg, 'resep']) }}"
      class="d-flex flex-column gap-3" novalidate data-rs-hd-form>
    @csrf

    <section class="rs-card" aria-labelledby="judul-resep">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
            <div>
                <h3 id="judul-resep" class="h6 fw-bold mb-1">Resep hemodialisa</h3>
                <p class="small rs-text-muted mb-0">Pasien rutin: salin resep sesi sebelumnya, lalu sesuaikan.</p>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm" data-rs-salin-resep="{{ json_encode($lalu['resep']) }}">
                <i class="bi bi-copy me-1" aria-hidden="true"></i>Salin dari {{ $lalu['tanggal'] }}
            </button>
        </div>
        <p class="alert alert-info small py-2 px-3" role="status" hidden data-rs-salin-info></p>

        <fieldset class="mb-3">
            <legend class="form-label">Jenis HD <span class="text-danger">*</span></legend>
            <div class="d-flex flex-wrap gap-2">
                @foreach (['inisiasi' => 'Inisiasi', 'rutin' => 'Rutin', 'akut' => 'Akut', 'sled' => 'SLED'] as $k => $label)
                    <input type="radio" class="btn-check" name="jenis" id="jenis-{{ $k }}" value="{{ $k }}" @checked($v('jenis') === $k)>
                    <label class="btn btn-outline-secondary" for="jenis-{{ $k }}">{{ $label }}</label>
                @endforeach
            </div>
            @error('jenis') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </fieldset>

        <div class="rs-hd-grid mb-3">
            @include('rekam-medis.hd._angka', ['nama' => 'durasi', 'label' => 'Durasi', 'satuan' => 'jam', 'step' => 0.5])
            @include('rekam-medis.hd._angka', ['nama' => 'qb', 'label' => 'QB', 'satuan' => 'ml/menit'])
            @include('rekam-medis.hd._angka', ['nama' => 'qd', 'label' => 'QD', 'satuan' => 'ml/menit'])
            @include('rekam-medis.hd._angka', ['nama' => 'uf_goal', 'label' => 'UF goal', 'satuan' => 'L', 'step' => 0.1,
                                               'nilai' => $v('uf_goal', $saranUf ?? '')])
        </div>
        @if ($saranUf !== null)
            <p class="small rs-text-muted mt-n2 mb-3">
                <i class="bi bi-lightbulb me-1" aria-hidden="true"></i>Saran UF goal dari kenaikan BB: {{ $fmt($saranUf) }} L.
            </p>
        @endif

        <div class="row g-3">
            <fieldset class="col-md-6">
                <legend class="form-label">Dialisat <span class="text-danger">*</span></legend>
                <div class="d-flex flex-wrap gap-2">
                    @foreach (['bikarbonat' => 'Bikarbonat', 'asetat' => 'Asetat'] as $k => $label)
                        <input type="radio" class="btn-check" name="dialisat" id="dialisat-{{ $k }}" value="{{ $k }}" @checked($v('dialisat') === $k)>
                        <label class="btn btn-outline-secondary" for="dialisat-{{ $k }}">{{ $label }}</label>
                    @endforeach
                </div>
                @error('dialisat') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </fieldset>
            <fieldset class="col-md-6">
                <legend class="form-label">Metode heparin <span class="text-danger">*</span></legend>
                <div class="d-flex flex-wrap gap-2">
                    @foreach (['reguler' => 'Reguler', 'minimal' => 'Minimal', 'bebas' => 'Bebas heparin'] as $k => $label)
                        <input type="radio" class="btn-check" name="heparin" id="heparin-{{ $k }}" value="{{ $k }}" @checked($v('heparin') === $k)>
                        <label class="btn btn-outline-secondary" for="heparin-{{ $k }}">{{ $label }}</label>
                    @endforeach
                </div>
                @error('heparin') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </fieldset>
        </div>

        <div class="rs-hd-grid mt-3">
            @include('rekam-medis.hd._angka', ['nama' => 'heparin_awal', 'label' => 'Dosis awal heparin', 'satuan' => 'IU', 'wajib' => false])
            @include('rekam-medis.hd._angka', ['nama' => 'heparin_lanjut', 'label' => 'Heparin lanjutan', 'satuan' => 'IU/jam', 'wajib' => false])
        </div>
    </section>
</form>