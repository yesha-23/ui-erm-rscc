{{-- $penandatangan = [[peran, nama, keterangan?], ...]
     Kota & tanggal muncul di atas kolom paling kanan. --}}
@php $tanggal = config('rscc.kota') . ', ' . now()->locale('id')->translatedFormat('d F Y'); @endphp

<div @class(['rs-sign', 'rs-sign-end' => count($penandatangan) === 1])>
    @foreach ($penandatangan as $p)
        <div class="rs-sign-box">
            <div class="rs-sign-date">{{ $loop->last ? $tanggal : '' }}</div>
            <div class="rs-sign-role">{{ $p[0] }},</div>
            <div class="rs-sign-space">
                {{-- TODO (IT RSCC): gambar tanda tangan / QR tanda tangan elektronik --}}
                @if ($draf ?? false) Belum ditandatangani @endif
            </div>
            <div class="rs-sign-name">{{ $p[1] }}</div>
            @isset($p[2])
                <div class="rs-sign-ket">{{ $p[2] }}</div>
            @endisset
        </div>
    @endforeach
</div>