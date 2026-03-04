<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Data Program</h5>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahProgram">
        + Add Data
    </button>
</div>

<div class="grid">
    @forelse($programs as $prog)
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
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            Belum ada data program
        </div>
    </div>
    @endforelse
</div>


{{-- ======================= --}}
{{-- MODAL TAMBAH --}}
{{-- ======================= --}}
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
                        <input type="text" placeholder="Contoh: Pembinaan" name="name_prog" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Departemen</label>
                        <select name="departemen_id" id="departemenSelect" class="form-select" required>
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemens as $dep)
                            <option value="{{ $dep->id }}">
                                {{ $dep->name_dep }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="subWrapper" style="display:none;">
                        <label class="form-label">Sub Departemen</label>
                        <select id="subDepartemenSelect" class="form-select">
                        </select>
                    </div>

                    <input type="hidden" name="departemen_id" id="hiddenDepartemenId">

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



{{-- ======================= --}}
{{-- MODAL EDIT --}}
{{-- ======================= --}}
@foreach($programs as $prog)

@php
$selectedParentId = $prog->departemen->parent_id
? $prog->departemen->parent_id
: $prog->departemen->id;
@endphp

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

                        <select class="form-select edit-departemen" data-target="subEdit{{ $prog->id }}"
                            data-hidden="hiddenEdit{{ $prog->id }}" data-selected="{{ $prog->departemen->id }}"
                            required>

                            <option value="">-- Pilih Departemen --</option>

                            @foreach($departemens as $dep)
                            <option value="{{ $dep->id }}" {{ $selectedParentId==$dep->id ? 'selected' : '' }}>
                                {{ $dep->name_dep }}
                            </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="mb-3" id="subWrapperEdit{{ $prog->id }}" style="display:none;">

                        <label class="form-label">Sub Departemen</label>

                        <select id="subEdit{{ $prog->id }}" class="form-select">
                        </select>

                    </div>

                    <input type="hidden" name="departemen_id" id="hiddenEdit{{ $prog->id }}"
                        value="{{ $prog->departemen->id }}">

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


{{-- Kirim data ke JS --}}
<script>
    window.departemensData = @json($departemens);
</script>