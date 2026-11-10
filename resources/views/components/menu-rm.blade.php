@props(['noReg', 'aktif' => ''])

@php $grup = \App\Support\MenuRekamMedis::grup($noReg); @endphp

<nav class="rs-rmmenu" aria-label="Menu rekam medis" data-rs-rmmenu>
    {{-- Judul "Menu rekam medis" sekarang ada di tombol header (pasien-header) --}}
    <x-search label="Cari form" size="sm" placeholder="Cari form…" autocomplete="off" data-rs-rmmenu-cari />
    <button type="button" class="rs-rmmenu-pintasan" data-bs-toggle="modal" data-bs-target="#cariMenu">
        <i class="bi bi-lightning-charge" aria-hidden="true"></i>Cari dari mana saja:
        <span class="rs-kbd"><kbd>Ctrl</kbd><kbd>K</kbd></span>
    </button>

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

    <p class="small rs-text-muted text-center py-3 mb-0" data-rs-rmmenu-kosong hidden>Form tidak ditemukan.</p>
</nav>