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
    <h5 class="mb-0">Data Unit/Departemen</h5>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahDep">
        + Add Data
    </button>
</div>

<div class="row">
    @forelse($departemens as $dep)
    <div class="col-md-4 mb-3">
        <div class="card bg-light border-0 shadow-sm rounded-4 card-hover">
            <div class="card-body">

                <h5 class="fw-bold mb-1">
                    {{ $dep->name_dep }}
                </h5>
                
                <small>Kantor: {{ $dep->kantor->name_ktr ?? '-' }}</small>

                <div class="mt-2 mb-2">
                    <span class="badge bg-primary">
                        {{ $dep->children_count }} Sub
                    </span>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('departemen.sub', $dep) }}" class="btn btn-sm btn-outline-info">
                        Kelola Sub
                    </a>

                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                        data-bs-target="#modalEdit{{ $dep->id }}">
                        Edit
                    </button>

                    <form action="{{ route('departemen.delete', $dep->id) }}" method="POST"
                        onsubmit="return confirm('Yakin hapus departemen ini?')">
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
            Belum ada data departemen
        </div>
    </div>
    @endforelse
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahDep">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('departemen.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Tambah Departemen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="name_dep" class="form-control mb-2" placeholder="Contoh: Keuangan">

                    <select name="kantor_id" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Kantor --</option>

                        @foreach($kantors as $ktr)
                        <option value="{{ $ktr->id }}">
                            {{ $ktr->name_ktr }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
@foreach($departemens as $dep)
<div class="modal fade" id="modalEdit{{ $dep->id }}">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('departemen.update', $dep->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit Departemen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="name_dep" value="{{ $dep->name_dep }}" class="form-control mb-2">

                    <select name="kantor_id" class="form-select">
                        @foreach($kantors as $ktr)
                        <option value="{{ $ktr->id }}" {{ $ktr->id == $dep->kantor_id ? 'selected' : '' }}>
                            {{ $ktr->name_ktr }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach