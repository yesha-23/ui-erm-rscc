@props(['kolom', 'cek' => [], 'rows'])

<div class="rs-card p-0 overflow-hidden">
    @if ($rows->isEmpty())
        {{-- Keadaan kosong --}}
        <div class="rs-empty">
            <span class="rs-empty-icon"><i class="bi bi-search" aria-hidden="true"></i></span>
            <h2 class="h6 fw-bold mb-1">Tidak ada kunjungan yang cocok</h2>
            <p class="rs-text-muted mb-3">Coba ubah kata kunci, tanggal, atau filter.</p>
            <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm">Hapus semua filter</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table rs-table mb-0">
                <thead>
                    <tr>
                        @foreach ($kolom as $k)
                            <th scope="col">{{ $k['label'] }}</th>
                        @endforeach
                        <th scope="col"><span class="visually-hidden">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $r)
                        @php
                            $inisial = collect(explode(' ', $r['nama']))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            $jkn = $r['penjamin'] === 'JKN';
                        @endphp
                        <tr>
                            @foreach ($kolom as $k)
                                <td>
                                    @switch($k['tipe'])
                                        @case('pasien')
                                            <div class="d-flex align-items-start gap-2">
                                                <span class="rs-avatar rs-avatar-pasien" aria-hidden="true">{{ $inisial }}</span>
                                                <div>
                                                    <a href="{{ route('rekam-medis.show', $r['no_reg']) }}" class="rs-link-pasien">{{ $r['nama'] }}</a>
                                                    <div class="small rs-text-muted">RM {{ $r['no_rm'] }} · Reg {{ $r['no_reg'] }}</div>
                                                    @if ($r['no_sep'])
                                                        <div class="small rs-sep">SEP {{ $r['no_sep'] }}</div>
                                                    @elseif ($jkn)
                                                        <x-badge jenis="belum" class="mt-1">Belum cetak SEP</x-badge>
                                                    @endif
                                                </div>
                                            </div>
                                            @break

                                        @case('daftar')
                                            <div class="fw-semibold">{{ $r['jam'] }}</div>
                                            <div class="small rs-text-muted">{{ $r['tgl'] }}</div>
                                            @break

                                        @case('unit')
                                            <div>{{ $r['unit_tujuan'] }}</div>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                <x-badge :jenis="$jkn ? 'jkn' : 'nonjkn'">{{ $r['penjamin'] }}</x-badge>
                                                <x-badge :jenis="$r['sudah_datang'] ? 'datang' : 'belum'">
                                                    {{ $r['sudah_datang'] ? 'Sudah datang' : 'Belum datang' }}
                                                </x-badge>
                                            </div>
                                            @break

                                        @case('kelas')
                                            <div class="fw-semibold">{{ $r['kelas'] }}</div>
                                            <x-badge :jenis="$jkn ? 'jkn' : 'nonjkn'" class="mt-1">{{ $r['penjamin'] }}</x-badge>
                                            @break

                                        @case('ruang')
                                            <div>{{ $r['ruang'] }}</div>
                                            <div class="small rs-text-muted">{{ $r['bed'] }}</div>
                                            @break

                                        @case('kelengkapan')
                                            <ul class="rs-cek-list">
                                                @foreach ($cek as $field => $label)
                                                    <li><x-status-cek :ok="$r[$field]" :label="$label" /></li>
                                                @endforeach
                                            </ul>
                                            @break

                                        @default
                                            {{ filled($r[$k['field']]) ? $r[$k['field']] : '—' }}
                                    @endswitch
                                </td>
                            @endforeach

                            <td class="text-end text-nowrap">
                                <a href="{{ route('rekam-medis.show', $r['no_reg']) }}" class="btn btn-primary btn-sm">Buka</a>
                                <div class="dropdown d-inline-block">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown"
                                            aria-expanded="false" aria-label="Aksi lain untuk {{ $r['nama'] }}">
                                        <i class="bi bi-three-dots" aria-hidden="true"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-clock-history me-2"></i>Riwayat kunjungan</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-printer me-2"></i>Cetak SEP</a></li>
                                        <li><a class="dropdown-item" href="#"><i class="bi bi-tag me-2"></i>Cetak label pasien</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="rs-table-foot">
            <span class="rs-text-muted small">
                Menampilkan {{ $rows->firstItem() }}–{{ $rows->lastItem() }} dari {{ $rows->total() }} kunjungan
            </span>
            {{ $rows->links() }}
        </div>
    @endif
</div>