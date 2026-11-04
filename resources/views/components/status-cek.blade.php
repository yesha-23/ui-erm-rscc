@props(['ok' => false, 'label' => ['Lengkap', 'Belum']])

{{-- Class rs-status (bukan rs-cek) supaya tidak bentrok dengan checklist PPI/CP --}}
<span @class(['rs-status', 'rs-status-ok' => $ok, 'rs-status-belum' => ! $ok])>
    <i class="bi {{ $ok ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }}" aria-hidden="true"></i>
    {{ $ok ? $label[0] : $label[1] }}
</span>