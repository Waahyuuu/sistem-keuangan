<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">

    @if(session('success'))
    <div class="toast align-items-center text-bg-success border-0 show-auto">
        <div class="d-flex">
            <div class="toast-body">
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="toast align-items-center text-bg-danger border-0 show-auto">
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
    <h5 class="mb-0">Data Program</h5>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahProgram">
        + Add Data
    </button>
</div>

<div class="row">

    @forelse($programs as $prog)
    <div class="col-md-4 mb-4">
        <div class="card bg-light border-0 shadow-sm rounded-4 card-hover">
            <div class="card-body">

                <h5 class="fw-bold mb-1">
                    {{ $prog->name_prog }}
                </h5>

                <p class="mb-2">
                    @if($prog->departemen->parent)

                    <span class="badge bg-primary">
                        {{ $prog->departemen->parent->name_dep }}
                    </span>

                    <i class="bi bi-arrow-right mx-1"></i>

                    <span class="badge bg-secondary">
                        Sub: {{ $prog->departemen->name_dep }}
                    </span>

                    @else

                    <span class="badge bg-primary">
                        {{ $prog->departemen->name_dep }}
                    </span>

                    @endif
                </p>

                <div class="d-flex justify-content-end gap-2 mt-3">

                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                        data-bs-target="#modalEditProgram{{ $prog->id }}">
                        Edit
                    </button>

                    <form action="{{ route('program.delete', $prog->id) }}" method="POST"
                        onsubmit="return confirm('Yakin hapus program ini?')">
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
            Belum ada data program
        </div>
    </div>
    @endforelse

</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahProgram" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('program.store') }}">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Program</label>
                        <input type="text" name="name_prog" class="form-control"
                            placeholder="Contoh: Program Pembangunan 2026" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Departemen</label>
                        <select id="departemenSelect" class="form-select" required>
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemens as $dep)
                            <option value="{{ $dep->id }}">
                                {{ $dep->name_dep }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="subWrapper">
                        <label class="form-label">Sub Departemen</label>
                        <select name="departemen_id" id="subDepartemenSelect" class="form-select">
                            <option value="">-- Pilih Sub Departemen --</option>
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
@foreach($programs as $prog)
<div class="modal fade" id="modalEditProgram{{ $prog->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('program.update', $prog->id) }}">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Program</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Program</label>
                        <input type="text" name="name_prog" value="{{ $prog->name_prog }}" class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Departemen</label>
                        <select class="form-select edit-departemen" data-target="subEdit{{ $prog->id }}" required>
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemens as $dep)
                            <option value="{{ $dep->id }}" {{ ( $prog->departemen->parent_id
                                ? $prog->departemen->parent_id == $dep->id
                                : $prog->departemen->id == $dep->id
                                ) ? 'selected' : ''
                                }}>
                                {{ $dep->name_dep }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="subWrapperEdit{{ $prog->id }}">
                        <label class="form-label">Sub Departemen</label>
                        <select name="departemen_id" id="subEdit{{ $prog->id }}" class="form-select">
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

{{-- kirim data ke js --}}
<script>
    window.departemensData = @json($departemens ?? []);
</script>