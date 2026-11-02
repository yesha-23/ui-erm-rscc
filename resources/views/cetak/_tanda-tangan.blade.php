{{-- $penandatangan = [[peran, nama], ...]. Tanggal & kota muncul di atas kolom paling kanan. --}}
@php $tanggal = config('rscc.kota') . ', ' . now()->locale('id')->translatedFormat('d F Y'); @endphp

<div @class(['rs-sign', 'rs-sign-end' => count($penandatangan) === 1])>
    @foreach ($penandatangan as [$peran, $nama])
        <div class="rs-sign-box">
            <div class="rs-sign-date">{{ $loop->last ? $tanggal : '' }}</div>
            <div class="rs-sign-role">{{ $peran }}</div>
            <div class="rs-sign-space">
                {{-- TODO (IT RSCC): gambar tanda tangan / QR tanda tangan elektronik --}}
                @if ($draf ?? false) Belum ditandatangani @endif
            </div>
            <div class="rs-sign-name">{{ $nama }}</div>
        </div>
    @endforeach
</div>