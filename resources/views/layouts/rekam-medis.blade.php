<!doctype html>
<html lang="id" class="rs-sidebar-mini">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rekam Medis') · {{ $pasien['nama'] }} · EMR RSCC</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="rs-body-app">
<div class="rs-app">
    {{-- Sidebar utama otomatis mode ikon (class rs-sidebar-mini di <html>) --}}
    <x-sidebar />

    <div class="rs-content rs-rm">
        <x-pasien-header :pasien="$pasien" :crumb="$crumb ?? null" />

        <div class="rs-rm-body">
            <aside id="menuRekamMedis" class="rs-rm-menu collapse d-lg-block">
                <x-menu-rm :no-reg="$pasien['no_reg']" :aktif="$menuAktif ?? ''" />
            </aside>
            {{-- Pulihkan posisi gulir menu SEBELUM halaman tampil, supaya menu tidak terlihat melompat
                 (posisi disimpan oleh initMenuRmTetap di rscc.js) --}}
            <script>
                try {
                    const posisiMenu = sessionStorage.getItem('rs-rmmenu-gulir');
                    if (posisiMenu !== null) document.getElementById('menuRekamMedis').scrollTop = Number(posisiMenu);
                } catch (e) { /* abaikan */ }
            </script>

            <main class="rs-rm-main">
                <x-flash />
                @yield('content')
            </main>
        </div>
    </div>
</div>

{{-- Modal diletakkan di akhir <body> supaya tidak terpotong oleh area yang bisa digulir --}}
<x-cari-menu :pasien="$pasien" />
<x-modal-final :pasien="$pasien" />
<x-modal-alergi :pasien="$pasien" />
@stack('modal')
@stack('scripts')
</body>
</html>