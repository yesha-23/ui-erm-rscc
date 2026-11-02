@props(['chips' => [], 'pilihan' => [], 'dari', 'sampai'])

<form method="GET" class="rs-card rs-filter mb-3" role="search">
    @if (request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif

    {{-- Baris utama: cari, tanggal, tombol filter --}}
    <div class="d-flex flex-wrap align-items-center gap-2">
        <div class="input-group rs-filter-cari">
            <span class="input-group-text rs-search-icon"><i class="bi bi-search" aria-hidden="true"></i></span>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control rs-search-input"
                   placeholder="Cari nama / No. RM" aria-label="Cari pasien">
        </div>

        <div class="d-flex align-items-center gap-2">
            <input type="date" name="dari" value="{{ $dari }}" class="form-control rs-filter-tanggal" aria-label="Dari tanggal">
            <span class="rs-text-muted" aria-hidden="true">–</span>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-control rs-filter-tanggal" aria-label="Sampai tanggal">
        </div>

        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse"
                data-bs-target="#filterLanjutan" aria-expanded="false" aria-controls="filterLanjutan">
            <i class="bi bi-funnel me-1" aria-hidden="true"></i>Filter
            @if (count($chips))
                <span class="rs-badge-count ms-1">{{ count($chips) }}</span>
            @endif
        </button>

        <button type="submit" class="btn btn-primary">Terapkan</button>
    </div>

    {{-- Filter lanjutan (tersembunyi sampai tombol Filter diklik) --}}
    <div class="collapse" id="filterLanjutan">
        <div class="row g-3 pt-3">
            <div class="col-sm-6 col-lg-3">
                <label for="f-bayar" class="form-label">Status bayar</label>
                <select id="f-bayar" name="bayar" class="form-select">
                    <option value="">Semua</option>
                    <option value="belum" @selected(request('bayar') === 'belum')>Belum terbayar</option>
                    <option value="sudah" @selected(request('bayar') === 'sudah')>Sudah terbayar</option>
                </select>
            </div>

            @if ($pilihan['unit'])
                <div class="col-sm-6 col-lg-3">
                    <label for="f-unit" class="form-label">Unit tujuan</label>
                    <select id="f-unit" name="unit" class="form-select">
                        <option value="">Semua unit</option>
                        @foreach ($pilihan['unit'] as $opsi)
                            <option value="{{ $opsi }}" @selected(request('unit') === $opsi)>{{ $opsi }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if ($pilihan['ppa'])
                <div class="col-sm-6 col-lg-3">
                    <label for="f-ppa" class="form-label">Dokter / PPA</label>
                    <select id="f-ppa" name="ppa" class="form-select">
                        <option value="">Semua PPA</option>
                        @foreach ($pilihan['ppa'] as $opsi)
                            <option value="{{ $opsi }}" @selected(request('ppa') === $opsi)>{{ $opsi }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    {{-- Chip filter aktif --}}
    @if (count($chips))
        <div class="d-flex flex-wrap align-items-center gap-2 pt-3">
            @foreach ($chips as $chip)
                <a href="{{ $chip['hapus'] }}" class="rs-chip">
                    {{ $chip['label'] }}
                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                    <span class="visually-hidden">(hapus filter)</span>
                </a>
            @endforeach
            <a href="{{ url()->current() }}" class="rs-chip-reset">Hapus semua</a>
        </div>
    @endif
</form>