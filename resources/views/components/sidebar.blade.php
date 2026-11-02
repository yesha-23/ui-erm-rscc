@php
    $menu = \App\Support\Navigasi::pelayanan();
    $belumDibaca = collect(\App\Support\DataContoh::pesan())->where('dibaca', false)->count();
@endphp

<div class="rs-sidebar">
    <nav id="sidebarUtama" class="offcanvas-lg offcanvas-start rs-sidebar-inner" tabindex="-1" aria-label="Menu utama">

        {{-- Bagian atas: tetap di tempat --}}
        <div class="rs-sidebar-head">
            <a class="rs-brand" href="{{ route('beranda') }}">
                <img src="{{ asset('images/logo-rscc.png') }}" alt="Logo RSCC" width="40" height="34" class="rs-logo">
                <span class="rs-menu-text"><strong>EMR RSCC</strong><small>RS Condong Catur</small></span>
            </a>
            <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas"
                    data-bs-target="#sidebarUtama" aria-label="Tutup menu"></button>
        </div>

        {{-- Bagian tengah: satu-satunya yang boleh digulir bila layar pendek --}}
        <div class="rs-sidebar-menu">
            <a href="{{ route('beranda') }}" title="Beranda"
               @class(['rs-menu-item', 'active' => request()->routeIs('beranda')])>
                <i class="bi bi-house" aria-hidden="true"></i><span class="rs-menu-text">Beranda</span>
            </a>

            <p class="rs-menu-label">PELAYANAN</p>

            @foreach ($menu as $item)
                @php
                    $aktif = request()->routeIs($item['route']);
                @endphp

                @if (! isset($item['sub']))
                    <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
                       @class(['rs-menu-item', 'active' => $aktif])>
                        <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
                        <span class="rs-menu-text">{{ $item['label'] }}</span>
                    </a>
                @else
                    @php
                        $idSub = 'sub-' . \Illuminate\Support\Str::slug($item['label']);
                        $nilaiAktif = request()->route($item['param']) ?? $item['sub'][0]['value'];
                    @endphp

                    <button type="button" title="{{ $item['label'] }}"
                            @class(['rs-menu-item', 'rs-menu-parent', 'is-current' => $aktif, 'collapsed' => ! $aktif])
                            data-bs-toggle="collapse" data-bs-target="#{{ $idSub }}"
                            aria-expanded="{{ $aktif ? 'true' : 'false' }}" aria-controls="{{ $idSub }}">
                        <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
                        <span class="rs-menu-text">{{ $item['label'] }}</span>
                        <i class="bi bi-chevron-down rs-chevron rs-menu-text" aria-hidden="true"></i>
                    </button>

                    <div id="{{ $idSub }}" @class(['collapse', 'show' => $aktif])>
                        <div class="rs-submenu">
                            @foreach ($item['sub'] as $sub)
                                <a href="{{ route($item['route'], [$item['param'] => $sub['value']]) }}"
                                   @class(['rs-submenu-item', 'active' => $aktif && $nilaiAktif === $sub['value']])>
                                    {{ $sub['label'] }}
                                    <span class="rs-count">{{ $sub['jumlah'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Bagian bawah: tetap di tempat --}}
        <div class="rs-sidebar-foot">
            <a href="{{ route('akun.inbox') }}" title="Inbox"
               @class(['rs-menu-item', 'active' => request()->routeIs('akun.inbox')])>
                <i class="bi bi-inbox" aria-hidden="true"></i>
                <span class="rs-menu-text">Inbox</span>
                @if ($belumDibaca)
                    <span class="rs-badge-count">{{ $belumDibaca }}</span>
                @endif
            </a>
            <a href="{{ route('akun.pengaturan') }}" title="Pengaturan"
               @class(['rs-menu-item', 'active' => request()->routeIs('akun.pengaturan')])>
                <i class="bi bi-gear" aria-hidden="true"></i><span class="rs-menu-text">Pengaturan</span>
            </a>
            <a href="{{ route('login') }}" class="rs-menu-item rs-danger" title="Keluar">
                <i class="bi bi-box-arrow-left" aria-hidden="true"></i><span class="rs-menu-text">Keluar</span>
            </a>
            <button type="button" class="rs-menu-item rs-sidebar-toggle d-none d-lg-flex"
                    data-rs-toggle-sidebar title="Kecilkan / lebarkan menu">
                <i class="bi bi-chevron-double-left" aria-hidden="true"></i>
                <span class="rs-menu-text">Kecilkan menu</span>
            </button>
        </div>
    </nav>
</div>