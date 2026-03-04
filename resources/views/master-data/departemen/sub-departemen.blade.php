@extends('layouts.app')

@section('content')

<!-- =========================
        HEADER
========================== -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('master.data', ['tab' => 'departemen']) }}"
        class="btn btn-outline-secondary">
        ← Kembali
    </a>

    <button class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#modalTambahSub">
        + Tambah Sub Departemen
    </button>
</div>


<!-- =========================
        PARENT INFO
========================== -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <h4 class="fw-bold mb-1">{{ $parent->name_dep }}</h4>
        <small class="text-muted">
            Kantor: {{ $parent->kantor->name_ktr }}
        </small>
    </div>
</div>


<!-- =========================
        LIST SUB
========================== -->
<div class="row g-4">
    @forelse($subDepartemens as $sub)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex flex-column justify-content-between">

                    <div>
                        <h6 class="fw-bold mb-3">
                            {{ $sub->name_dep }}
                        </h6>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-sm btn-outline-warning w-100"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditSub{{ $sub->id }}">
                            Edit
                        </button>

                        <form method="POST"
                            action="{{ route('departemen.sub.delete', $sub) }}"
                            class="w-100"
                            onsubmit="return confirm('Yakin hapus sub departemen ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger w-100">
                                Hapus
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        <!-- =========================
                MODAL EDIT
        ========================== -->
        <div class="modal fade" id="modalEditSub{{ $sub->id }}">
            <div class="modal-dialog">
                <form method="POST"
                    action="{{ route('departemen.sub.update', $sub) }}">
                    @csrf
                    @method('PUT')

                    <div class="modal-content rounded-4">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Sub Departemen</h5>
                            <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <label class="form-label">Nama Sub Departemen</label>
                            <input type="text"
                                name="name_dep"
                                value="{{ $sub->name_dep }}"
                                class="form-control"
                                required>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary w-100">
                                Update
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                Belum ada sub departemen
            </div>
        </div>
    @endforelse
</div>


<!-- =========================
        MODAL TAMBAH
========================== -->
<div class="modal fade" id="modalTambahSub">
    <div class="modal-dialog">
        <form method="POST"
            action="{{ route('departemen.sub.store', $parent) }}">
            @csrf

            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Sub Departemen</h5>
                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Nama Sub Departemen</label>
                    <input type="text"
                        name="name_dep"
                        class="form-control"
                        placeholder="Masukkan nama sub departemen"
                        required>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary w-100">
                        Simpan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

@endsection