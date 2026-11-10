@extends('layouts.rekam-medis')
@section('title', 'Asesmen awal gawat darurat')

{{--
  Asesmen Awal Keperawatan Gawat Darurat (IGD).
  Isi, urutan, dan nama isian disamakan dengan form ERM lama (igd_aak) supaya pengguna tidak bingung:
  bagian atas (tgl/jam periksa, cara datang, pengantar, alergi) + tab OBJECTIVE, ASSESSMENT, PLANNING.
  Pilihan diambil dari DataContoh::opsiAsesmenGd().
--}}

@section('content')
@php
    // Tab yang dibuka pertama: dari error validasi → dari tab terakhir disimpan → tab 1
    $petaLangkah = [
        0 => ['gcs_e', 'gcs_v', 'gcs_m', 'kesadaran', 'td_sistolik', 'td_diastolik',
              'nadi', 'napas', 'suhu', 'spo2', 'nyeri', 'jatuh.a', 'jatuh.b'],
        1 => ['diagnosa', 'diagnosa_lain'],
    ];
    $langkahAwal = (int) old('langkah', session('langkah', 0));
    foreach ($petaLangkah as $i => $kunci) {
        if ($errors->hasAny($kunci)) { $langkahAwal = $i; break; }
    }

    $keterangan = match ($status) {
        'final' => 'Final · disimpan ' . $waktu,
        'draf'  => 'Draf · terakhir disimpan ' . $waktu,
        default => 'Belum pernah disimpan',
    };

    // Nilai isian: input lama (setelah gagal validasi) → data tersimpan → bawaan
    $v = fn (string $kunci, $bawaan = null) => old($kunci, data_get($isi, $kunci, $bawaan));

    // Alergi = data pasien (per No. RM), sama dengan chip di header. Diubah lewat modal alergi.
    $alergi = match ($pasien['alergi_status'] ?? null) {
        'ada'   => ['rs-badge-nonjkn', 'bi-exclamation-triangle-fill', 'Ada alergi: ' . $pasien['alergi']],
        'tidak' => ['rs-badge-jkn', 'bi-check-circle-fill', 'Tidak ada alergi'],
        default => ['rs-badge-belum', 'bi-question-circle-fill', 'Alergi belum dikaji'],
    };

    $jaminanBawaan = $pasien['penjamin'] === 'JKN' ? 'bpjs-kesehatan' : 'umum-tunai';
@endphp

{{-- ===== Judul ===== --}}
<x-judul-form ikon="bi-clipboard2-pulse" :kelompok="'Form rekam medis · ' . $pasien['unit']"
              judul="Asesmen awal keperawatan gawat darurat" :sub="'Diisi oleh perawat · ' . $keterangan">
    @if ($status === 'final')
        <x-badge jenis="sukses">Final</x-badge>
    @elseif ($status === 'draf')
        <x-badge jenis="peringatan">Draf</x-badge>
    @endif
</x-judul-form>

@if ($errors->any())
    <div class="alert alert-danger d-flex gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        <div>
            <strong>Belum bisa disimpan final.</strong> Lengkapi isian berikut:
            <ul class="mb-0 ps-3 mt-1">
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form id="form-asesmen" method="POST" novalidate data-rs-asesmen data-rs-langkah-awal="{{ $langkahAwal }}"
      action="{{ route('rekam-medis.asesmen.simpan', $pasien['no_reg']) }}">
    @csrf
    <input type="hidden" name="langkah" value="{{ $langkahAwal }}" data-rs-langkah-input>

    {{-- ================= BAGIAN ATAS (selalu tampil, seperti ERM lama) ================= --}}
    <section class="rs-card mb-3" aria-labelledby="judul-kedatangan">
        <h3 id="judul-kedatangan" class="h6 fw-bold mb-3">Data kunjungan</h3>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="tgl_periksa" class="form-label">Tgl Periksa <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="date" id="tgl_periksa" name="tgl_periksa" value="{{ $v('tgl_periksa', now()->toDateString()) }}"
                       @class(['form-control', 'is-invalid' => $errors->has('tgl_periksa')])>
                @error('tgl_periksa')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="cara_datang" class="form-label">Cara Pasien Datang <span class="text-danger" aria-hidden="true">*</span></label>
                <select id="cara_datang" name="cara_datang" @class(['form-select', 'is-invalid' => $errors->has('cara_datang')])>
                    <option value="">Pilih…</option>
                    @foreach ($opsi['cara_datang'] as $kode => $teks)
                        <option value="{{ $kode }}" @selected((string) $v('cara_datang') === (string) $kode)>{{ $teks }}</option>
                    @endforeach
                </select>
                @error('cara_datang')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="jam_periksa" class="form-label">Jam Periksa <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="time" id="jam_periksa" name="jam_periksa" value="{{ $v('jam_periksa', now()->format('H:i')) }}"
                       @class(['form-control', 'is-invalid' => $errors->has('jam_periksa')])>
                @error('jam_periksa')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="pengantar_nama" class="form-label">Nama Pengantar (Jika Diantar)</label>
                <input type="text" id="pengantar_nama" name="pengantar_nama" class="form-control" value="{{ $v('pengantar_nama') }}">
            </div>
            <div class="col-md-6">
                <p class="form-label mb-2" id="label-alergi">Alergi</p>
                <div class="rs-aak-alergi {{ $alergi[0] }}" aria-labelledby="label-alergi">
                    <i class="bi {{ $alergi[1] }}" aria-hidden="true"></i>
                    <span class="flex-grow-1">{{ $alergi[2] }}</span>
                    <button type="button" class="btn btn-link btn-sm p-0 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAlergi">
                        Ubah<span class="visually-hidden"> riwayat alergi</span>
                    </button>
                </div>
                <div class="form-text">Berlaku untuk semua kunjungan pasien ini, sama dengan alergi di header.</div>
            </div>
            <div class="col-md-6">
                <label for="pengantar_hp" class="form-label">No HP Pengantar (Jika Diantar)</label>
                <input type="tel" id="pengantar_hp" name="pengantar_hp" class="form-control" inputmode="tel" value="{{ $v('pengantar_hp') }}">
            </div>
        </div>
    </section>

    {{-- ===== Tab ===== --}}
    <div class="rs-langkah-nav" role="tablist" aria-label="Bagian asesmen">
        @foreach ($langkah as $i => $nama)
            <button type="button" role="tab" class="rs-langkah-tab"
                    id="tab-langkah-{{ $i }}" aria-controls="langkah-{{ $i }}" data-rs-langkah-tab>
                <span class="rs-langkah-nomor" aria-hidden="true">{{ $i + 1 }}</span>{{ $nama }}
            </button>
        @endforeach
    </div>

    {{-- ================= TAB 1: OBJECTIVE ================= --}}
    <div id="langkah-0" role="tabpanel" aria-labelledby="tab-langkah-0" data-rs-langkah-panel class="d-flex flex-column gap-3">
        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3 rs-langkah-judul" tabindex="-1">Keadaan umum</h3>
            <x-form.gcs :opsi="$opsi" :isi="$isi" />
            <div class="rs-aak-daftar mt-3">
                <x-form.baris label="Kesadaran" name="kesadaran" wajib :opsi="$opsi['kesadaran']" :nilai="$v('kesadaran')" />
            </div>
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3">Tanda-tanda vital</h3>
            <x-form.ttv :isi="$isi" />
        </section>

        {{-- Nyeri: Wong Baker Faces Pain Scale --}}
        @php $nyeri = (string) $v('nyeri', '0'); @endphp
        <section class="rs-card" data-rs-nyeri>
            <h3 class="h6 fw-bold mb-1">Wong Baker Faces Pain Scale</h3>
            <p class="small rs-text-muted mb-3">Untuk dewasa dan anak usia ≥ 3 tahun. Klik wajah atau angka.</p>

            <fieldset class="rs-pilihan-grup">
                <legend class="rs-legend">Skala nyeri (0–10) <span class="text-danger" aria-hidden="true">*</span></legend>
                <div class="rs-wb" aria-hidden="true">
                    @foreach ($opsi['nyeri_wajah'] as $n => [$ikon, $teks])
                        <label for="nyeri-{{ $n }}" class="rs-wb-item" data-n="{{ $n }}">
                            <i class="bi {{ $ikon }}"></i>
                            <span class="rs-wb-angka">{{ $n }}</span>
                            <span>{{ $teks }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="rs-nyeri">
                    @for ($n = 0; $n <= 10; $n++)
                        <input type="radio" class="btn-check" name="nyeri" id="nyeri-{{ $n }}" value="{{ $n }}"
                               autocomplete="off" @checked($nyeri === (string) $n)>
                        <label for="nyeri-{{ $n }}" class="rs-nyeri-angka">{{ $n }}</label>
                    @endfor
                </div>
            </fieldset>
            <p class="small fw-semibold mt-2 mb-0" data-rs-nyeri-label aria-live="polite"></p>

            {{-- Detail nyeri: disembunyikan bila skala 0 (CSS :has) --}}
            <div class="rs-nyeri-detail rs-aak-daftar">
                <x-form.baris label="Faktor yang memperberat" name="nyeri_pemberat" tipe="checkbox"
                              :opsi="$opsi['nyeri_pemberat']" :nilai="$v('nyeri_pemberat', [])"
                              isian="nyeri_pemberat_lain" :isian-nilai="$v('nyeri_pemberat_lain')" isian-saat="lainnya" />
                <div class="rs-aak-baris">
                    <label for="nyeri_peringan" class="rs-aak-tanya">Faktor yang meringankan</label>
                    <div class="rs-aak-jawab">
                        <input type="text" id="nyeri_peringan" name="nyeri_peringan" class="form-control"
                               value="{{ $v('nyeri_peringan') }}" placeholder="mis. obat dan istirahat">
                    </div>
                </div>
                <x-form.baris label="Kualitas nyeri" name="nyeri_kualitas" :opsi="$opsi['nyeri_kualitas']"
                              :nilai="$v('nyeri_kualitas')"
                              isian="nyeri_kualitas_lain" :isian-nilai="$v('nyeri_kualitas_lain')" isian-saat="lainnya" />
                <div class="rs-aak-baris">
                    <label for="nyeri_lokasi" class="rs-aak-tanya">Lokasi nyeri</label>
                    <div class="rs-aak-jawab">
                        <input type="text" id="nyeri_lokasi" name="nyeri_lokasi" class="form-control"
                               value="{{ $v('nyeri_lokasi') }}" placeholder="mis. ulu hati">
                    </div>
                </div>
                <x-form.baris label="Menjalar" name="nyeri_menjalar" :opsi="$opsi['ya_tidak']" :nilai="$v('nyeri_menjalar')" />
                <x-form.baris label="Tingkat keparahan" name="nyeri_keparahan" :opsi="$opsi['nyeri_keparahan']" :nilai="$v('nyeri_keparahan')" />
                <x-form.baris label="Lamanya nyeri" name="nyeri_lama" :opsi="$opsi['nyeri_lama']" :nilai="$v('nyeri_lama')" />
                <x-form.baris label="Frekuensi nyeri" name="nyeri_waktu" :opsi="$opsi['nyeri_waktu']" :nilai="$v('nyeri_waktu')" />
            </div>
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3">Antropometri</h3>
            <div class="rs-aak-antropometri">
                @foreach (['tb' => ['TB', 'cm'], 'bb' => ['BB', 'kg'], 'lila' => ['LILA', 'cm'], 'tinggi_lutut' => ['Tinggi Lutut', 'cm']] as $kunci => [$lbl, $satuan])
                    <div>
                        <label for="antro-{{ $kunci }}" class="form-label small fw-semibold">{{ $lbl }}</label>
                        <div class="input-group">
                            <input type="number" inputmode="decimal" step="0.1" min="0" id="antro-{{ $kunci }}"
                                   name="{{ $kunci }}" class="form-control" value="{{ $v($kunci) }}">
                            <span class="input-group-text">{{ $satuan }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rs-card" data-rs-jatuh>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
                <h3 class="h6 fw-bold mb-0">Pengkajian risiko jatuh <span class="fw-normal rs-text-muted">(diisi oleh perawat)</span></h3>
                <span class="rs-jatuh-hasil" data-rs-jatuh-hasil aria-live="polite"></span>
            </div>
            <div class="rs-aak-daftar">
                @foreach ($opsi['risiko_jatuh'] as $kunci => $pertanyaan)
                    <x-form.baris :label="$pertanyaan" name="jatuh[{{ $kunci }}]" wajib :opsi="$opsi['ya_tidak']"
                                  :nilai="$v('jatuh.' . $kunci, 'tidak')" />
                @endforeach
            </div>
            <p class="small rs-text-muted mb-0 mt-2">
                Interpretasi hasil (otomatis): <strong>Tidak berisiko</strong> = tidak ada jawaban "Ya" ·
                <strong>Risiko rendah</strong> = ditemukan 1 jawaban "Ya" · <strong>Risiko tinggi</strong> = ditemukan 2 jawaban "Ya".
            </p>
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-1">Pengkajian status fungsional <span class="fw-normal rs-text-muted">(diisi oleh perawat)</span></h3>
            <div class="rs-aak-daftar">
                <x-form.baris label="1. Penggunaan alat bantu" name="alat_bantu" :opsi="$opsi['ya_tidak']" :nilai="$v('alat_bantu')"
                              isian="alat_bantu_ket" :isian-nilai="$v('alat_bantu_ket')" isian-saat="ya" placeholder="Sebutkan alat bantu…" />
                <x-form.baris label="2. ADL (Activities of Daily Living)" name="adl" :opsi="$opsi['adl']" :nilai="$v('adl')" />
            </div>
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-1">Pengkajian status nutrisional <span class="fw-normal rs-text-muted">(diisi oleh perawat)</span></h3>
            <div class="rs-aak-daftar">
                <x-form.baris label="1. Pasien penurunan berat badan?" name="nutrisi_bb" :opsi="$opsi['ya_tidak']" :nilai="$v('nutrisi_bb')" />
                <x-form.baris label="2. Pasien penurunan nafsu makan?" name="nutrisi_makan" :opsi="$opsi['ya_tidak']" :nilai="$v('nutrisi_makan')" />
            </div>
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-1">Pengkajian status psikologi <span class="fw-normal rs-text-muted">(diisi oleh perawat)</span></h3>
            <div class="rs-aak-daftar">
                <x-form.baris label="Status psikologi" name="psikologi" :opsi="$opsi['psikologi']" :nilai="$v('psikologi')"
                              isian="psikologi_lain" :isian-nilai="$v('psikologi_lain')" isian-saat="lainnya" />
            </div>
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-1">Status sosial, ekonomi, kultural dan spiritual <span class="fw-normal rs-text-muted">(diisi oleh perawat)</span></h3>
            <div class="rs-aak-daftar">
                <x-form.baris label="Kendala komunikasi" name="kendala_komunikasi" :opsi="$opsi['ya_tidak']" :nilai="$v('kendala_komunikasi')"
                              isian="kendala_komunikasi_ket" :isian-nilai="$v('kendala_komunikasi_ket')" isian-saat="ya" />
                <x-form.baris label="Yang merawat di rumah" name="perawat_rumah" :opsi="$opsi['ya_tidak']" :nilai="$v('perawat_rumah')"
                              isian="perawat_rumah_ket" :isian-nilai="$v('perawat_rumah_ket')" isian-saat="ya" placeholder="Sebutkan siapa…" />
                <x-form.baris label="Jaminan kesehatan" name="jaminan" :opsi="$opsi['jaminan']" :nilai="$v('jaminan', $jaminanBawaan)"
                              isian="jaminan_ket" :isian-nilai="$v('jaminan_ket')" placeholder="Keterangan (mis. nama perusahaan/asuransi)" />
                <x-form.baris label="Apakah ada tindakan yang bertentangan dengan kepercayaan pasien?" name="kepercayaan"
                              :opsi="$opsi['ya_tidak']" :nilai="$v('kepercayaan')"
                              isian="kepercayaan_ket" :isian-nilai="$v('kepercayaan_ket')" isian-saat="ya" />
            </div>
        </section>
    </div>

    {{-- ================= TAB 2: ASSESSMENT ================= --}}
    <div id="langkah-1" role="tabpanel" aria-labelledby="tab-langkah-1" data-rs-langkah-panel class="d-flex flex-column gap-3" hidden>
        <section class="rs-card" data-rs-diagnosa>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h3 class="h6 fw-bold mb-0 rs-langkah-judul" tabindex="-1">
                    Diagnosis keperawatan <span class="fw-normal rs-text-muted">(diisi oleh perawat)</span>
                    <span class="text-danger" aria-hidden="true">*</span>
                </h3>
                <span class="rs-pill-count" aria-live="polite"><span data-rs-diagnosa-jumlah>0</span> dipilih</span>
            </div>
            <x-form.centang name="diagnosa" label="Diagnosis keperawatan" :opsi="$opsi['diagnosa']" :nilai="$v('diagnosa', [])"
                            isian="diagnosa_lain" :isian-nilai="$v('diagnosa_lain')" />
        </section>
    </div>

    {{-- ================= TAB 3: PLANNING ================= --}}
    <div id="langkah-2" role="tabpanel" aria-labelledby="tab-langkah-2" data-rs-langkah-panel class="d-flex flex-column gap-3" hidden>
        <section class="rs-card">
            <h3 class="h6 fw-bold mb-1 rs-langkah-judul" tabindex="-1">
                Intervensi keperawatan <span class="fw-normal rs-text-muted">(diisi oleh perawat)</span>
            </h3>
            @foreach ($opsi['intervensi'] as $kunci => [$judul, $daftar])
                <div class="rs-aak-kelompok">
                    <p class="rs-aak-kelompok-judul">{{ $judul }}</p>
                    <x-form.centang name="intervensi_{{ $kunci }}" :label="$judul" :opsi="$daftar"
                                    :nilai="$v('intervensi_' . $kunci, [])"
                                    isian="intervensi_{{ $kunci }}_lain" :isian-nilai="$v('intervensi_' . $kunci . '_lain')" />
                </div>
            @endforeach
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-1">Discharge planning <span class="fw-normal rs-text-muted">(diisi oleh dokter/perawat)</span></h3>
            <p class="small rs-text-muted mb-2">Apakah pasien memiliki kriteria seperti di bawah ini:</p>
            <div class="rs-aak-daftar">
                @foreach ($opsi['discharge'] as $kunci => $pertanyaan)
                    <x-form.baris :label="$pertanyaan" name="discharge[{{ $kunci }}]" :opsi="$opsi['ya_tidak']"
                                  :nilai="$v('discharge.' . $kunci)" />
                @endforeach
            </div>
            <div class="rs-aak-kelompok">
                <p class="rs-aak-kelompok-judul">Edukasi</p>
                <x-form.centang name="edukasi" label="Edukasi discharge planning" :opsi="$opsi['edukasi']" :nilai="$v('edukasi', [])" />
            </div>
        </section>

        <section class="rs-card">
            <label for="edukasi_lain" class="h6 fw-bold d-block mb-3">
                Edukasi lainnya <span class="fw-normal rs-text-muted">(diisi oleh dokter/perawat)</span>
            </label>
            <textarea id="edukasi_lain" name="edukasi_lain" rows="4" class="form-control"
                      placeholder="mis. edukasi minum cukup, tanda bahaya demam">{{ $v('edukasi_lain') }}</textarea>
        </section>

        <section class="rs-card">
            <h3 class="h6 fw-bold mb-3">Perawat yang melakukan asesmen</h3>
            <div class="row">
                <div class="col-md-6">
                    {{-- TODO (IT RSCC): tanda tangan elektronik + nama perawat dari akun yang login --}}
                    <div class="rs-signature">Ditandatangani otomatis saat disimpan final</div>
                    <p class="small rs-text-muted mt-2 mb-0">[Nama Perawat], A.Md.Kep</p>
                </div>
            </div>
        </section>
    </div>
</form>

{{-- ===== Bar aksi (di luar form; tombol memakai form="form-asesmen") ===== --}}
<div class="rs-action-bar">
    <span class="small rs-text-muted" data-rs-status-simpan>
        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>{{ $status ? ucfirst($keterangan) : 'Belum disimpan' }}
    </span>
    <div class="rs-action-bar-buttons">
        <button type="button" class="btn btn-outline-secondary" data-rs-langkah-prev>
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Sebelumnya
        </button>
        <button type="submit" form="form-asesmen" name="aksi" value="draf" class="btn btn-outline-secondary">
            <i class="bi bi-save me-1" aria-hidden="true"></i>Simpan draf
        </button>
        <button type="button" class="btn btn-primary" data-rs-langkah-next>
            Lanjut<i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
        </button>
        <button type="submit" form="form-asesmen" name="aksi" value="final" class="btn btn-success" data-rs-langkah-final hidden>
            <i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Simpan final
        </button>
    </div>
</div>
@endsection