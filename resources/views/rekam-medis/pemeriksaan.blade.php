@extends('layouts.rekam-medis')
@section('title', 'Pemeriksaan Rawat Jalan')

@section('content')
    @php
        $noReg   = $pasien['no_reg'];
        $v       = fn (string $k, $bawaan = '') => old($k, $isi[$k] ?? $bawaan);
        $pilih10 = (array) old('icd10', $isi['icd10'] ?? []);
        $pilih9  = (array) old('icd9', $isi['icd9'] ?? []);
        $tl      = $v('tindak_lanjut');
        $teksStatus = match ($status) {
            'final' => 'Disimpan final · ' . $waktu,
            'draf'  => 'Draf tersimpan · ' . $waktu,
            default => 'Belum pernah disimpan',
        };
        $tindakLanjut = ['pulang' => 'Pulang', 'kontrol' => 'Kontrol ulang', 'rujuk' => 'Rujuk', 'ranap' => 'Rawat inap'];
        $galatIcd10 = $errors->first('icd10') ?: $errors->first('icd10.*');
    @endphp

    {{-- Judul + lompat ke bagian S/O/A/P --}}
    <x-judul-form ikon="bi-clipboard2-pulse" kelompok="Form rekam medis · Rawat Jalan" judul="Pemeriksaan dokter"
                  :sub="$pasien['unit_tujuan'] . ' · ' . $pasien['dpjp_nama']">
        <nav class="rs-px-nav" aria-label="Lompat ke bagian">
            <a href="#bag-s" title="Subjective">S</a>
            <a href="#bag-o" title="Objective">O</a>
            <a href="#bag-a" title="Assessment">A</a>
            <a href="#bag-p" title="Planning">P</a>
        </nav>
    </x-judul-form>

    @if ($errors->any())
        <div class="alert alert-danger rs-flash" role="alert">
            <strong>Belum bisa disimpan final.</strong> Periksa {{ $errors->count() }} isian yang ditandai merah.
        </div>
    @endif

    <div class="rs-px">
        <form id="formPemeriksaan" method="POST" action="{{ route('rekam-medis.pemeriksaan.simpan', $noReg) }}"
              class="d-flex flex-column gap-3" novalidate data-rs-form-pemeriksaan>
            @csrf

            {{-- S --}}
            <section class="rs-card" id="bag-s" aria-labelledby="judul-s">
                <h3 class="rs-px-judul" id="judul-s">
                    <span class="rs-px-huruf" aria-hidden="true">S</span>Subjective <small>keluhan dari pasien</small>
                </h3>
                <div class="mb-3">
                    <label for="f-keluhan" class="form-label">Keluhan utama <span class="text-danger">*</span></label>
                    <textarea id="f-keluhan" name="keluhan" rows="2" @class(['form-control', 'is-invalid' => $errors->has('keluhan')])
                              placeholder="mis. nyeri ulu hati sejak 3 hari, mual">{{ $v('keluhan') }}</textarea>
                    @error('keluhan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label for="f-rps" class="form-label">Riwayat penyakit sekarang</label>
                    <textarea id="f-rps" name="rps" rows="3" class="form-control"
                              placeholder="Perjalanan penyakit, faktor pemberat/peringan, obat yang sudah diminum">{{ $v('rps') }}</textarea>
                </div>
            </section>

            {{-- O --}}
            <section class="rs-card" id="bag-o" aria-labelledby="judul-o">
                <h3 class="rs-px-judul" id="judul-o">
                    <span class="rs-px-huruf" aria-hidden="true">O</span>Objective <small>hasil pemeriksaan</small>
                </h3>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <span class="form-label mb-0">Tanda vital terakhir · {{ \Illuminate\Support\Carbon::parse($ttv['stempel'])->format('H:i') }}</span>
                    <a href="{{ route('rekam-medis.ttv', $noReg) }}" class="small fw-semibold">Ukur ulang <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="rs-px-ttv mb-3">
                    <span>TD {{ $ttv['td_sistolik'] }}/{{ $ttv['td_diastolik'] }} mmHg</span>
                    <span>Nadi {{ $ttv['nadi'] }} x/menit</span>
                    <span>Napas {{ $ttv['napas'] }} x/menit</span>
                    <span>Suhu {{ number_format((float) $ttv['suhu'], 1, ',', '') }} °C</span>
                    <span>SpO₂ {{ $ttv['spo2'] }} %</span>
                </div>

                <div class="mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-1">
                        <label for="f-fisik" class="form-label mb-0">Pemeriksaan fisik</label>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-rs-target="#f-fisik"
                                data-rs-isi-teks="Keadaan umum baik, kesadaran compos mentis. Pemeriksaan fisik dalam batas normal.">
                            <i class="bi bi-check2-all me-1" aria-hidden="true"></i>Isi: dalam batas normal
                        </button>
                    </div>
                    <textarea id="f-fisik" name="fisik" rows="3" class="form-control"
                              placeholder="Temuan pemeriksaan fisik yang bermakna">{{ $v('fisik') }}</textarea>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <span class="form-label mb-0">Hasil penunjang terbaru</span>
                    <a href="{{ route('rekam-medis.permintaan', $noReg) }}" class="small fw-semibold">
                        Permintaan baru <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                    @foreach ($penunjang as $h)
                        <li class="d-flex gap-2 small">
                            <i class="bi {{ $h['jenis'] === 'lab' ? 'bi-droplet-half' : 'bi-image' }} text-primary" aria-hidden="true"></i>
                            <span><strong>{{ $h['judul'] }}</strong> · {{ $h['ringkasan'] }}
                                <span class="rs-text-muted">({{ $h['tanggal'] }}, {{ $h['jam'] }})</span></span>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- A --}}
            <section class="rs-card" id="bag-a" aria-labelledby="judul-a">
                <h3 class="rs-px-judul" id="judul-a">
                    <span class="rs-px-huruf" aria-hidden="true">A</span>Assessment <small>diagnosis</small>
                </h3>
                <div class="mb-3">
                    <label for="f-diagnosa" class="form-label">Diagnosa kerja <span class="text-danger">*</span></label>
                    <textarea id="f-diagnosa" name="diagnosa" rows="2" @class(['form-control', 'is-invalid' => $errors->has('diagnosa')])
                              placeholder="mis. Dispepsia fungsional">{{ $v('diagnosa') }}</textarea>
                    @error('diagnosa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="rs-px-icd" data-rs-icd data-nama="icd10" data-utama="1">
                    <label for="cari-icd10" class="form-label">
                        Kode ICD-10 <span class="text-danger">*</span>
                        <span class="rs-text-muted fw-normal">· kode pertama = diagnosis utama</span>
                    </label>
                    <ul class="rs-px-chips" aria-label="ICD-10 terpilih" data-rs-icd-terpilih>
                        @foreach ($pilih10 as $i => $kode)
                            <li class="rs-px-chip">
                                @if ($i === 0)<span class="rs-px-utama">Utama</span>@endif
                                <input type="hidden" name="icd10[]" value="{{ $kode }}">
                                <strong>{{ $kode }}</strong><span>{{ $icd10[$kode] ?? '' }}</span>
                                <button type="button" class="rs-px-chip-hapus" aria-label="Hapus {{ $kode }}" data-rs-icd-hapus>
                                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <div class="position-relative">
                        <div @class(['rs-search', 'border-danger' => $galatIcd10])>
                            <i class="bi bi-search" aria-hidden="true"></i>
                            <input type="text" id="cari-icd10" autocomplete="off" role="combobox"
                                   aria-expanded="false" aria-controls="daftar-icd10" aria-autocomplete="list"
                                   placeholder="Ketik kode atau nama penyakit, mis. tifoid atau A01" data-rs-icd-cari>
                        </div>
                        <ul id="daftar-icd10" class="rs-px-saran" role="listbox" aria-label="Saran ICD-10" hidden data-rs-icd-daftar></ul>
                    </div>
                    @if ($galatIcd10) <div class="invalid-feedback d-block">{{ $galatIcd10 }}</div> @endif
                    <script type="application/json" data-rs-icd-katalog>@json($icd10)</script>
                </div>
            </section>

            {{-- P --}}
            <section class="rs-card" id="bag-p" aria-labelledby="judul-p">
                <h3 class="rs-px-judul" id="judul-p">
                    <span class="rs-px-huruf" aria-hidden="true">P</span>Planning <small>rencana & tindak lanjut</small>
                </h3>

                <div class="mb-3">
                    <label for="f-rencana" class="form-label">Rencana / terapi <span class="text-danger">*</span></label>
                    <textarea id="f-rencana" name="rencana" rows="3" @class(['form-control', 'is-invalid' => $errors->has('rencana')])
                              placeholder="Terapi, pemeriksaan lanjutan, anjuran">{{ $v('rencana') }}</textarea>
                    @error('rencana') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="rs-px-icd mb-3" data-rs-icd data-nama="icd9" data-utama="0">
                    <label for="cari-icd9" class="form-label">Tindakan (ICD-9-CM) <span class="rs-text-muted fw-normal">· bila ada</span></label>
                    <ul class="rs-px-chips" aria-label="ICD-9 terpilih" data-rs-icd-terpilih>
                        @foreach ($pilih9 as $kode)
                            <li class="rs-px-chip">
                                <input type="hidden" name="icd9[]" value="{{ $kode }}">
                                <strong>{{ $kode }}</strong><span>{{ $icd9[$kode] ?? '' }}</span>
                                <button type="button" class="rs-px-chip-hapus" aria-label="Hapus {{ $kode }}" data-rs-icd-hapus>
                                    <i class="bi bi-x-lg" aria-hidden="true"></i>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                    <div class="position-relative">
                        <div class="rs-search">
                            <i class="bi bi-search" aria-hidden="true"></i>
                            <input type="text" id="cari-icd9" autocomplete="off" role="combobox"
                                   aria-expanded="false" aria-controls="daftar-icd9" aria-autocomplete="list"
                                   placeholder="Ketik kode atau nama tindakan, mis. EKG atau 89.52" data-rs-icd-cari>
                        </div>
                        <ul id="daftar-icd9" class="rs-px-saran" role="listbox" aria-label="Saran ICD-9" hidden data-rs-icd-daftar></ul>
                    </div>
                    @if ($errors->first('icd9.*')) <div class="invalid-feedback d-block">{{ $errors->first('icd9.*') }}</div> @endif
                    <script type="application/json" data-rs-icd-katalog>@json($icd9)</script>
                </div>

                <fieldset class="mb-3">
                    <legend class="form-label">Tindak lanjut <span class="text-danger">*</span></legend>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($tindakLanjut as $kunci => $label)
                            <input type="radio" class="btn-check" name="tindak_lanjut" id="tl-{{ $kunci }}"
                                   value="{{ $kunci }}" @checked($tl === $kunci)>
                            <label class="btn btn-outline-secondary" for="tl-{{ $kunci }}">{{ $label }}</label>
                        @endforeach
                    </div>
                    @error('tindak_lanjut') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </fieldset>

                <div class="mb-3" data-rs-tampil-jika="kontrol" @if ($tl !== 'kontrol') hidden @endif>
                    <label for="f-tgl-kontrol" class="form-label">Tanggal kontrol <span class="text-danger">*</span></label>
                    <input type="date" id="f-tgl-kontrol" name="tgl_kontrol" value="{{ $v('tgl_kontrol') }}"
                           min="{{ now()->toDateString() }}" style="max-width: 220px"
                           @class(['form-control', 'is-invalid' => $errors->has('tgl_kontrol')])>
                    @error('tgl_kontrol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3" data-rs-tampil-jika="rujuk" @if ($tl !== 'rujuk') hidden @endif>
                    <label for="f-rujuk" class="form-label">Tujuan rujukan <span class="text-danger">*</span></label>
                    <input type="text" id="f-rujuk" name="tujuan_rujuk" value="{{ $v('tujuan_rujuk') }}"
                           placeholder="mis. Klinik Jantung / RS rujukan"
                           @class(['form-control', 'is-invalid' => $errors->has('tujuan_rujuk')])>
                    @error('tujuan_rujuk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="f-edukasi" class="form-label">Edukasi</label>
                    <textarea id="f-edukasi" name="edukasi" rows="2" class="form-control"
                              placeholder="mis. makan teratur, hindari makanan pedas">{{ $v('edukasi') }}</textarea>
                </div>

                <div class="rs-px-lanjut">
                    <span class="small fw-semibold rs-text-muted">Lanjutkan dari sini:</span>
                    <a href="{{ route('rekam-medis.resep', $noReg) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-capsule me-1" aria-hidden="true"></i>E-Resep
                    </a>
                    <a href="{{ route('rekam-medis.permintaan', $noReg) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-eyedropper me-1" aria-hidden="true"></i>Permintaan lab / radiologi
                    </a>
                    <a href="{{ route('rekam-medis.dokumen', [$noReg, 'surat']) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-file-earmark-text me-1" aria-hidden="true"></i>Surat kontrol / rujukan
                    </a>
                </div>
            </section>
        </form>

        {{-- Panel kanan: kunjungan sebelumnya --}}
        <aside class="rs-card rs-px-panel" aria-labelledby="judul-sebelumnya">
            <h3 id="judul-sebelumnya" class="h6 fw-bold mb-1">Kunjungan sebelumnya</h3>
            <p class="small rs-text-muted mb-3">Untuk pasien kontrol: salin diagnosa, ICD-10, dan rencana, lalu sesuaikan.</p>
            <p class="alert alert-info small py-2 px-3" role="status" hidden data-rs-salin-info></p>

            @foreach ($sebelumnya as $k)
                <article class="rs-px-kunjungan">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <div class="fw-bold small">{{ $k['tanggal'] }}</div>
                            <div class="small rs-text-muted">{{ $k['klinik'] }} · {{ $k['dokter'] }}</div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm text-nowrap"
                                aria-label="Salin dari kunjungan {{ $k['tanggal'] }}"
                                data-rs-salin="{{ json_encode(['tanggal' => $k['tanggal'], 'diagnosa' => $k['a'], 'rencana' => $k['p'], 'icd10' => $k['icd10']]) }}">
                            <i class="bi bi-copy me-1" aria-hidden="true"></i>Salin
                        </button>
                    </div>
                    <dl class="rs-px-soap">
                        <dt>S</dt><dd>{{ $k['s'] }}</dd>
                        <dt>O</dt><dd>{{ $k['o'] }}</dd>
                        <dt>A</dt><dd>{{ $k['a'] }} <span class="rs-text-muted">({{ implode(', ', $k['icd10']) }})</span></dd>
                        <dt>P</dt><dd>{{ $k['p'] }}</dd>
                    </dl>
                </article>
            @endforeach

            <a href="{{ route('rekam-medis.riwayat', $noReg) }}" class="small fw-semibold d-inline-block mt-3">
                Semua riwayat <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        </aside>
    </div>

    {{-- Bar aksi (di luar form; tombol terhubung lewat atribut form="") --}}
    <div class="rs-action-bar">
        <span class="small rs-text-muted">
            <i class="bi bi-info-circle me-1" aria-hidden="true"></i><span data-rs-status-simpan>{{ $teksStatus }}</span>
        </span>
        <div class="rs-action-bar-buttons">
            <button type="submit" form="formPemeriksaan" name="aksi" value="draf" class="btn btn-outline-secondary">
                <i class="bi bi-floppy me-1" aria-hidden="true"></i>Simpan draf
            </button>
            <button type="submit" form="formPemeriksaan" name="aksi" value="final" class="btn btn-primary">
                <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Simpan final
            </button>
        </div>
    </div>
@endsection