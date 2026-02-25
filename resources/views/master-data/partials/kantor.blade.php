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
    <h5 class="mb-0">Data Kantor</h5>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKantor">
        + Add Data
    </button>
</div>

<div class="row">

    @forelse($kantors as $ktr)
    <div class="col-md-4 mb-4">
        <div class="card bg-light border-0 shadow-sm rounded-4 card-hover">
            <div class="card-body">

                <h5 class="fw-bold mb-1">
                    {{ $ktr->name_ktr }}
                </h5>

                <p class="mb-2">
                    <small>Kode: {{ $ktr->code_ktr }}</small>
                </p>

                <span class="badge {{ $ktr->type_ktr == 'pusat' ? 'bg-primary' : 'bg-success' }}">
                    {{ ucfirst($ktr->type_ktr) }}
                </span>

                <div class="d-flex justify-content-end gap-2 mt-3">

                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                        data-bs-target="#modalEditKantor{{ $ktr->id }}">
                        Edit
                    </button>

                    <form action="{{ route('kantor.delete', $ktr->id) }}" method="POST"
                        onsubmit="return confirm('Yakin hapus kantor ini?')">
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
            Belum ada data kantor
        </div>
    </div>
    @endforelse

</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahKantor" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('kantor.store') }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kantor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Kantor</label>
                        <input type="text" name="name_ktr" class="form-control"
                            placeholder="Contoh: Kantor Pusat Jakarta" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kode Kantor</label>
                        <input type="text" name="code_ktr" class="form-control" placeholder="Contoh: KTR001" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe Kantor</label>
                        <select name="type_ktr" class="form-select" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="pusat">Pusat</option>
                            <option value="cabang">Cabang</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
@foreach($kantors as $ktr)
<div class="modal fade" id="modalEditKantor{{ $ktr->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('kantor.update', $ktr->id) }}">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kantor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Kantor</label>
                        <input type="text" name="name_ktr" value="{{ $ktr->name_ktr }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kode Kantor</label>
                        <input type="text" name="code_ktr" value="{{ $ktr->code_ktr }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe Kantor</label>
                        <select name="type_ktr" class="form-select" required>
                            <option value="pusat" {{ $ktr->type_ktr == 'pusat' ? 'selected' : '' }}>Pusat</option>
                            <option value="cabang" {{ $ktr->type_ktr == 'cabang' ? 'selected' : '' }}>Cabang</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                </div>

            </div>
        </form>
    </div>
</div>
@endforeach