{{-- Cari menu (Ctrl+K / ⌘K): halaman umum + menu pasien + semua surat & persetujuan --}}
@props(['pasien' => null])

@php
    $item = [];
    $tambah = function (string $kelompok, string $label, string $url, string $ikon) use (&$item) {
        $item[] = compact('kelompok', 'label', 'url', 'ikon');
    };

    if (\Route::has('beranda')) {
        $tambah('Halaman', 'Beranda', route('beranda'), 'bi-house');
    }
    foreach (\App\Support\Navigasi::pelayanan() as $n) {
        if (! empty($n['route']) && \Route::has($n['route'])) {
            $tambah('Pelayanan', $n['label'], route($n['route']), 'bi-hospital');
        }
    }
    foreach (['akun.profil' => 'Profil', 'akun.inbox' => 'Inbox', 'akun.pengaturan' => 'Pengaturan'] as $nama => $label) {
        if (\Route::has($nama)) {
            $tambah('Akun', $label, route($nama), 'bi-person');
        }
    }

    if ($pasien) {
        foreach (\App\Support\MenuRekamMedis::grup($pasien['no_reg']) as $g) {
            foreach ($g['items'] as $it) {
                if ($it['href'] !== '#') {
                    $tambah('Pasien · ' . $pasien['nama'], $it['label'], $it['href'], $g['ikon']);
                }
            }
        }
        foreach (['surat', 'persetujuan'] as $jenis) {
            foreach (\App\Support\KatalogDokumen::semua($jenis) as $d) {
                $tambah(ucfirst($jenis), $d['nama'],
                    route('rekam-medis.dokumen', [$pasien['no_reg'], $jenis, $d['slug']]), 'bi-file-earmark-text');
            }
        }
    }
@endphp

<div class="modal fade rs-cari-menu" id="cariMenu" tabindex="-1" aria-labelledby="judulCariMenu" aria-hidden="true" data-rs-cari-menu>
    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header gap-2">
                <h2 class="visually-hidden" id="judulCariMenu">Cari menu</h2>
                <x-search label="Cari menu" class="flex-grow-1" autocomplete="off" data-rs-cari-menu-input
                          placeholder="Cari menu, form, atau dokumen… (mis. surat sakit)" />
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-2">
                <ul class="list-unstyled mb-0">
                    @foreach ($item as $it)
                        <li data-rs-cari-item data-teks="{{ mb_strtolower($it['label'] . ' ' . $it['kelompok']) }}">
                            <a href="{{ $it['url'] }}" class="rs-cari-menu-item">
                                <i class="bi {{ $it['ikon'] }}" aria-hidden="true"></i>
                                <span class="flex-grow-1">{{ $it['label'] }}</span>
                                <small>{{ $it['kelompok'] }}</small>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <p class="text-center rs-text-muted small py-4 mb-0" data-rs-cari-menu-kosong hidden>Tidak ada menu yang cocok.</p>
            </div>
            <div class="modal-footer justify-content-start small rs-text-muted rs-kbd">
                <kbd>↑</kbd><kbd>↓</kbd> pilih · <kbd>Enter</kbd> buka · <kbd>Esc</kbd> tutup
            </div>
        </div>
    </div>
</div>