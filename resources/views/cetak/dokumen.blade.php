@extends('layouts.cetak')
@section('title', $dokumen['nama'])

@section('content')
<h1 class="rs-print-title">{{ mb_strtoupper($dokumen['nama']) }}</h1>
<p class="rs-print-number">Nomor: {{ $isi['nomor'] }}</p>

<p>{{ $isi['pembuka'] }}</p>

<table class="rs-fields rs-fields-indent">
    @foreach ([
        'Nama'                 => $pasien['nama'],
        'Tanggal lahir / umur' => $pasien['tgl_lahir'] . ' / ' . $pasien['umur'],
        'Jenis kelamin'        => $pasien['jk'],
        'No. rekam medis'      => $pasien['no_rm'],
        'Alamat'               => $pasien['alamat'],
    ] as $label => $nilai)
        <tr><th scope="row">{{ $label }}</th><td>{{ $nilai }}</td></tr>
    @endforeach
</table>

@if (! empty($isi['rincian']))
    @if (! empty($isi['pengantar']))
        <p>{{ $isi['pengantar'] }}</p>
    @endif
    <table class="rs-fields rs-fields-indent">
        @foreach ($isi['rincian'] as $label => $nilai)
            <tr><th scope="row">{{ $label }}</th><td>{{ $nilai }}</td></tr>
        @endforeach
    </table>
@endif

<p>{{ $isi['penutup'] }}</p>

@include('cetak._tanda-tangan', ['penandatangan' => $isi['penandatangan']])
@endsection