@props(['tabs', 'aktif'])

<nav class="rs-tabs mb-3" aria-label="Saring berdasarkan status">
    @foreach ($tabs as $key => $tab)
        <a href="{{ request()->fullUrlWithQuery(['status' => $key, 'page' => null]) }}"
           @class(['rs-tab', 'active' => $aktif === $key])
           @if ($aktif === $key) aria-current="page" @endif>
            {{ $tab['label'] }}
            <span class="rs-tab-count">{{ $tab['jumlah'] }}</span>
        </a>
    @endforeach
</nav>