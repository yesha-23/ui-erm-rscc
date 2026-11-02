<header class="rs-topbar">

    {{-- Tombol buka menu: hanya muncul di layar kecil (HP / tablet) --}}
    <button type="button" class="btn btn-outline-secondary d-lg-none"
            data-bs-toggle="offcanvas" data-bs-target="#sidebarUtama"
            aria-controls="sidebarUtama" aria-label="Buka menu">
        <i class="bi bi-list" aria-hidden="true"></i>
    </button>

    {{-- Kotak cari pasien --}}
    <form class="rs-topbar-search" role="search">
        <label for="cari-pasien" class="visually-hidden">Cari pasien</label>
        <div class="input-group">
            <span class="input-group-text rs-search-icon">
                <i class="bi bi-search" aria-hidden="true"></i>
            </span>
            <input id="cari-pasien" type="search" class="form-control rs-search-input"
                   placeholder="Cari pasien (nama / No. RM)…">
        </div>
    </form>

    {{-- Pengguna yang sedang login --}}
    <div class="ms-auto d-flex align-items-center gap-2">
        <span class="rs-avatar" aria-hidden="true">ZK</span>
        <span class="d-none d-sm-flex flex-column lh-sm">
            <span class="fw-bold small">Zaki Kurnia B., S.Kom</span>
            <span class="rs-text-muted small">Admin IT</span>
        </span>
    </div>

</header>