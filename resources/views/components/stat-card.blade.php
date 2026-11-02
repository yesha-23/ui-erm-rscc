@props(['label', 'value', 'icon' => 'bi-people', 'warna' => 'biru', 'keterangan' => null])

<div class="rs-card h-100 d-flex flex-column gap-3">
    <div class="d-flex justify-content-between align-items-center">
        <span class="fw-semibold rs-text-muted">{{ $label }}</span>
        <span class="rs-stat-icon rs-stat-{{ $warna }}"><i class="bi {{ $icon }}" aria-hidden="true"></i></span>
    </div>
    <p class="rs-stat-value mb-0">{{ $value }}</p>
    @if ($keterangan)
        <p class="small rs-text-muted mb-0">{{ $keterangan }}</p>
    @endif
</div>