<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · {{ config('rscc.nama') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/cetak.css')
</head>
<body class="rs-print-body">

    {{-- Bar atas: tidak ikut tercetak --}}
    <div class="rs-print-toolbar no-print">
        <button type="button" class="rs-btn" data-rs-close>← Tutup</button>
        <span class="judul">@yield('title')</span>
        <span class="hint">Kertas A4 · skala 100% · matikan "Header dan footer" di jendela cetak</span>
        <button type="button" class="rs-btn rs-btn-primary" data-rs-print>🖨 Cetak</button>
    </div>

    <main class="rs-sheet">
        @if ($draf ?? false)
            <div class="rs-watermark" aria-hidden="true">DRAF</div>
        @endif

        <x-kop-surat />

        @yield('content')

        <footer class="rs-sheet-foot">
            <span>No. RM {{ $pasien['no_rm'] ?? '-' }} · No. Reg {{ $pasien['no_reg'] ?? '-' }}</span>
            {{-- TODO (IT RSCC): ganti [Nama Pengguna] dengan auth()->user()->nama --}}
            <span>Dicetak dari EMR RSCC oleh [Nama Pengguna] · {{ now()->locale('id')->translatedFormat('d F Y, H:i') }}</span>
        </footer>
    </main>

    <script>
        document.querySelector('[data-rs-print]')?.addEventListener('click', () => window.print());
        document.querySelector('[data-rs-close]')?.addEventListener('click', () => {
            // Dibuka di tab baru → tutup tab; dibuka dari halaman lain → kembali
            history.length > 1 ? history.back() : window.close();
        });
    </script>
</body>
</html>