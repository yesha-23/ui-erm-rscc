@php $totalIntake = collect($intra)->sum(fn ($o) => (int) ($o['intake'] ?? 0)); @endphp

<section class="rs-card p-0 overflow-hidden mb-3" aria-labelledby="judul-intra">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 pb-2">
        <h3 id="judul-intra" class="h6 fw-bold mb-0">Observasi selama HD</h3>
        <span class="small rs-text-muted">
            Target: QB {{ $resep['qb'] ?? '—' }} ml/menit · UF {{ isset($resep['uf_goal']) ? $fmt($resep['uf_goal']) : '—' }} L
            · {{ isset($resep['durasi']) ? $fmt($resep['durasi']) : '—' }} jam · observasi tiap 1 jam
        </span>
    </div>

    @if (count($intra))
        <div class="table-responsive">
            <table class="table rs-table rs-table-kartu rs-hd-tabel mb-0">
                <thead>
                    <tr>
                        <th scope="col">Jam</th><th scope="col">QB</th><th scope="col">UF rate</th><th scope="col">TD</th>
                        <th scope="col">Nadi</th><th scope="col">Suhu</th><th scope="col">Intake</th><th scope="col">Catatan</th>
                        <th scope="col"><span class="visually-hidden">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($intra as $i => $o)
                        <tr>
                            <td class="rs-td-utama fw-bold">{{ $o['jam'] }}</td>
                            <td data-label="QB">{{ $o['qb'] }} ml/menit</td>
                            <td data-label="UF rate">{{ filled($o['uf_rate'] ?? null) ? $fmt($o['uf_rate'], 2) . ' L/jam' : '—' }}</td>
                            <td data-label="TD">{{ $o['td_sistolik'] }}/{{ $o['td_diastolik'] }} mmHg</td>
                            <td data-label="Nadi">{{ $o['nadi'] }} x/menit</td>
                            <td data-label="Suhu">{{ filled($o['suhu'] ?? null) ? $fmt($o['suhu']) . ' °C' : '—' }}</td>
                            <td data-label="Intake">{{ filled($o['intake'] ?? null) ? $o['intake'] . ' ml' : '—' }}</td>
                            <td data-label="Catatan">{{ $o['catatan'] ?: '—' }}</td>
                            <td class="rs-td-aksi text-end">
                                <form method="POST" action="{{ route('rekam-medis.hd.intra.hapus', [$noReg, $i]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-secondary btn-sm" aria-label="Hapus observasi jam {{ $o['jam'] }}">
                                        <i class="bi bi-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="rs-table-foot">
            <span class="small rs-text-muted">{{ count($intra) }} observasi</span>
            <span class="small fw-semibold">Total intake: {{ $totalIntake }} ml</span>
        </div>
    @else
        <div class="rs-empty">
            <span class="rs-empty-icon"><i class="bi bi-activity" aria-hidden="true"></i></span>
            <h4 class="h6 fw-bold mb-1">Belum ada observasi</h4>
            <p class="rs-text-muted mb-0">Tambahkan observasi pertama saat HD dimulai.</p>
        </div>
    @endif
</section>

<section class="rs-card" aria-labelledby="judul-tambah-obs">
    <h3 id="judul-tambah-obs" class="h6 fw-bold mb-3">Tambah observasi</h3>
    <form method="POST" action="{{ route('rekam-medis.hd.simpan', [$noReg, 'intra']) }}" novalidate class="rs-hd-grid">
        @csrf
        <div>
            <label for="hd-jam" class="form-label">Jam <span class="text-danger">*</span></label>
            <input type="time" id="hd-jam" name="jam" value="{{ old('jam', now()->format('H:i')) }}"
                   @class(['form-control', 'is-invalid' => $errors->has('jam')])>
            @error('jam') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        @include('rekam-medis.hd._angka', ['nama' => 'qb', 'label' => 'QB', 'satuan' => 'ml/menit', 'nilai' => old('qb', $resep['qb'] ?? '')])
        @include('rekam-medis.hd._angka', ['nama' => 'uf_rate', 'label' => 'UF rate', 'satuan' => 'L/jam', 'step' => 0.01, 'wajib' => false])
        @include('rekam-medis.hd._angka', ['nama' => 'td_sistolik', 'label' => 'TD sistolik', 'satuan' => 'mmHg'])
        @include('rekam-medis.hd._angka', ['nama' => 'td_diastolik', 'label' => 'TD diastolik', 'satuan' => 'mmHg'])
        @include('rekam-medis.hd._angka', ['nama' => 'nadi', 'label' => 'Nadi', 'satuan' => 'x/menit'])
        @include('rekam-medis.hd._angka', ['nama' => 'suhu', 'label' => 'Suhu', 'satuan' => '°C', 'step' => 0.1, 'wajib' => false])
        @include('rekam-medis.hd._angka', ['nama' => 'intake', 'label' => 'Intake cairan', 'satuan' => 'ml', 'wajib' => false])
        <div class="rs-hd-catatan">
            <label for="hd-catatan" class="form-label">Catatan</label>
            <input type="text" id="hd-catatan" name="catatan" value="{{ old('catatan') }}" class="form-control"
                   placeholder="mis. tidak ada keluhan, kram ringan">
        </div>
        <div class="d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah observasi
            </button>
        </div>
    </form>
</section>

<div class="d-flex justify-content-end mt-3">
    <a href="{{ route('rekam-medis.hd', [$noReg, 'pasca']) }}" class="btn btn-outline-secondary">
        Selesai HD, lanjut ke Pasca-HD <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
    </a>
</div>