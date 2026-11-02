@extends('layouts.app')
@section('title', 'Inbox')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Inbox</h1>
            <p class="rs-text-muted mb-0">{{ $belumDibaca }} pesan belum dibaca</p>
        </div>
        {{-- TODO (IT RSCC): tandai semua pesan sebagai dibaca --}}
        <button type="button" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-check2-all me-1" aria-hidden="true"></i>Tandai semua dibaca
        </button>
    </div>

    <div class="rs-card p-0 rs-inbox">
        {{-- Daftar pesan (di HP disembunyikan saat sedang membaca pesan) --}}
        <nav @class(['rs-inbox-list', 'd-none d-lg-block' => $terpilih]) aria-label="Daftar pesan">
            <ul class="list-unstyled mb-0">
                @foreach ($pesan as $p)
                    @php $aktif = $terpilih && $terpilih['id'] === $p['id']; @endphp
                    <li>
                        <a href="{{ route('akun.inbox', $p['id']) }}"
                           @class(['rs-inbox-item', 'active' => $aktif, 'unread' => ! $p['dibaca']])
                           @if ($aktif) aria-current="true" @endif>
                            <span class="rs-avatar rs-avatar-pasien" aria-hidden="true">{{ $p['inisial'] }}</span>
                            <span class="rs-inbox-text">
                                <span class="d-flex justify-content-between gap-2">
                                    <span class="rs-inbox-from">{{ $p['dari'] }}</span>
                                    <span class="small rs-text-muted text-nowrap">{{ $p['waktu'] }}</span>
                                </span>
                                <span class="rs-inbox-title">{{ $p['judul'] }}</span>
                                <span class="rs-inbox-snippet">{{ $p['ringkas'] }}</span>
                            </span>
                            @unless ($p['dibaca'])
                                <span class="rs-dot"><span class="visually-hidden">Belum dibaca</span></span>
                            @endunless
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Isi pesan --}}
        <article @class(['rs-inbox-detail', 'd-none d-lg-flex' => ! $terpilih])>
            @if ($terpilih)
                <a href="{{ route('akun.inbox') }}" class="d-lg-none small fw-semibold text-decoration-none mb-3">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali ke daftar
                </a>
                <div class="d-flex align-items-start gap-3 mb-3">
                    <span class="rs-avatar rs-avatar-pasien" aria-hidden="true">{{ $terpilih['inisial'] }}</span>
                    <div class="flex-grow-1">
                        <div class="fw-bold">{{ $terpilih['dari'] }}</div>
                        <div class="small rs-text-muted">{{ $terpilih['waktu'] }}</div>
                    </div>
                    <x-badge :jenis="$terpilih['kategori'] === 'Tugas' ? 'peringatan' : 'info'">{{ $terpilih['kategori'] }}</x-badge>
                </div>
                <h2 class="h5 fw-bold mb-3">{{ $terpilih['judul'] }}</h2>
                <div class="rs-inbox-body">{!! nl2br(e($terpilih['isi'])) !!}</div>
            @else
                <div class="rs-empty m-auto">
                    <span class="rs-empty-icon"><i class="bi bi-envelope-open" aria-hidden="true"></i></span>
                    <h2 class="h6 fw-bold mb-1">Pilih pesan untuk dibaca</h2>
                    <p class="rs-text-muted mb-0">Isi pesan akan tampil di sini.</p>
                </div>
            @endif
        </article>
    </div>
@endsection