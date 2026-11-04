{{-- Pemeriksaan fisik per bagian tubuh. Kotak catatan hanya tampil bila "Ada temuan" (CSS :has). --}}
@props(['opsi', 'isi' => []])

<div data-rs-fisik>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <span class="small rs-text-muted" aria-live="polite">
            <strong data-rs-fisik-jumlah>0</strong> bagian ada temuan
        </span>
        <button type="button" class="btn btn-outline-secondary btn-sm" data-rs-fisik-normal>
            <i class="bi bi-check2-all me-1" aria-hidden="true"></i>Semua normal
        </button>
    </div>

    <ul class="rs-fisik list-unstyled mb-0">
        @foreach ($opsi['fisik'] as $kunci => $label)
            @php $nilai = old("fisik.$kunci", $isi['fisik'][$kunci] ?? 'normal'); @endphp
            <li class="rs-fisik-baris">
                <span class="rs-fisik-nama" id="fisik-{{ $kunci }}-judul">{{ $label }}</span>
                <div class="rs-fisik-pilih" role="radiogroup" aria-labelledby="fisik-{{ $kunci }}-judul">
                    <input type="radio" class="btn-check" autocomplete="off" name="fisik[{{ $kunci }}]"
                           id="fisik-{{ $kunci }}-normal" value="normal" @checked($nilai === 'normal')>
                    <label for="fisik-{{ $kunci }}-normal" class="rs-pilih rs-pilih-normal">Normal</label>

                    <input type="radio" class="btn-check" autocomplete="off" name="fisik[{{ $kunci }}]"
                           id="fisik-{{ $kunci }}-temuan" value="temuan" @checked($nilai === 'temuan')>
                    <label for="fisik-{{ $kunci }}-temuan" class="rs-pilih rs-pilih-temuan">Ada temuan</label>
                </div>
                <textarea name="fisik_catatan[{{ $kunci }}]" rows="2" class="form-control rs-fisik-catatan"
                          aria-label="Temuan {{ $label }}"
                          placeholder="Tulis temuan, mis. nyeri tekan (+)">{{ old("fisik_catatan.$kunci", $isi['fisik_catatan'][$kunci] ?? '') }}</textarea>
            </li>
        @endforeach
    </ul>
</div>