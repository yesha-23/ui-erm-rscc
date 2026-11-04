@extends('layouts.rekam-medis')
@section('title', 'Tanda vital')

@section('content')
@php
    use App\Support\RentangTtv;
    $angka = fn ($n) => RentangTtv::angka($n);
    $panah = fn ($st) => $st === 'tinggi' ? ' ↑' : ($st === 'rendah' ? ' ↓' : '');
@endphp

{{-- ===== Judul ===== --}}
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
    <div>
        <p class="rs-eyebrow mb-1"><i class="bi bi-heart-pulse me-1" aria-hidden="true"></i>Asesmen & pemeriksaan</p>
        <h2 class="h4 fw-bold mb-0">Tanda vital</h2>
        <p class="small rs-text-muted mb-0 mt-1">
            {{ $observasi->count() }} kali observasi · terakhir {{ $terakhir['waktu'] ?? '–' }}
        </p>
    </div>
    <button type="button" @class(['btn btn-primary', 'collapsed' => ! $errors->any()])
            data-bs-toggle="collapse" data-bs-target="#formObservasi"
            aria-expanded="{{ $errors->any() ? 'true' : 'false' }}" aria-controls="formObservasi">
        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Tambah observasi
    </button>
</div>

{{-- ===== Form observasi baru (terlipat) ===== --}}
<div id="formObservasi" @class(['collapse', 'show' => $errors->any()]) data-rs-focus-on-show>
    <form method="POST" action="{{ route('rekam-medis.ttv.simpan', $pasien['no_reg']) }}" novalidate class="rs-card mb-3">
        @csrf
        <h3 class="h6 fw-bold mb-3">Observasi baru</h3>

        @if ($errors->any())
            <div class="alert alert-danger small d-flex gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
                <div>@foreach ($errors->all() as $pesan)<div>{{ $pesan }}</div>@endforeach</div>
            </div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-sm-4">
                <label for="waktu" class="form-label">Waktu <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="datetime-local" id="waktu" name="waktu" value="{{ old('waktu', now()->format('Y-m-d\TH:i')) }}"
                       @class(['form-control', 'is-invalid' => $errors->has('waktu')])>
            </div>
            <div class="col-sm-4">
                <label for="kesadaran" class="form-label">Kesadaran <span class="text-danger" aria-hidden="true">*</span></label>
                <select id="kesadaran" name="kesadaran" @class(['form-select', 'is-invalid' => $errors->has('kesadaran')])>
                    @foreach ($kesadaran as $kode => $label)
                        <option value="{{ $kode }}" @selected(old('kesadaran', 'cm') === $kode)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <label for="nyeri" class="form-label">Skala nyeri (0–10)</label>
                <select id="nyeri" name="nyeri" class="form-select">
                    <option value="">Tidak dinilai</option>
                    @for ($n = 0; $n <= 10; $n++)
                        <option value="{{ $n }}" @selected((string) old('nyeri') === (string) $n)>{{ $n }}</option>
                    @endfor
                </select>
            </div>
        </div>

        {{-- Komponen buatan Clara, dipakai ulang --}}
        <x-form.ttv :isi="[]" />

        <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#formObservasi">Batal</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1" aria-hidden="true"></i>Simpan observasi</button>
        </div>
    </form>
</div>

@if (! $terakhir)
    <div class="rs-card rs-empty">
        <p class="rs-text-muted mb-0">Belum ada observasi. Klik "Tambah observasi".</p>
    </div>
@else
    {{-- ===== Ringkasan terakhir ===== --}}
    <section class="mb-3" aria-label="Tanda vital terakhir">
        <h3 class="h6 fw-bold mb-2">Terakhir · {{ $terakhir['waktu'] }}</h3>
        <div class="rs-ttv-ringkas">
            @foreach ($kolom as $k)
                @php
                    $nilai = $terakhir[$k['name']] ?? null;
                    $lama  = $sebelumnya[$k['name']] ?? null;
                    $st    = RentangTtv::status($k['name'], $nilai);
                    $arah  = (filled($nilai) && filled($lama)) ? ((float) $nilai <=> (float) $lama) : 0;
                @endphp
                <div @class(['rs-ttv-kartu', 'is-warn' => $st])>
                    <span class="rs-ttv-kartu-label">{{ $k['label'] }}</span>
                    <span class="rs-ttv-kartu-nilai">{{ $angka($nilai) }} <small>{{ $k['unit'] }}</small></span>
                    <span class="rs-ttv-kartu-catatan">
                        @if ($st === 'tinggi') ↑ Di atas normal
                        @elseif ($st === 'rendah') ↓ Di bawah normal
                        @else ✓ Normal
                        @endif
                        @if ($arah)
                            <span class="d-block fw-normal">{{ $arah > 0 ? 'Naik' : 'Turun' }} dari {{ $angka($lama) }}</span>
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===== Grafik tren ===== --}}
    <section class="rs-card mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h3 class="h6 fw-bold mb-0">Tren</h3>
            <div class="rs-segmented rs-segmented-inline" role="radiogroup" aria-label="Pilih tanda vital">
                @foreach (['suhu' => 'Suhu', 'nadi' => 'Nadi', 'td' => 'Tekanan darah', 'napas' => 'Napas', 'spo2' => 'SpO₂'] as $kode => $label)
                    <input type="radio" class="btn-check" name="grafik-ttv" id="grafik-{{ $kode }}" value="{{ $kode }}"
                           autocomplete="off" @checked($kode === 'suhu')>
                    <label for="grafik-{{ $kode }}" class="rs-segmented-item">{{ $label }}</label>
                @endforeach
            </div>
        </div>
        <div class="rs-grafik-ttv">
            <canvas data-rs-ttv-grafik role="img" aria-label="Grafik tren tanda vital. Rincian angka ada di tabel observasi."></canvas>
        </div>
        <p class="small rs-text-muted mt-2 mb-0">Garis putus-putus = batas normal dewasa (acuan; sesuaikan SPO RS).</p>
    </section>
    <script type="application/json" id="data-ttv">@json($grafik)</script>

    {{-- ===== Tabel observasi ===== --}}
    <section class="rs-card p-0 overflow-hidden">
        <div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-2">
            <h3 class="h6 fw-bold mb-0">Semua observasi</h3>
            <span class="rs-pill-count">{{ $observasi->count() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table rs-table rs-table-kartu rs-table-ringkas rs-table-tanpa-aksi mb-0">
                <thead>
                    <tr>
                        <th scope="col">Waktu</th>
                        <th scope="col">TD (mmHg)</th>
                        <th scope="col">Nadi</th>
                        <th scope="col">Napas</th>
                        <th scope="col">Suhu (°C)</th>
                        <th scope="col">SpO₂ (%)</th>
                        <th scope="col">Nyeri</th>
                        <th scope="col">Kesadaran</th>
                        <th scope="col">Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($observasi as $o)
                        @php
                            $sel = function (string $name) use ($o, $angka, $panah) {
                                $st = RentangTtv::status($name, $o[$name] ?? null);
                                $kelas = $st === 'tinggi' ? 'rs-nilai-tinggi' : ($st === 'rendah' ? 'rs-nilai-rendah' : '');
                                return '<span class="' . $kelas . '">' . e($angka($o[$name] ?? null) . $panah($st)) . '</span>';
                            };
                        @endphp
                        <tr>
                            <td class="rs-td-utama"><span class="fw-semibold">{{ $o['waktu'] }}</span></td>
                            <td data-label="TD (mmHg)"><span>{!! $sel('td_sistolik') !!} / {!! $sel('td_diastolik') !!}</span></td>
                            <td data-label="Nadi">{!! $sel('nadi') !!}</td>
                            <td data-label="Napas">{!! $sel('napas') !!}</td>
                            <td data-label="Suhu (°C)">{!! $sel('suhu') !!}</td>
                            <td data-label="SpO₂ (%)">{!! $sel('spo2') !!}</td>
                            <td data-label="Nyeri"><span>{{ filled($o['nyeri'] ?? null) ? $o['nyeri'] . '/10' : '–' }}</span></td>
                            <td data-label="Kesadaran"><span>{{ $kesadaran[$o['kesadaran']] ?? '–' }}</span></td>
                            <td data-label="Petugas"><span>{{ $o['oleh'] }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif
@endsection