@props(['jenis' => 'jkn'])

@php
    $kelas = [
        'jkn'        => 'rs-badge-jkn',
        'nonjkn'     => 'rs-badge-nonjkn',
        'datang'     => 'rs-badge-datang',
        'belum'      => 'rs-badge-belum',
        'netral'     => 'rs-badge-netral',
        'sukses'     => 'rs-badge-jkn',
        'gagal'      => 'rs-badge-nonjkn',
        'info'       => 'rs-badge-datang',
        'peringatan' => 'rs-badge-belum',
    ][$jenis] ?? 'rs-badge-datang';
@endphp

<span {{ $attributes->class(['rs-badge', $kelas]) }}>{{ $slot }}</span>