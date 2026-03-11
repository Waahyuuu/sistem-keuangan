@extends('layouts.app')

@section('title', 'Pengaturan Akun')

@section('content')

<h3 class="mb-4">Manajemen Pengguna</h3>

<div class="card shadow-sm border-0 rounded-4">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Pengguna</h5>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
            + Tambah Pengguna
        </button>
    </div>


    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Kantor</th>
                        <th>Departemen</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($users as $user)

                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>

                        <td>
                            @if($user->role == 'admin')
                            <span class="badge bg-danger">Admin</span>
                            @elseif($user->role == 'operator')
                            <span class="badge bg-primary">Operator</span>
                            @else
                            <span class="badge bg-secondary">User</span>
                            @endif
                        </td>

                        <td>
                            {{ $user->kantor->name_ktr ?? '-' }}
                        </td>

                        <td>
                            {{ $user->departemen->name_dep ?? '-' }}
                        </td>

                        <td>

                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editUser{{ $user->id }}">
                                Edit
                            </button>

                        </td>

                    </tr>


                    {{-- MODAL EDIT USER --}}

                    <div class="modal fade" id="editUser{{ $user->id }}">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <form action="{{ route('pengguna.update',$user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Pengguna</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">

                                        <div class="mb-3">
                                            <label>Nama</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ $user->name }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ $user->email }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Password Baru</label>
                                            <input type="password" name="password" class="form-control"
                                                placeholder="Kosongkan jika tidak diganti">
                                        </div>

                                        <div class="mb-3">
                                            <label>Role</label>
                                            <select name="role" class="form-control">

                                                <option value="admin" {{ $user->role=='admin'?'selected':'' }}>
                                                    Admin
                                                </option>

                                                <option value="operator" {{ $user->role=='operator'?'selected':'' }}>
                                                    Operator
                                                </option>

                                                <option value="user" {{ $user->role=='user'?'selected':'' }}>
                                                    User
                                                </option>

                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label>Kantor</label>
                                            <select name="kantor_id" class="form-control">

                                                <option value="">-- Pilih Kantor --</option>

                                                @foreach($kantors as $ktr)

                                                <option value="{{ $ktr->id }}" {{ $user->kantor_id == $ktr->id ?
                                                    'selected':'' }}>
                                                    {{ $ktr->name_ktr }}
                                                </option>

                                                @endforeach

                                            </select>
                                        </div>


                                        <div class="mb-3">
                                            <label>Departemen</label>

                                            <select name="departemen_id" class="form-control">

                                                <option value="">-- Pilih Departemen --</option>

                                                @foreach($departemens as $dep)

                                                <option value="{{ $dep->id }}" {{ $user->departemen_id == $dep->id ?
                                                    'selected':'' }}>
                                                    {{ $dep->name_dep }}
                                                </option>

                                                @endforeach

                                            </select>

                                        </div>


                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-primary">
                                            Update
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>


                    @empty

                    <tr>
                        <td colspan="6" class="text-center">
                            Belum ada data pengguna
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- MODAL TAMBAH USER --}}

<div class="modal fade" id="modalTambah">

    <div class="modal-dialog">

        <div class="modal-content">

            <form action="{{ route('pengguna.store') }}" method="POST">

                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengguna</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>


                <div class="modal-body">

                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>


                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>


                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>


                    <div class="mb-3">
                        <label>Role</label>

                        <select name="role" id="roleSelect" class="form-control">

                            <option value="admin">Admin</option>
                            <option value="operator">Operator</option>
                            <option value="user">User</option>

                        </select>

                    </div>


                    {{-- KANTOR --}}
                    <div class="mb-3" id="kantorField">

                        <label>Kantor</label>

                        <select name="kantor_id" id="kantorSelect" class="form-control">

                            <option value="">-- Pilih Kantor --</option>

                            @foreach($kantors as $ktr)

                            <option value="{{ $ktr->id }}">
                                {{ $ktr->name_ktr }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- DEPARTEMEN --}}
                    <div class="mb-3" id="departemenField">

                        <label>Departemen</label>

                        <select name="departemen_id" class="form-control">

                            <option value="">-- Pilih Departemen --</option>

                            @foreach($departemens as $dep)

                            <option value="{{ $dep->id }}">
                                {{ $dep->name_dep }}
                            </option>

                            @endforeach

                        </select>

                    </div>


                </div>


                <div class="modal-footer">

                    <button class="btn btn-success">
                        Simpan
                    </button>

                </div>


            </form>

        </div>

    </div>

</div>

<script>
    function toggleForm(){

    let role = document.getElementById("roleSelect").value
    let kantor = document.getElementById("kantorField")
    let departemen = document.getElementById("departemenField")
    let kantorValue = document.getElementById("kantorSelect").value

    if(role === "admin"){
        kantor.style.display = "none"
        departemen.style.display = "none"
    }

    else if(role === "operator"){
        kantor.style.display = "block"

        if(kantorValue === ""){
            departemen.style.display = "none"
        }else{
            departemen.style.display = "block"
        }
    }

    else if(role === "user"){

        kantor.style.display = "block"

        if(kantorValue === ""){
            departemen.style.display = "none"
        }else{
            departemen.style.display = "block"
        }

    }

}

document.getElementById("roleSelect").addEventListener("change", toggleForm)
document.getElementById("kantorSelect").addEventListener("change", toggleForm)

document.addEventListener("DOMContentLoaded", toggleForm)

</script>

@endsection