@props(['ok' => false, 'label' => ['Lengkap', 'Belum']])

{{-- Class rs-lengkap: tidak bentrok dengan .rs-cek (checklist) maupun .rs-status (permintaan) --}}
<span @class(['rs-lengkap', 'rs-lengkap-ok' => $ok, 'rs-lengkap-belum' => ! $ok])>
    <i class="bi {{ $ok ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }}" aria-hidden="true"></i>
    {{ $ok ? $label[0] : $label[1] }}
</span>