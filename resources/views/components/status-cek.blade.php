@props(['ok' => false, 'label' => ['Lengkap', 'Belum']])

<span @class(['rs-cek', 'rs-cek-ok' => $ok, 'rs-cek-belum' => ! $ok])>
    <i class="bi {{ $ok ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }}" aria-hidden="true"></i>
    {{ $ok ? $label[0] : $label[1] }}
</span>