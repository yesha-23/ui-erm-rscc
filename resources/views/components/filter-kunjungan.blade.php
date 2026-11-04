@props(['pilihan' => [], 'nilai' => [], 'jumlahFilter' => 0, 'dari', 'sampai'])

<form method="GET" class="rs-card rs-filter mb-3" role="search" aria-label="Filter kunjungan">
    @if (request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif

    {{-- Satu baris. Layar sempit: turun baris otomatis. HP: filter selain cari & tanggal dilipat. --}}
    <div class="rs-filter-baris">
        <div class="rs-filter-item rs-filter-cari">
            <span class="form-label d-block" aria-hidden="true">Cari pasien</span>
            <x-search label="Cari pasien" name="q" :value="$nilai['q'] ?? ''"
                placeholder="Nama / No. RM / No. Reg" />
        </div>

        <div class="rs-filter-item" role="group" aria-labelledby="judul-tgl-daftar">
            <span class="form-label d-block" id="judul-tgl-daftar">Tanggal daftar</span>
            {{-- Satu kotak menyatu: Dari │ tanggal │ Sampai │ tanggal --}}
            <div class="input-group rs-rentang-tanggal" data-rs-rentang-tanggal>
                <label class="input-group-text" for="f-dari">Dari</label>
                <input type="date" id="f-dari" name="dari" value="{{ $dari }}" max="{{ $sampai }}"
                    class="form-control" data-rs-tgl-dari>
                <label class="input-group-text" for="f-sampai">Sampai</label>
                <input type="date" id="f-sampai" name="sampai" value="{{ $sampai }}" min="{{ $dari }}"
                    class="form-control" data-rs-tgl-sampai>
            </div>
        </div>

        {{-- Desktop: isi bagian ini ikut berjajar di baris yang sama (CSS display: contents) --}}
        <div class="collapse rs-filter-lain" id="filterLain">
            <div class="rs-filter-lain-isi">
                <div class="rs-filter-item rs-filter-pilih">
                    <label for="f-bayar" class="form-label">Status bayar</label>
                    <select id="f-bayar" name="bayar" class="form-select">
                        @foreach ($pilihan['bayar'] as $kunci => $label)
                            <option value="{{ $kunci }}" @selected(($nilai['bayar'] ?? 'belum') === $kunci)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($pilihan['unit'])
                    <div class="rs-filter-item rs-filter-pilih rs-filter-pilih-lebar">
                        <label for="f-unit" class="form-label">Unit tujuan</label>
                        <select id="f-unit" name="unit" class="form-select">
                            <option value="">Semua unit</option>
                            @foreach ($pilihan['unit'] as $opsi)
                                <option value="{{ $opsi }}" @selected(($nilai['unit'] ?? '') === $opsi)>{{ $opsi }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if ($pilihan['bangsal'])
                    <div class="rs-filter-item rs-filter-pilih">
                        <label for="f-bangsal" class="form-label">Ruang</label>
                        <select id="f-bangsal" name="bangsal" class="form-select">
                            <option value="">Semua ruang</option>
                            @foreach ($pilihan['bangsal'] as $opsi)
                                <option value="{{ $opsi }}" @selected(($nilai['bangsal'] ?? '') === $opsi)>{{ $opsi }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if ($pilihan['ppa'])
                    <div class="rs-filter-item rs-filter-pilih rs-filter-pilih-lebar">
                        <label for="f-ppa" class="form-label">Dokter / PPA</label>
                        <select id="f-ppa" name="ppa" class="form-select">
                            <option value="">Semua dokter/PPA</option>
                            @foreach ($pilihan['ppa'] as $opsi)
                                <option value="{{ $opsi }}" @selected(($nilai['ppa'] ?? '') === $opsi)>{{ $opsi }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <a href="{{ url()->current() }}" class="rs-chip-reset d-md-none">Reset semua filter</a>
            </div>
        </div>

        {{-- Tombol desktop --}}
        <div class="rs-filter-aksi d-none d-md-flex">
            <a href="{{ url()->current() }}" class="btn btn-outline-secondary rs-btn-ikon"
               title="Reset filter" aria-label="Reset filter">
                <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
            </a>
            <button type="submit" class="btn btn-primary">Terapkan</button>
        </div>

        {{-- Tombol HP --}}
        <div class="d-flex gap-2 w-100 d-md-none">
            <button type="button" class="btn btn-outline-secondary flex-fill" data-bs-toggle="collapse"
                    data-bs-target="#filterLain" aria-expanded="false" aria-controls="filterLain">
                <i class="bi bi-sliders me-1" aria-hidden="true"></i>Filter lain
                @if ($jumlahFilter)
                    <span class="rs-badge-count ms-2">{{ $jumlahFilter }}</span>
                @endif
            </button>
            <button type="submit" class="btn btn-primary flex-fill">Terapkan</button>
        </div>
    </div>
</form>