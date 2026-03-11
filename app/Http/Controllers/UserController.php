<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Kantor;
use App\Models\Departemen;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | LIST USER
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | STORE USER
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required'
        ]);

        $data = $request->all();

        $data['password'] = Hash::make($request->password);

        /*
        |--------------------------------------------------------------------------
        | LOGIC ROLE
        |--------------------------------------------------------------------------
        */

        if ($request->role == 'admin') {
            $data['kantor_id'] = null;
            $data['departemen_id'] = null;
        }

        if ($request->role == 'operator') {
            $data['departemen_id'] = null;
        }

        User::create($data);

        return redirect()
            ->back()
            ->with('success', 'User berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {

        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'role' => 'required'
        ]);

        $data = $request->all();

        /*
        |--------------------------------------------------------------------------
        | PASSWORD
        |--------------------------------------------------------------------------
        */

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        /*
        |--------------------------------------------------------------------------
        | ROLE LOGIC
        |--------------------------------------------------------------------------
        */

        if ($request->role == 'admin') {
            $data['kantor_id'] = null;
            $data['departemen_id'] = null;
        }

        if ($request->role == 'operator') {
            $data['departemen_id'] = null;
        }

        $user->update($data);

        return redirect()
            ->back()
            ->with('success', 'User berhasil diupdate');
    }
}
