@if (session('sukses'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 rs-flash" role="status">
        <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
        <div>{{ session('sukses') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif