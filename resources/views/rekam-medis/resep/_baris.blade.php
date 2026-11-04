{{-- Satu baris obat. $i = nomor baris ('__I__' untuk cetakan JS), $baris = isi baris --}}
@php $b = $baris ?? []; @endphp

<tr data-rs-baris-resep data-kode="{{ $b['kode'] ?? '' }}">
    <td class="rs-td-utama">
        <div>
            <div class="fw-semibold" data-rs-obat-nama>{{ $b['nama'] ?? '' }}</div>
            <div class="small rs-text-muted">Satuan: <span data-rs-obat-satuan>{{ $b['satuan'] ?? '' }}</span></div>
        </div>
        <input type="hidden" name="items[{{ $i }}][kode]" value="{{ $b['kode'] ?? '' }}" data-rs-f="kode">
        <input type="hidden" name="items[{{ $i }}][nama]" value="{{ $b['nama'] ?? '' }}" data-rs-f="nama">
        <input type="hidden" name="items[{{ $i }}][satuan]" value="{{ $b['satuan'] ?? '' }}" data-rs-f="satuan">
    </td>
    <td data-label="Jumlah">
        <input type="number" min="1" inputmode="numeric" aria-label="Jumlah" data-rs-f="jumlah"
               name="items[{{ $i }}][jumlah]" value="{{ $b['jumlah'] ?? '' }}"
               @class(['form-control form-control-sm rs-input-jumlah', 'is-invalid' => $errors->has("items.$i.jumlah")])>
    </td>
    <td data-label="Aturan pakai">
        <input type="text" list="daftar-signa" placeholder="mis. 3 x 1" aria-label="Aturan pakai" data-rs-f="signa"
               name="items[{{ $i }}][signa]" value="{{ $b['signa'] ?? '' }}"
               @class(['form-control form-control-sm', 'is-invalid' => $errors->has("items.$i.signa")])>
    </td>
    <td data-label="Rute">
        <select name="items[{{ $i }}][rute]" class="form-select form-select-sm" aria-label="Rute" data-rs-f="rute">
            @foreach ($rute as $kode => $label)
                <option value="{{ $kode }}" @selected(($b['rute'] ?? 'oral') === $kode)>{{ $label }}</option>
            @endforeach
        </select>
    </td>
    <td data-label="Catatan">
        <input type="text" placeholder="mis. sesudah makan" aria-label="Catatan" data-rs-f="catatan"
               name="items[{{ $i }}][catatan]" value="{{ $b['catatan'] ?? '' }}" class="form-control form-control-sm">
    </td>
    <td class="rs-td-aksi text-end">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-rs-hapus-baris
                aria-label="Hapus {{ $b['nama'] ?? 'obat' }}">
            <i class="bi bi-trash" aria-hidden="true"></i>
        </button>
    </td>
</tr>