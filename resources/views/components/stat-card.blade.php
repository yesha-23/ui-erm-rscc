@props(['label', 'value', 'icon' => 'bi-people'])

<div class="rs-card h-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="fw-semibold text-secondary">{{ $label }}</span>
        <span class="rs-stat-icon"><i class="bi {{ $icon }}" aria-hidden="true"></i></span>
    </div>
    <p class="fs-2 fw-bold mb-0">{{ $value }}</p>
</div>