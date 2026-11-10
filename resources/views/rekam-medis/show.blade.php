@extends('layouts.rekam-medis')
@section('title', 'Ringkasan')

@section('content')
    @php
        $noReg = $pasien['no_reg'];

        // Progres dihitung dari form WAJIB saja; form opsional tidak membuat progres terlihat tertinggal
        $wajib        = collect($tugas)->where('wajib', true);
        $wajibSelesai = $wajib->where('status', 'selesai')->count();
        $persen       = $wajib->count() ? round($wajibSelesai / $wajib->count() * 100) : 100;

        $ikonStatus = ['selesai' => 'bi-check-lg', 'draf' => 'bi-pencil', 'belum' => 'bi-exclamation-lg', 'opsional' => 'bi-plus-lg'];
        $teksStatus = ['selesai' => 'selesai', 'draf' => 'draf', 'belum' => 'belum diisi', 'opsional' => 'opsional, belum diisi'];
    @endphp

    <x-judul-form ikon="" :kelompok="'Ringkasan pasien · ' . $pasien['unit']" judul="Ringkasan kunjungan"
                  :kembali-ke="route($pasien['unit_route'], $pasien['unit_sub'] ? [$pasien['unit_sub']] : [])"
                  :label-kembali="'Kembali ke daftar kunjungan ' . $pasien['unit']">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('rekam-medis.dokumen', [$noReg, 'surat']) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-file-earmark-text me-1" aria-hidden="true"></i>Buat surat
            </a>
            <a href="{{ route('rekam-medis.cetak.resume', $noReg) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-printer me-1" aria-hidden="true"></i>Resume medis
            </a>
        </div>
    </x-judul-form>

    <div class="row g-3 rs-rk">
        {{-- 1. Identitas pasien (permintaan RS: paling atas, isi mengikuti ERM lama) --}}
        <div class="col-12">
            <x-identitas-pasien :pasien="$pasien" />
        </div>

        {{-- 2. Perlu dikerjakan --}}
        <div class="col-xl-7">
            <section class="rs-card h-100" aria-labelledby="judul-tugas">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                    <h3 id="judul-tugas" class="h6 fw-bold mb-0">Perlu dikerjakan</h3>
                    <span class="rs-pill-count">{{ $wajibSelesai }} dari {{ $wajib->count() }} wajib selesai</span>
                </div>
                <div class="progress rs-rk-progres mb-3" role="progressbar" aria-label="Kemajuan form wajib"
                     aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar" style="width: {{ $persen }}%"></div>
                </div>

                <ul class="rs-rk-tugas">
                    @foreach ($tugas as $t)
                        @php $st = (! $t['wajib'] && $t['status'] === 'belum') ? 'opsional' : $t['status']; @endphp
                        <li class="rs-rk-tugas-item is-{{ $st }}">
                            <span class="rs-rk-tugas-ikon" aria-hidden="true">
                                <i class="bi {{ $ikonStatus[$st] }}"></i>
                            </span>
                            <div class="flex-grow-1" style="min-width: 0">
                                <div class="fw-semibold">{{ $t['label'] }}</div>
                                <div class="small rs-text-muted">
                                    <span class="visually-hidden">Status: {{ $teksStatus[$st] }}.</span>{{ $t['ket'] }}
                                    @unless ($t['wajib']) <span class="rs-soon ms-1">Opsional</span> @endunless
                                </div>
                            </div>
                            @if ($t['href'] === '#')
                                <span class="rs-soon" title="Form belum tersedia">Segera</span>
                            @elseif ($t['status'] === 'selesai')
                                <a href="{{ $t['href'] }}" class="btn btn-outline-secondary btn-sm">Lihat</a>
                            @else
                                <a href="{{ $t['href'] }}" class="btn btn-primary btn-sm">
                                    {{ $t['status'] === 'draf' ? 'Lanjutkan' : 'Isi' }}
                                    <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>

        {{-- 3. Tanda vital terakhir (di samping Perlu dikerjakan) --}}
        <div class="col-xl-5">
            <section class="rs-card h-100" aria-labelledby="judul-ttv">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                    <h3 id="judul-ttv" class="h6 fw-bold mb-0">
                        Tanda vital terakhir <span class="rs-text-muted fw-normal">· {{ $ttv['waktu'] }}</span>
                    </h3>
                    <a href="{{ route('rekam-medis.ttv', $noReg) }}" class="small fw-semibold">
                        Lihat grafik <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
                <div class="rs-rk-ttv">
                    @foreach ($ttv['item'] as [$label, $nilai, $satuan, $flag])
                        <div @class(['rs-rk-ttv-item', 'is-tinggi' => $flag === 'tinggi', 'is-rendah' => $flag === 'rendah'])>
                            <span class="rs-rk-ttv-label">{{ $label }}</span>
                            <span class="rs-rk-ttv-nilai">
                                {{ $nilai }}
                                @if ($flag)
                                    <i class="bi bi-arrow-{{ $flag === 'tinggi' ? 'up' : 'down' }}" aria-hidden="true"></i>
                                    <span class="visually-hidden">({{ $flag }})</span>
                                @endif
                            </span>
                            <span class="rs-rk-ttv-satuan">{{ $satuan }}</span>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- 4. Kunjungan ini: kartu khusus unit + diagnosa kerja (gabungan kartu "Diagnosa & DPJP") --}}
        <div class="col-lg-5">
            <section class="rs-card h-100" aria-labelledby="judul-kartu-unit">
                <h3 id="judul-kartu-unit" class="h6 fw-bold d-flex align-items-center gap-2 mb-3">
                    <span class="rs-rk-ikon" aria-hidden="true"><i class="bi {{ $kartu['ikon'] }}"></i></span>
                    {{ $kartu['judul'] }}
                </h3>

                <div class="mb-3">
                    <x-badge :jenis="$kartu['sorot'][1]" class="rs-rk-sorot">{{ $kartu['sorot'][0] }}</x-badge>
                    @if ($kartu['progres'])
                        @php [$n, $maks] = $kartu['progres']; @endphp
                        <div class="progress rs-rk-progres mt-2" role="progressbar" aria-label="Sesi terapi"
                             aria-valuenow="{{ $n }}" aria-valuemin="0" aria-valuemax="{{ $maks }}">
                            <div class="progress-bar" style="width: {{ round($n / $maks * 100) }}%"></div>
                        </div>
                    @endif
                </div>

                <dl class="rs-rk-dl">
                    @foreach ($kartu['baris'] as $label => $nilai)
                        <dt>{{ $label }}</dt>
                        <dd>{{ $nilai }}</dd>
                    @endforeach
                    <dt>Diagnosa kerja</dt>
                    <dd @class(['fw-semibold', 'rs-rk-dx-kosong' => $pasien['diagnosa_kerja'] === 'Belum ada diagnosa'])>
                        {{ $pasien['diagnosa_kerja'] }}
                    </dd>
                </dl>
                <a href="{{ route('rekam-medis.cppt', $noReg) }}" class="small fw-semibold d-inline-block mt-3">
                    Buka CPPT <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </section>
        </div>

        {{-- 5. Aktivitas terakhir --}}
        <div class="col-lg-7">
            <section class="rs-card h-100" aria-labelledby="judul-aktivitas">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                    <h3 id="judul-aktivitas" class="h6 fw-bold mb-0">Aktivitas terakhir</h3>
                    <a href="{{ route('rekam-medis.riwayat', $noReg) }}" class="small fw-semibold">
                        Semua riwayat <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
                <ul class="rs-rk-aktivitas">
                    @foreach (array_slice($aktivitas, 0, 5) as $a)
                        <li>
                            <a href="{{ route('rekam-medis.' . $a['route'], $noReg) }}">
                                <span class="rs-rk-aktivitas-jam">{{ $a['jam'] }}</span>
                                <i class="bi {{ $a['ikon'] }}" aria-hidden="true"></i>
                                <span class="flex-grow-1">{{ $a['teks'] }}</span>
                                <span class="small rs-text-muted text-nowrap d-none d-sm-inline">{{ $a['oleh'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        </div>
    </div>
@endsection