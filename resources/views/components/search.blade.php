@props(['label' => 'Cari', 'size' => null])

{{-- Satu kotak utuh: ikon + isian. Saat diklik seluruh kotak menyala. --}}
<label {{ $attributes->only('class')->class(['rs-search', 'rs-search-sm' => $size === 'sm']) }}>
    <i class="bi bi-search" aria-hidden="true"></i>
    <span class="visually-hidden">{{ $label }}</span>
    <input type="search" {{ $attributes->except('class') }}>
</label>