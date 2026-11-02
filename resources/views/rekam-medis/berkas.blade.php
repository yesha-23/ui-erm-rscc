@extends('layouts.rekam-medis')
@section('title', 'Berkas digital')

@section('content')
{{-- ===== Judul + tombol unggah ===== --}}
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <div>
        <p class="rs-eyebrow mb-1"><i class="bi bi-folder2-open me-1" aria-hidden="true"></i>Penunjang</p>
        <h2 class="h4 fw-bold mb-0">Berkas digital</h2>
        <p class="small rs-text-muted mb-0 mt-1">Hasil pemeriksaan dari luar, surat rujukan, dan dokumen pendukung pasien.</p>
    </div>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUnggah">
        <i class="bi bi-cloud-arrow-up me-1" aria-hidden="true"></i>Unggah berkas
    </button>
</div>

{{-- ===== Filter jenis ===== --}}
<nav class="d-flex flex-wrap gap-2 mb-3" aria-label="Saring jenis berkas">
    <a href="{{ route('rekam-medis.berkas', $pasien['no_reg']) }}"
       @class(['rs-pill-filter', 'active' => ! $filter]) @if (! $filter) aria-current="page" @endif>
        Semua <span class="rs-pill-filter-count">{{ $total }}</span>
    </a>
    @foreach ($jenisBerkas as $kunci => $label)
        <a href="{{ route('rekam-medis.berkas', [$pasien['no_reg'], 'jenis' => $kunci]) }}"
           @class(['rs-pill-filter', 'active' => $filter === $kunci]) @if ($filter === $kunci) aria-current="page" @endif>
            {{ $label }} <span class="rs-pill-filter-count">{{ $jumlah[$kunci] ?? 0 }}</span>
        </a>
    @endforeach
</nav>

{{-- ===== Daftar berkas ===== --}}
@if ($berkas->isEmpty())
    <div class="rs-card rs-empty">
        <span class="rs-empty-icon"><i class="bi bi-folder2" aria-hidden="true"></i></span>
        <h3 class="h6 fw-bold mb-1">{{ $filter ? 'Belum ada berkas jenis ini' : 'Belum ada berkas' }}</h3>
        <p class="rs-text-muted mb-3">Unggah hasil pemeriksaan dari luar, surat rujukan, atau dokumen pendukung lainnya.</p>
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUnggah">
            <i class="bi bi-cloud-arrow-up me-1" aria-hidden="true"></i>Unggah berkas
        </button>
    </div>
@else
    <ul class="rs-berkas-grid list-unstyled mb-0">
        @foreach ($berkas as $b)
            <li class="rs-card rs-berkas-card">
                <div class="d-flex gap-3">
                    <span @class(['rs-berkas-ikon', 'is-pdf' => $b['tipe'] === 'pdf', 'is-gambar' => $b['tipe'] === 'gambar'])
                          aria-hidden="true">
                        <i @class(['bi', 'bi-file-earmark-pdf' => $b['tipe'] === 'pdf', 'bi-file-earmark-image' => $b['tipe'] === 'gambar'])></i>
                    </span>
                    <div class="min-w-0 flex-grow-1">
                        <p class="rs-berkas-nama mb-1" title="{{ $b['nama'] }}">{{ $b['nama'] }}</p>
                        <p class="small rs-text-muted mb-0">{{ $jenisBerkas[$b['jenis']] }} · {{ $b['ukuran'] }}</p>
                    </div>
                </div>

                @if ($b['keterangan'])
                    <p class="small mb-0 mt-3">{{ $b['keterangan'] }}</p>
                @endif

                <div class="rs-berkas-foot">
                    <span class="small rs-text-muted">{{ $b['waktu'] }}<br>{{ $b['oleh'] }}</span>
                    <div class="d-flex gap-1">
                        {{-- TODO (IT RSCC): route lihat & unduh berkas --}}
                        <a href="#" class="btn btn-outline-secondary btn-sm" aria-label="Lihat {{ $b['nama'] }}">
                            <i class="bi bi-eye me-1" aria-hidden="true"></i>Lihat
                        </a>
                        <a href="#" class="btn btn-outline-secondary btn-sm" aria-label="Unduh {{ $b['nama'] }}">
                            <i class="bi bi-download" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endif
@endsection

{{-- ===== Popup unggah ===== --}}
@push('modal')
<div class="modal fade rs-modal-unggah" id="modalUnggah" tabindex="-1" aria-labelledby="judulUnggah" aria-hidden="true"
     data-rs-unggah @if ($errors->any()) data-rs-buka-otomatis @endif>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <form class="modal-content" method="POST" enctype="multipart/form-data" novalidate
              action="{{ route('rekam-medis.berkas.simpan', $pasien['no_reg']) }}">
            @csrf

            <div class="modal-header">
                <div>
                    <h2 class="modal-title h5 fw-bold" id="judulUnggah">Unggah berkas</h2>
                    <p class="small rs-text-muted mb-0">{{ $pasien['nama'] }} · RM {{ $pasien['no_rm'] }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                @if ($errors->any())
                    <div class="alert alert-danger small d-flex gap-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                        <div>
                            @foreach ($errors->all() as $pesan)
                                <div>{{ $pesan }}</div>
                            @endforeach
                            <div class="mt-1">Berkas perlu dipilih ulang.</div>
                        </div>
                    </div>
                @endif

                {{-- Area tarik & lepas: <input> tersembunyi tapi tetap bisa difokus keyboard --}}
                <input type="file" id="inputBerkas" name="berkas[]" multiple class="visually-hidden"
                       accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">
                <label for="inputBerkas" class="rs-dropzone" data-rs-dropzone>
                    <span class="rs-dropzone-ikon"><i class="bi bi-cloud-arrow-up" aria-hidden="true"></i></span>
                    <span class="fw-semibold">Tarik berkas ke sini atau <span class="rs-dropzone-link">pilih dari komputer</span></span>
                    <span class="small rs-text-muted">PDF, JPG, PNG · maksimal 10 MB per berkas · hingga 10 berkas</span>
                </label>

                <ul class="rs-berkas-dipilih list-unstyled" data-rs-daftar-berkas aria-live="polite" hidden></ul>

                <div class="row g-3 mt-1">
                    <div class="col-sm-6">
                        <label for="jenisBerkas" class="form-label">Jenis berkas</label>
                        <select id="jenisBerkas" name="jenis" required
                                @class(['form-select', 'is-invalid' => $errors->has('jenis')])>
                            <option value="" disabled @selected(! old('jenis'))>Pilih jenis…</option>
                            @foreach ($jenisBerkas as $kunci => $label)
                                <option value="{{ $kunci }}" @selected(old('jenis', $filter) === $kunci)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('jenis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label for="keteranganBerkas" class="form-label">Keterangan <span class="rs-text-muted fw-normal">(opsional)</span></label>
                        <input type="text" id="keteranganBerkas" name="keterangan" maxlength="255"
                               value="{{ old('keterangan') }}" placeholder="mis. hasil lab klinik X, 31 Okt"
                               @class(['form-control', 'is-invalid' => $errors->has('keterangan')])>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" data-rs-tombol-unggah disabled>
                    <i class="bi bi-upload me-1" aria-hidden="true"></i>Unggah<span data-rs-jumlah-berkas></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endpush