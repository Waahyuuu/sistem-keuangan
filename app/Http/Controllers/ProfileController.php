<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kantor;
use App\Models\Departemen;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

    public function index()
    {
        $users = User::with(['kantor', 'departemen'])->get();
        $kantors = Kantor::all();
        $departemens = Departemen::all();

        return view('pengaturan-akun', compact(
            'users',
            'kantors',
            'departemens'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'kantor_id' => $request->kantor_id,
            'departemen_id' => $request->departemen_id
        ]);

        return redirect()->route('pengaturan')
            ->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => "required|email|unique:users,email,$id",
            'role' => 'required'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'kantor_id' => $request->kantor_id,
            'departemen_id' => $request->departemen_id
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('pengaturan')
            ->with('success', 'Pengguna berhasil diupdate');
    }
}
