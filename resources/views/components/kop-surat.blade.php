{{-- Kop surat resmi RS — dipakai semua lembar cetak --}}
<header class="rs-kop">
    <img src="{{ asset('images/logo-kop-rscc.png') }}" alt="Logo {{ config('rscc.nama') }}">
    <div class="rs-kop-teks">
        <p class="rs-kop-pt">{{ config('rscc.badan_usaha') }}</p>
        <p class="rs-kop-nama">{{ config('rscc.nama') }}</p>
        <p class="rs-kop-alamat">
            {{ config('rscc.alamat') }}<br>
            Telp. {{ config('rscc.telepon') }}
            &nbsp; E-mail: {{ config('rscc.email') }}
            &nbsp; Website: <span class="rs-kop-web">{{ config('rscc.website') }}</span>
        </p>
    </div>
</header>