@props(['noReg', 'aktif' => ''])

@php
    $jumlah = \App\Support\KatalogDokumen::jumlah();

    // href '#' = halaman belum dibuat → tampil dengan label "Segera"
    $grup = [
        ['judul' => 'Asesmen & pemeriksaan', 'ikon' => 'bi-clipboard2-pulse', 'items' => [
            ['key' => 'ringkasan', 'label' => 'Ringkasan pasien', 'href' => route('rekam-medis.show', $noReg)],
            ['key' => 'asesmen',   'label' => 'Asesmen awal',     'href' => '#'],
            ['key' => 'ttv',       'label' => 'Tanda vital',      'href' => '#'],
        ]],
        ['judul' => 'Catatan terintegrasi', 'ikon' => 'bi-journal-text', 'items' => [
            ['key' => 'cppt', 'label' => 'CPPT / SBAR', 'href' => '#'],
        ]],
        ['judul' => 'Penunjang', 'ikon' => 'bi-eyedropper', 'items' => [
            ['key' => 'permintaan', 'label' => 'Permintaan lab / radiologi','href' => route('rekam-medis.permintaan', $noReg)],
            ['key' => 'berkas',     'label' => 'Berkas digital',             'href' => '#'],
        ]],
        ['judul' => 'Farmasi', 'ikon' => 'bi-capsule', 'items' => [
            ['key' => 'resep', 'label' => 'E-Resep', 'href' => '#'],
        ]],
        ['judul' => 'Riwayat', 'ikon' => 'bi-clock-history', 'items' => [
            ['key' => 'riwayat', 'label' => 'Riwayat pemeriksaan', 'href' => '#'],
        ]],
        ['judul' => 'Dokumen', 'ikon' => 'bi-folder2-open', 'items' => [
            ['key' => 'surat',       'label' => 'Pembuatan surat', 'jumlah' => $jumlah['surat'],
             'href' => route('rekam-medis.dokumen', [$noReg, 'surat'])],
            ['key' => 'persetujuan', 'label' => 'Persetujuan',     'jumlah' => $jumlah['persetujuan'],
             'href' => route('rekam-medis.dokumen', [$noReg, 'persetujuan'])],
            ['key' => 'cetak',       'label' => 'Cetak',           'href' => route('rekam-medis.cetak', $noReg)],
        ]],
        ['judul' => 'Mutu', 'ikon' => 'bi-shield-check', 'items' => [
            ['key' => 'ppi', 'label' => 'PPI',               'href' => '#'],
            ['key' => 'cp',  'label' => 'Clinical pathways', 'href' => '#'],
        ]],
    ];
@endphp

<nav class="rs-rmmenu" aria-label="Menu rekam medis">
    <p class="rs-rmmenu-title">Menu rekam medis</p>
    <x-search label="Cari form" size="sm" class="mb-2" placeholder="Cari form…" />

    @foreach ($grup as $g)
        <div class="rs-rmmenu-group">
            <p class="rs-rmmenu-label">
                <i class="bi {{ $g['ikon'] }}" aria-hidden="true"></i>{{ $g['judul'] }}
            </p>
            @foreach ($g['items'] as $it)
                @php $segera = $it['href'] === '#'; @endphp
                <a href="{{ $it['href'] }}"
                   @class(['rs-rmmenu-item', 'active' => $aktif === $it['key'], 'is-soon' => $segera])
                   @if ($aktif === $it['key']) aria-current="page" @endif
                   @if ($segera) title="Belum tersedia" @endif>
                    <span>{{ $it['label'] }}</span>
                    @if ($segera)
                        <span class="rs-soon">Segera</span>
                    @elseif (isset($it['jumlah']))
                        <span class="rs-rmmenu-count">{{ $it['jumlah'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endforeach
</nav>