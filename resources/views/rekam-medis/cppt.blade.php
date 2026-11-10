@extends('layouts.rekam-medis')
@section('title', 'CPPT dan SBAR')

@section('content')
@php
    $formatAktif  = old('format', 'soap');
    $profesiAktif = old('profesi', 'perawat');
    $labelTampil  = [
        'semua'    => 'Semua',
        'menunggu' => 'Menunggu verifikasi',
        'dokter'   => 'Dokter',
        'perawat'  => 'Perawat',
    ];
    // Judul bagian di hasil catatan (ditulis lengkap seperti sistem lama)
    $judulBagian = [
        'soap' => ['s' => 'Subjective', 'o' => 'Objective', 'a' => 'Assessment', 'p' => 'Planning'],
        'sbar' => ['s' => 'Situation', 'b' => 'Background', 'a' => 'Assessment', 'r' => 'Recommendation'],
    ];
@endphp

{{-- ===== Judul ===== --}}
<x-judul-form ikon="bi-journal-text" kelompok="Catatan terintegrasi" judul="CPPT dan SBAR"
              sub="Catatan perkembangan pasien dari semua profesional pemberi asuhan (PPA)." />

{{-- ===== Form catatan baru ===== --}}
<form method="POST" action="{{ route('rekam-medis.cppt.simpan', $pasien['no_reg']) }}" novalidate
      class="rs-card rs-cppt-form mb-3">
    @csrf

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
        <div>
            <h3 class="h6 fw-bold mb-1">Catatan baru</h3>
            <p class="small rs-text-muted mb-0">
                PPA: [Nama PPA] · {{ now()->locale('id')->translatedFormat('d M Y, H:i') }}
            </p>
        </div>
        <div class="rs-segmented rs-segmented-inline" role="radiogroup" aria-label="Format catatan">
            @foreach ($formatCatatan as $kode => $f)
                <input type="radio" class="btn-check" name="format" id="format-{{ $kode }}" value="{{ $kode }}"
                       autocomplete="off" @checked($formatAktif === $kode)>
                <label for="format-{{ $kode }}" class="rs-segmented-item">{{ $f['judul'] }}</label>
            @endforeach
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger small d-flex gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
            <div>
                @foreach ($errors->all() as $pesan)
                    <div>{{ $pesan }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- TODO (IT RSCC): profesi diambil otomatis dari akun yang login --}}
    <x-form.pilihan name="profesi" label="Sebagai" :opsi="$profesiList" :nilai="$profesiAktif" class="mb-3" />

    {{-- Dua set isian; yang tampil mengikuti format terpilih (CSS :has) --}}
    @foreach ($formatCatatan as $kode => $f)
        <div class="rs-soap-isian" data-rs-format="{{ $kode }}">
            @foreach ($f['bagian'] as $huruf => [$nama, $petunjuk])
                @php
                    $kunci = "$kode.$huruf";
                    $id    = "$kode-$huruf";
                @endphp
                <div class="rs-soap-baris">
                    <span class="rs-soap-huruf" aria-hidden="true">{{ strtoupper($huruf) }}</span>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-baseline gap-2 mb-1">
                            <label for="{{ $id }}" class="form-label mb-0">
                                {{ $nama }}
                                @if (in_array($kunci, $wajib, true))<span class="text-danger" aria-hidden="true">*</span>@endif
                                <span class="fw-normal small rs-text-muted">· {{ $petunjuk }}</span>
                            </label>
                            @if ($kunci === 'soap.o')
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none"
                                        data-rs-sisip="#soap-o" data-rs-teks="{{ $ttvTerakhir }}">
                                    <i class="bi bi-plus-circle me-1" aria-hidden="true"></i>Sisipkan TTV terakhir
                                </button>
                            @endif
                        </div>
                        <textarea id="{{ $id }}" name="{{ $kode }}[{{ $huruf }}]" rows="2"
                                  @class(['form-control', 'is-invalid' => $errors->has($kunci)])>{{ old($kunci) }}</textarea>
                        @error($kunci)
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="mt-3">
        <label for="instruksi" class="form-label">Instruksi PPA <span class="rs-text-muted fw-normal">(opsional)</span></label>
        <textarea id="instruksi" name="instruksi" rows="2" placeholder="mis. observasi TTV tiap 4 jam"
                  @class(['form-control', 'is-invalid' => $errors->has('instruksi')])>{{ old('instruksi') }}</textarea>
        @error('instruksi')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
        <span class="small rs-text-muted">
            <i class="bi bi-info-circle me-1" aria-hidden="true"></i>Catatan selain dari dokter akan menunggu verifikasi DPJP.
        </span>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan catatan
        </button>
    </div>
</form>

{{-- ===== Riwayat catatan ===== --}}
<section class="rs-card p-0 overflow-hidden">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 px-3 pt-3 pb-2">
        <h3 class="h6 fw-bold mb-0">Riwayat catatan</h3>
        @if ($jumlah['menunggu'])
            <span class="rs-status rs-status-proses">{{ $jumlah['menunggu'] }} menunggu verifikasi DPJP</span>
        @endif
    </div>

    <nav class="d-flex flex-wrap gap-2 px-3 pb-3" aria-label="Saring catatan">
        @foreach ($labelTampil as $kunci => $label)
            <a href="{{ route('rekam-medis.cppt', [$pasien['no_reg'], 'tampil' => $kunci]) }}"
               @class(['rs-pill-filter', 'active' => $tampil === $kunci])
               @if ($tampil === $kunci) aria-current="page" @endif>
                {{ $label }} <span class="rs-pill-filter-count">{{ $jumlah[$kunci] }}</span>
            </a>
        @endforeach
    </nav>

    @if ($catatan->isEmpty())
        <div class="rs-empty border-top">
            <p class="rs-text-muted mb-0">Tidak ada catatan untuk pilihan ini.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table rs-table rs-table-kartu rs-table-ringkas rs-table-cppt mb-0">
                <thead>
                    <tr>
                        <th scope="col">PPA</th>
                        <th scope="col">Hasil pemeriksaan</th>
                        <th scope="col">Instruksi</th>
                        <th scope="col">Verifikasi DPJP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($catatan as $c)
                        @php $menunggu = $c['perlu_verifikasi'] && ! $c['verifikasi']; @endphp
                        <tr @class(['rs-row-menunggu' => $menunggu])>
                            <td class="rs-td-utama">
                                <div>
                                    <div class="fw-semibold">{{ $c['ppa'] }}</div>
                                    <span class="rs-profesi rs-profesi-{{ $c['profesi'] }}">{{ $profesiList[$c['profesi']] }}</span>
                                    <div class="small rs-text-muted mt-1">{{ $c['waktu'] }}</div>
                                </div>
                            </td>
                            <td data-label="Hasil pemeriksaan">
                                <div @class(['min-w-0 rs-cppt-hasil', 'rs-cppt-hasil-dokter' => $c['profesi'] === 'dokter'])>
                                    <span class="rs-format-tag">{{ strtoupper($c['format']) }}</span>
                                    <dl class="rs-cppt-isi">
                                        @foreach ($c['isi'] as $huruf => $teks)
                                            <dt>{{ $judulBagian[$c['format']][$huruf] ?? strtoupper($huruf) }}:</dt>
                                            <dd>{{ $teks }}</dd>
                                        @endforeach
                                    </dl>
                                </div>
                            </td>
                            <td data-label="Instruksi"><span>{{ $c['instruksi'] ?: '—' }}</span></td>
                            <td data-label="Verifikasi DPJP">
                                @if ($c['verifikasi'])
                                    <div>
                                        <span class="rs-status rs-status-hasil">Terverifikasi</span>
                                        <div class="small rs-text-muted mt-1">{{ $c['verifikasi']['oleh'] }} · {{ $c['verifikasi']['waktu'] }}</div>
                                    </div>
                                @elseif ($menunggu)
                                    <form method="POST" action="{{ route('rekam-medis.cppt.verifikasi', [$pasien['no_reg'], $c['id']]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-patch-check me-1" aria-hidden="true"></i>Verifikasi
                                        </button>
                                    </form>
                                @else
                                    <span>—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection