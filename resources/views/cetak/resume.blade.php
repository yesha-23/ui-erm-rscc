@extends('layouts.cetak')
@section('title', 'Resume Medis')

@section('content')
<h1 class="rs-print-title">RESUME MEDIS</h1>
<p class="rs-print-number">{{ $pasien['unit'] }} · No. Registrasi {{ $pasien['no_reg'] }}</p>

<dl class="rs-ident">
    @foreach ([
        'Nama'               => $pasien['nama'],
        'No. RM'             => $pasien['no_rm'],
        'Tgl lahir / umur'   => $pasien['tgl_lahir'] . ' / ' . $pasien['umur'],
        'Jenis kelamin'      => $pasien['jk'],
        'Tanggal masuk'      => $pasien['datang'],
        'Penjamin'           => $pasien['penjamin'] === 'JKN' ? 'BPJS Kesehatan' : 'Umum',
        'DPJP'               => $pasien['dpjp_nama'],
        'Alergi'             => $pasien['alergi'],
    ] as $label => $nilai)
        <div @class(['is-alert' => $label === 'Alergi'])>
            <dt>{{ $label }}</dt>
            <dd>{{ $nilai }}</dd>
        </div>
    @endforeach
</dl>

<table class="rs-section-table">
    <tbody>
        @foreach ($resume as $label => $nilai)
            <tr><th scope="row">{{ $label }}</th><td>{{ $nilai }}</td></tr>
        @endforeach
    </tbody>
</table>

@include('cetak._tanda-tangan', ['penandatangan' => [['Dokter penanggung jawab (DPJP)', $pasien['dpjp_nama'], 'SIP. [No. SIP]']]])
@endsection