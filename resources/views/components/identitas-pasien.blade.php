@props(['pasien'])

{{--
  Kartu Identitas pasien di Ringkasan (revisi 10 Nov, mengikuti isi ERM lama).
  - Badge penjamin (BPJS Kesehatan / Umum Tunai) + kelas.
  - 3 kotak atas selalu tampil: Profil, Registrasi & SEP, Alergi & status khusus.
  - Detail bisa diringkas; pilihan diingat (localStorage 'rs-identitas').
  - NIK, No. BPJS, No. HP, alamat disensor; satu tombol "Tampilkan data" membuka semuanya
    sampai pindah pasien (sessionStorage 'rs-sensor-buka'). Diatur initIdentitasPasien di rscc.js.
--}}

@php
    $noReg = $pasien['no_reg'];
    $jkn   = $pasien['penjamin'] === 'JKN';
    $penjamin = $jkn ? 'BPJS Kesehatan' : 'Umum Tunai';

    // Sensor tampilan: 4 karakter awal + titik + 4 karakter akhir.
    // TODO (IT RSCC): data asli masih ikut dikirim ke halaman. Untuk perlindungan sungguhan,
    // ambil data asli lewat endpoint saat tombol "Tampilkan data" ditekan dan catat log aksesnya.
    $sensor = function (?string $s): string {
        $s = trim((string) $s);
        $n = mb_strlen($s);
        if ($n === 0 || $s === '—') return '—';
        if ($n <= 8) return '••••••';
        return mb_substr($s, 0, 4) . str_repeat('•', min(8, $n - 8)) . mb_substr($s, -4);
    };
    $rahasia = fn (?string $s) => new \Illuminate\Support\HtmlString(
        '<span class="rs-sensor">'
        . '<span class="rs-sensor-tutup" aria-hidden="true">' . e($sensor($s)) . '</span>'
        . '<span class="visually-hidden rs-sensor-sr">disembunyikan</span>'
        . '<span class="rs-sensor-asli">' . e($s ?: '—') . '</span>'
        . '</span>'
    );

    // [modifier kotak, ikon, judul status, keterangan]
    $alergi = match ($pasien['alergi_status'] ?? null) {
        'ada'   => ['is-ada', 'bi-exclamation-triangle-fill', 'Ada alergi', $pasien['alergi']],
        'tidak' => ['is-tidak', 'bi-check-circle-fill', 'Tidak ada alergi', null],
        default => ['is-belum', 'bi-question-circle-fill', 'Belum dikaji', 'Tanyakan ke pasien atau keluarga, lalu simpan.'],
    };
@endphp

<section class="rs-card rs-idp" aria-labelledby="judul-identitas" data-rs-identitas="{{ $noReg }}">
    <div class="rs-idp-kepala">
        <div class="d-flex align-items-center gap-2">
            <span class="rs-rk-ikon" aria-hidden="true"><i class="bi bi-person-vcard"></i></span>
            <div>
                <h3 id="judul-identitas" class="h6 fw-bold mb-0">Identitas pasien</h3>
                <p class="rs-idp-sub mb-0">Ringkasan data registrasi &amp; profil</p>
            </div>
        </div>

        <div class="rs-idp-alat">
            <x-badge :jenis="$jkn ? 'jkn' : 'nonjkn'">{{ $penjamin }}</x-badge>
            <x-badge jenis="netral">{{ $pasien['kelas_hak'] }}</x-badge>

            <button type="button" class="btn btn-outline-secondary btn-sm" data-rs-sensor-toggle aria-pressed="false">
                <i class="bi bi-eye me-1" aria-hidden="true" data-rs-sensor-ikon></i><span data-rs-sensor-label>Tampilkan data</span>
            </button>

            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                    data-bs-target="#identitasDetail" aria-expanded="true" aria-controls="identitasDetail"
                    data-rs-identitas-toggle>
                <span class="rs-saat-buka"><i class="bi bi-chevron-up me-1" aria-hidden="true"></i>Ringkas</span>
                <span class="rs-saat-tutup"><i class="bi bi-chevron-down me-1" aria-hidden="true"></i>Tampilkan lengkap</span>
            </button>
        </div>
    </div>

    {{-- Tiga kotak atas: selalu tampil --}}
    <div class="rs-idp-strip">
        <div class="rs-idp-kotak">
            <p class="rs-idp-label">Profil</p>
            <p class="rs-idp-utama">{{ $pasien['jk'] }} · {{ $pasien['umur'] }}</p>
            <p class="mb-0">{{ $pasien['tempat_lahir'] }}, {{ $pasien['tgl_lahir'] }}</p>
            <p class="mb-0">
                <i class="bi bi-telephone me-1 rs-text-muted" aria-hidden="true"></i><span class="visually-hidden">No. HP: </span>{{ $rahasia($pasien['no_hp']) }}
            </p>
        </div>

        <div class="rs-idp-kotak">
            <p class="rs-idp-label">No. Registrasi</p>
            <p class="rs-idp-nomor">{{ $noReg }}</p>

            <p class="rs-idp-label mt-2">No. SEP</p>
            @if ($pasien['no_sep'])
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="rs-idp-nomor rs-idp-nomor-sm">{{ $pasien['no_sep'] }}</span>
                    {{-- TODO (IT RSCC): sambungkan ke fungsi Cetak SEP di sistem lama --}}
                    <button type="button" class="btn btn-outline-secondary btn-sm rs-idp-ikon-tombol"
                            aria-label="Cetak SEP" title="Cetak SEP">
                        <i class="bi bi-printer" aria-hidden="true"></i>
                    </button>
                </div>
            @elseif ($jkn)
                <x-badge jenis="peringatan">Belum cetak SEP</x-badge>
            @else
                <span class="rs-text-muted">— (bukan JKN)</span>
            @endif
        </div>

        <div class="rs-idp-kotak rs-idp-aman {{ $alergi[0] }}">
            <div class="d-flex justify-content-between align-items-center gap-2">
                <p class="rs-idp-label mb-0">Alergi</p>
                <button type="button" class="btn btn-link btn-sm p-0 fw-bold" data-bs-toggle="modal" data-bs-target="#modalAlergi">
                    Ubah<span class="visually-hidden"> riwayat alergi</span>
                </button>
            </div>
            <p class="rs-idp-utama rs-idp-alergi-status">
                <i class="bi {{ $alergi[1] }} me-1" aria-hidden="true"></i>{{ $alergi[2] }}
            </p>
            @if ($alergi[3])
                <p class="small mb-0 rs-idp-alergi-ket">{{ $alergi[3] }}</p>
            @endif
            @if ($pasien['alergi_waktu'])
                <p class="small rs-text-muted mb-0">Diperbarui {{ $pasien['alergi_waktu'] }}</p>
            @endif

            {{-- Status khusus: selalu abu-abu dan tertutup, apa pun isinya --}}
            <div class="rs-idp-khusus">
                <i class="bi bi-shield-lock" aria-hidden="true"></i>
                <span class="flex-grow-1"><strong>Status khusus</strong> · Terbatas</span>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                        data-bs-target="#statusKhusus" aria-expanded="false" aria-controls="statusKhusus">
                    <span class="rs-saat-tutup">Tampilkan</span><span class="rs-saat-buka">Sembunyikan</span>
                </button>
            </div>
            <div class="collapse" id="statusKhusus">
                <div class="rs-rk-rahasia mt-2">
                    <div>
                        <div><strong>HIV / B20:</strong> {{ $pasien['status_hiv'] }}</div>
                        <p class="small rs-text-muted mb-0">
                            Informasi rahasia, hanya untuk PPA yang merawat pasien ini.
                            Catatan untuk IT: batasi berdasarkan peran pengguna dan simpan log setiap akses.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail lengkap (bisa diringkas) --}}
    <div class="collapse show" id="identitasDetail">
        <dl class="rs-idp-detail">
            <div><dt>NIK</dt><dd>{{ $rahasia($pasien['nik']) }}</dd></div>
            <div><dt>Pekerjaan</dt><dd>{{ $pasien['pekerjaan'] }}</dd></div>
            <div><dt>Agama</dt><dd>{{ $pasien['agama'] }}</dd></div>
            <div><dt>Status kawin</dt><dd>{{ $pasien['status_kawin'] }}</dd></div>
            <div><dt>No. BPJS</dt><dd>{{ $jkn ? $rahasia($pasien['no_bpjs']) : '—' }}</dd></div>
            <div><dt>Daftar / jam</dt><dd>{{ $pasien['datang'] }}</dd></div>
            <div><dt>Alamat</dt><dd>{{ $rahasia($pasien['alamat']) }}</dd></div>
            <div><dt>Alamat domisili</dt><dd>{{ $rahasia($pasien['alamat_domisili']) }}</dd></div>
            <div><dt>Penjaminan</dt><dd>{{ $penjamin }} · {{ $pasien['kelas_hak'] }}</dd></div>
            <div>
                <dt>DPJP</dt>
                <dd>
                    {{ $pasien['dpjp_nama'] }}
                    {{-- TODO (IT RSCC): tampilkan tombol Ubah hanya untuk peran admisi; simpan & catat perubahan DPJP --}}
                    <button type="button" class="btn btn-link btn-sm p-0 ms-1 fw-bold align-baseline" data-bs-toggle="collapse"
                            data-bs-target="#ubahDpjp" aria-expanded="false" aria-controls="ubahDpjp">
                        <span class="rs-saat-tutup">Ubah</span><span class="rs-saat-buka">Batal</span><span class="visually-hidden"> DPJP</span>
                    </button>
                    <div class="collapse" id="ubahDpjp">
                        <label for="pilihDpjp" class="visually-hidden">Pilih DPJP</label>
                        <select id="pilihDpjp" class="form-select form-select-sm mt-2" aria-describedby="catatanDpjp">
                            <option selected>{{ $pasien['dpjp_nama'] }}</option>
                            <option>[Nama dokter lain]</option>
                        </select>
                        <p id="catatanDpjp" class="form-text mb-0">Contoh tampilan, belum tersimpan.</p>
                    </div>
                </dd>
            </div>
        </dl>
    </div>
    {{-- Pulihkan pilihan "Ringkas" SEBELUM tampil, supaya kartu tidak berkedip (disimpan oleh initIdentitasPasien) --}}
    <script>
        try {
            if (localStorage.getItem('rs-identitas') === 'ringkas') {
                document.getElementById('identitasDetail').classList.remove('show');
                const t = document.querySelector('[data-rs-identitas-toggle]');
                t.setAttribute('aria-expanded', 'false');
                t.classList.add('collapsed');
            }
        } catch (e) { /* abaikan */ }
    </script>
</section>