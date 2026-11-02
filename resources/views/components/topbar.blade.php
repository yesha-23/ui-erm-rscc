@php
    $u = \App\Support\DataContoh::pengguna();
    $belumDibaca = collect(\App\Support\DataContoh::pesan())->where('dibaca', false)->count();
@endphp

<header class="rs-topbar">

    {{-- Tombol buka menu: hanya di layar kecil --}}
    <button type="button" class="btn btn-outline-secondary d-lg-none"
            data-bs-toggle="offcanvas" data-bs-target="#sidebarUtama"
            aria-controls="sidebarUtama" aria-label="Buka menu">
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    {{-- Kotak cari pasien --}}
    <form class="rs-topbar-search" role="search">
        <x-search label="Cari pasien" id="cari-pasien" placeholder="Cari pasien (nama / No. RM)…" />
    </form>

    {{-- Menu pengguna --}}
    <div class="dropdown ms-auto">
        <button type="button" class="rs-user-btn" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="rs-avatar" aria-hidden="true">{{ $u['inisial'] }}</span>
            <span class="d-none d-sm-flex flex-column lh-sm text-start">
                <span class="fw-bold small">{{ $u['nama_pendek'] }}</span>
                <span class="rs-text-muted small">{{ $u['jabatan'] }}</span>
            </span>
            <i class="bi bi-chevron-down small rs-text-muted" aria-hidden="true"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end rs-dropdown">
            <li><a class="dropdown-item" href="{{ route('akun.profil') }}"><i class="bi bi-person me-2" aria-hidden="true"></i>Profil saya</a></li>
            <li>
                <a class="dropdown-item d-flex align-items-center" href="{{ route('akun.inbox') }}">
                    <i class="bi bi-inbox me-2" aria-hidden="true"></i>Inbox
                    @if ($belumDibaca)
                        <span class="rs-badge-count">{{ $belumDibaca }}</span>
                    @endif
                </a>
            </li>
            <li><a class="dropdown-item" href="{{ route('akun.pengaturan') }}"><i class="bi bi-gear me-2" aria-hidden="true"></i>Pengaturan</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="{{ route('login') }}"><i class="bi bi-box-arrow-left me-2" aria-hidden="true"></i>Keluar</a></li>
        </ul>
    </div>
</header>