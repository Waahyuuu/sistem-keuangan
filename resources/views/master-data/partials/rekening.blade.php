<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">

    @if(session('success'))
    <div class="toast align-items-center text-bg-success border-0 show-auto" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="toast align-items-center text-bg-danger border-0 show-auto" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Akun Rekening</h5>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
        + Add Data
    </button>
</div>

<div class="grid">
    @forelse($rekenings as $rek)
    <div class="card bg-light border-0 shadow-sm rounded-4 card-hover">
        <div class="card-body">

            <h5 class="fw-bold mb-1">
                {{ $rek->name_rek }}
            </h5>

            <p class="mb-4">
                <small>{{ $rek->no_rek }}</small>
            </p>

            <div class="d-flex justify-content-between align-items-center">

                <div class="d-flex align-items-center gap-2">
                    <div class="form-check form-switch m-0">
                        <input class="form-check-input toggle-rekening" type="checkbox" data-id="{{ $rek->id }}" {{
                            $rek->is_active ? 'checked' : '' }}>
                    </div>

                    <small class="status-text {{ $rek->is_active ? 'text-success' : 'text-muted' }}">
                        {{ $rek->is_active ? 'Aktif' : 'Nonaktif' }}
                    </small>
                </div>

                <div class="d-flex gap-2">

                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                        data-bs-target="#modalEdit{{ $rek->id }}">
                        Edit
                    </button>

                    <form action="{{ route('rekening.delete', $rek->id) }}" method="POST"
                        onsubmit="return confirm('Yakin hapus rekening ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">
                            Hapus
                        </button>
                    </form>

                </div>

            </div>

        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            Belum ada data rekening
        </div>
    </div>
    @endforelse

</div>

{{-- Modal Edit --}}
@foreach($rekenings as $rek)
<div class="modal fade" id="modalEdit{{ $rek->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('rekening.update', $rek->id) }}">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Rekening</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Rekening</label>
                        <input type="text" name="name_rek" value="{{ $rek->name_rek }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No Rekening</label>
                        <input type="text" name="no_rek" value="{{ $rek->no_rek }}" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        Update
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endforeach

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('rekening.store') }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Rekening</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Rekening</label>
                        <input type="text" name="name_rek" placeholder="Contoh: Belanja" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No Rekening</label>
                        <input type="text" name="no_rek" placeholder="Contoh: Angkat atau '-'" class="form-control"
                            required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">
                        Simpan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>