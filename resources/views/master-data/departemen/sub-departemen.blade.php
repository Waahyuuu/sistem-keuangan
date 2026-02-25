@extends('layouts.app')

@section('content')

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

<a href="{{ route('master.data', ['tab' => 'departemen']) }}" class="btn btn-secondary mb-3">
    ← Kembali
</a>

<div class="card mb-4">
    <div class="card-body">
        <h4>{{ $parent->name_dep }}</h4>
        <small>Kantor: {{ $parent->kantor->name_ktr }}</small>
    </div>
</div>

<form method="POST" action="{{ route('departemen.sub.store', $parent) }}">
    @csrf
    <div class="input-group mb-4">
        <input type="text" name="name_dep" class="form-control" placeholder="Nama Sub Departemen">
        <button class="btn btn-primary">Tambah</button>
    </div>
</form>

@foreach($subDepartemens as $sub)
<div class="card mb-2">
    <div class="card-body d-flex justify-content-between align-items-center">
        <b>{{ $sub->name_dep }}</b>

        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal"
                data-bs-target="#modalEditSub{{ $sub->id }}">
                Edit
            </button>

            <form method="POST" action="{{ route('departemen.sub.delete', $sub) }}"
                onsubmit="return confirm('Yakin hapus sub departemen ini?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">
                    Hapus
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal fade" id="modalEditSub{{ $sub->id }}">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('departemen.sub.update', $sub) }}">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit Sub Departemen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="name_dep" value="{{ $sub->name_dep }}" class="form-control">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection