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
    <h5 class="mb-0">Data Kategori</h5>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
        + Add Data
    </button>
</div>

<div class="row">

    @forelse($kategoris as $ktgr)
    <div class="col-md-4 mb-4">
        <div class="card bg-light border-0 shadow-sm rounded-4 card-hover p-3">

            <div class="d-flex justify-content-between align-items-start">

                <div>
                    <h5 class="fw-bold mb-1">{{ $ktgr->name_ktgr }}</h5>

                    <span class="badge"
                        style="background-color: {{ $ktgr->type_ktgr == 'pemasukan' ? '#198754' : '#dc3545' }}; color: #fff;">
                        {{ ucfirst($ktgr->type_ktgr) }}
                    </span>
                </div>

                <div
                    style="width: 30px; height: 30px; border-radius: 100%; 
                            background-color: {{ $ktgr->color_ktgr ?? ($ktgr->type_ktgr == 'pemasukan' ? '#198754' : '#dc3545') }};">
                </div>

            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                    data-bs-target="#modalEditKategori{{ $ktgr->id }}">
                    Edit
                </button>

                <form action="{{ route('kategori.delete', $ktgr->id) }}" method="POST"
                    onsubmit="return confirm('Yakin hapus kategori ini?')">
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
            Belum ada data kategori
        </div>
    </div>
    @endforelse

</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambahKategori" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('kategori.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="name_ktgr" class="form-control" placeholder="Contoh: Dana Pembangunan"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe Kategori</label>

                        <div class="toggle-group">
                            <input type="radio" name="type_ktgr" id="pemasukan" value="pemasukan" checked>
                            <label for="pemasukan" class="toggle-option income">Pemasukan</label>

                            <input type="radio" name="type_ktgr" id="pengeluaran" value="pengeluaran">
                            <label for="pengeluaran" class="toggle-option expense">Pengeluaran</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Warna Badge</label>
                        <input type="color" name="color_ktgr" class="form-control form-control-color" value="#0d6efd">
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
@foreach($kategoris as $ktgr)
<div class="modal fade" id="modalEditKategori{{ $ktgr->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('kategori.update', $ktgr->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Nama Kategori</label>
                        <input type="text" name="name_ktgr" value="{{ $ktgr->name_ktgr }}" class="form-control"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe Kategori</label>

                        <div class="toggle-group">

                            <input type="radio" name="type_ktgr" id="edit_pemasukan{{ $ktgr->id }}" value="pemasukan" {{
                                $ktgr->type_ktgr == 'pemasukan' ? 'checked' : '' }}>

                            <label for="edit_pemasukan{{ $ktgr->id }}" class="toggle-option income">
                                Pemasukan
                            </label>


                            <input type="radio" name="type_ktgr" id="edit_pengeluaran{{ $ktgr->id }}"
                                value="pengeluaran" {{ $ktgr->type_ktgr == 'pengeluaran' ? 'checked' : '' }}>

                            <label for="edit_pengeluaran{{ $ktgr->id }}" class="toggle-option expense">
                                Pengeluaran
                            </label>

                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Warna Badge</label>
                        <input type="color" name="color_ktgr" class="form-control form-control-color"
                            value="{{ $ktgr->color_ktgr ?? '#0d6efd' }}">
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