@props(['pasien'])

@php
    $noReg      = $pasien['no_reg'];
    $tugas      = \App\Support\KonfigurasiRekamMedis::tugas($pasien['unit_kode'], $noReg);
    $penghalang = \App\Support\KonfigurasiRekamMedis::penghalangFinal($tugas);
    $opsional   = array_filter($tugas, fn ($t) => ! $t['wajib'] && $t['status'] !== 'selesai');
    $final      = session("final.$noReg");
@endphp

<div class="modal fade" id="modalFinal" tabindex="-1" aria-labelledby="judulModalFinal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rs-modal">
            <div class="modal-header">
                <h2 class="modal-title h5 fw-bold" id="judulModalFinal">
                    {{ $final ? 'Kunjungan sudah final' : 'Selesaikan kunjungan' }}
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body">
                @if ($final)
                    <p class="mb-2">
                        <i class="bi bi-lock-fill text-success me-1" aria-hidden="true"></i>
                        Disimpan final pukul <strong>{{ $final['waktu'] }}</strong> oleh {{ $final['oleh'] }}.
                    </p>
                    <p class="small rs-text-muted mb-0">
                        Perlu koreksi? Buka kembali, perbaiki form-nya, lalu submit final lagi.
                    </p>
                @elseif ($penghalang)
                    <p class="mb-2">Masih ada <strong>{{ count($penghalang) }} form wajib</strong> yang belum selesai:</p>
                    <ul class="rs-rk-tugas">
                        @foreach ($penghalang as $t)
                            <li class="rs-rk-tugas-item is-{{ $t['status'] }}">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $t['label'] }}</div>
                                    <div class="small rs-text-muted">{{ $t['ket'] }}</div>
                                </div>
                                <a href="{{ $t['href'] }}" class="btn btn-primary btn-sm">Buka</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mb-2">
                        <i class="bi bi-check-circle-fill text-success me-1" aria-hidden="true"></i>
                        Semua form wajib sudah lengkap. Setelah final, kunjungan dianggap selesai (BRM terisi dan close).
                    </p>
                    @if ($opsional)
                        <p class="small rs-text-muted mb-0">
                            Opsional, belum diisi: {{ implode(', ', array_column($opsional, 'label')) }}.
                            Tidak menghalangi final.
                        </p>
                    @endif
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                @if ($final)
                    <form method="POST" action="{{ route('rekam-medis.final.batal', $noReg) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-unlock me-1" aria-hidden="true"></i>Buka kembali
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('rekam-medis.final', $noReg) }}">
                        @csrf
                        <button type="submit" class="btn btn-success" @disabled($penghalang)>
                            <i class="bi bi-check2 me-1" aria-hidden="true"></i>Submit final
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>