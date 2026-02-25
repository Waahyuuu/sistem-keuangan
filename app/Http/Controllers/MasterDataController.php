<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rekening;
use App\Models\Kantor;
use App\Models\Departemen;
use App\Models\Program;
use App\Models\Kategori;
use Illuminate\Validation\Rule;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $allowedTabs = ['rekening', 'kantor', 'departemen', 'program', 'kategori'];

        $tab = $request->get('tab', 'rekening');

        if (!in_array($tab, $allowedTabs)) {
            $tab = 'rekening';
        }

        $rekenings = [];
        $kantors = [];
        $departemens = [];
        $programs = [];
        $kategoris = [];

        if ($tab == 'rekening') {
            $rekenings = Rekening::latest()->get();
        }

        if ($tab == 'kantor') {
            $kantors = Kantor::latest()->get();
        }

        if ($tab == 'departemen') {
            $kantors = Kantor::latest()->get();

            $departemens = Departemen::whereNull('parent_id')
                ->with('kantor')
                ->withCount('children')
                ->latest()
                ->get();
        }

        if ($tab == 'program') {

            $departemens = Departemen::with('children')
                ->whereNull('parent_id')
                ->orderBy('name_dep')
                ->get();

            $programs = Program::with('departemen.parent')
                ->latest()
                ->get();
        }

        if ($tab == 'kategori') {
            $programs = Program::orderBy('name_prog')->get();
            $kategoris = Kategori::with('program')->latest()->get();
        }

        return view('master-data.index', compact(
            'tab',
            'rekenings',
            'kantors',
            'departemens',
            'programs',
            'kategoris'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | REKENING START
    |--------------------------------------------------------------------------
    */

    public function storeRekening(Request $request)
    {
        $request->validate(
            [
                'name_rek' => 'required',
                'no_rek' => 'required|unique:rekenings,no_rek',
            ],
            [
                'name_rek.required' => 'Nama rekening wajib diisi.',
                'no_rek.required' => 'Nomor rekening wajib diisi.',
                'no_rek.unique' => 'Nomor rekening sudah digunakan.',
            ]
        );

        Rekening::create([
            'name_rek' => $request->name_rek,
            'no_rek' => $request->no_rek,
            'saldo_awal' => 0,
            'is_active' => true
        ]);

        return back()->with('success', 'Rekening berhasil ditambahkan');
    }

    public function updateRekening(Request $request, $id)
    {
        $rekening = Rekening::findOrFail($id);

        $request->validate(
            [
                'name_rek' => 'required',
                'no_rek' => [
                    'required',
                    Rule::unique('rekenings', 'no_rek')->ignore($rekening->id),
                ],
            ],
            [
                'name_rek.required' => 'Nama rekening wajib diisi.',
                'no_rek.required' => 'Nomor rekening wajib diisi.',
                'no_rek.unique' => 'Nomor rekening sudah digunakan.',
            ]
        );

        $rekening->update([
            'name_rek' => $request->name_rek,
            'no_rek' => $request->no_rek
        ]);

        return back()->with('success', 'Rekening berhasil diperbarui');
    }

    public function deleteRekening($id)
    {
        $rekening = Rekening::findOrFail($id);
        $rekening->delete();

        return back()->with('success', 'Rekening berhasil dihapus');
    }

    public function toggleRekening($id)
    {
        $rekening = Rekening::findOrFail($id);

        $rekening->update([
            'is_active' => !$rekening->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $rekening->is_active
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REKENING END
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | KANTOR START
    |--------------------------------------------------------------------------
    */

    public function storeKantor(Request $request)
    {
        $request->validate(
            [
                'name_ktr' => 'required',
                'code_ktr' => 'required|unique:kantors,code_ktr',
                'type_ktr' => 'required|in:pusat,cabang',
            ],
            [
                'name_ktr.required' => 'Nama kantor wajib diisi.',
                'code_ktr.required' => 'Kode kantor wajib diisi.',
                'code_ktr.unique' => 'Kode kantor sudah digunakan.',
                'type_ktr.required' => 'Tipe kantor wajib dipilih.',
                'type_ktr.in' => 'Tipe kantor tidak valid.',
            ]
        );

        Kantor::create([
            'name_ktr' => $request->name_ktr,
            'code_ktr' => $request->code_ktr,
            'type_ktr' => $request->type_ktr,
        ]);

        return back()->with('success', 'Kantor berhasil ditambahkan');
    }

    public function updateKantor(Request $request, $id)
    {
        $kantor = Kantor::findOrFail($id);

        $request->validate(
            [
                'name_ktr' => 'required',
                'code_ktr' => [
                    'required',
                    Rule::unique('kantors', 'code_ktr')->ignore($kantor->id),
                ],
                'type_ktr' => 'required|in:pusat,cabang',
            ],
            [
                'name_ktr.required' => 'Nama kantor wajib diisi.',
                'code_ktr.required' => 'Kode kantor wajib diisi.',
                'code_ktr.unique' => 'Kode kantor sudah digunakan.',
                'type_ktr.required' => 'Tipe kantor wajib dipilih.',
                'type_ktr.in' => 'Tipe kantor tidak valid.',
            ]
        );

        $kantor->update([
            'name_ktr' => $request->name_ktr,
            'code_ktr' => $request->code_ktr,
            'type_ktr' => $request->type_ktr,
        ]);

        return back()->with('success', 'Kantor berhasil diperbarui');
    }

    public function deleteKantor($id)
    {
        $kantor = Kantor::findOrFail($id);
        $kantor->delete();

        return back()->with('success', 'Kantor berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | KANTOR END
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | DEPARTEMEN START
    |--------------------------------------------------------------------------
    */

    public function storeDepartemen(Request $request)
    {
        $request->validate([
            'name_dep' => [
                'required',
                Rule::unique('departemens')
                    ->where(function ($query) use ($request) {
                        return $query->where('kantor_id', $request->kantor_id)
                            ->whereNull('parent_id');
                    }),
            ],
            'kantor_id' => 'required|exists:kantors,id',
        ], [
            'name_dep.required' => 'Nama departemen wajib diisi.',
            'name_dep.unique' => 'Nama departemen sudah digunakan di kantor ini.',
            'kantor_id.required' => 'Kantor wajib dipilih.',
        ]);

        Departemen::create([
            'name_dep' => $request->name_dep,
            'kantor_id' => $request->kantor_id,
            'parent_id' => null
        ]);

        return back()->with('success', 'Departemen berhasil ditambahkan');
    }

    public function updateDepartemen(Request $request, $id)
    {
        $departemen = Departemen::findOrFail($id);

        $request->validate([
            'name_dep' => [
                'required',
                Rule::unique('departemens')
                    ->where(function ($query) use ($request) {
                        return $query->where('kantor_id', $request->kantor_id)
                            ->whereNull('parent_id');
                    })
                    ->ignore($departemen->id),
            ],
            'kantor_id' => 'required|exists:kantors,id',
        ], [
            'name_dep.required' => 'Nama departemen wajib diisi.',
            'name_dep.unique' => 'Nama departemen sudah digunakan di kantor ini.',
        ]);

        $departemen->update([
            'name_dep' => $request->name_dep,
            'kantor_id' => $request->kantor_id,
        ]);

        return back()->with('success', 'Departemen berhasil diperbarui');
    }

    public function deleteDepartemen($id)
    {
        $departemen = Departemen::findOrFail($id);
        $departemen->delete();

        return back()->with('success', 'Departemen berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | DEPARTEMEN END
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SUB DEPARTEMEN START
    |--------------------------------------------------------------------------
    */

    public function subDepartemen(Departemen $departemen)
    {
        $parent = $departemen->load('kantor');

        if ($parent->parent_id !== null) {
            abort(404);
        }

        $subDepartemens = $parent->children()
            ->latest()
            ->get();

        return view(
            'master-data.departemen.sub-departemen',
            compact('parent', 'subDepartemens')
        );
    }

    public function storeSubDepartemen(Request $request, Departemen $departemen)
    {
        $request->validate([
            'name_dep' => [
                'required',
                Rule::unique('departemens')
                    ->where(function ($query) use ($departemen) {
                        return $query->where('parent_id', $departemen->id);
                    }),
            ]
        ], [
            'name_dep.required' => 'Nama sub departemen wajib diisi.',
            'name_dep.unique' => 'Nama sub departemen sudah digunakan.',
        ]);

        Departemen::create([
            'name_dep'   => $request->name_dep,
            'kantor_id'  => $departemen->kantor_id,
            'parent_id'  => $departemen->id
        ]);

        return back()->with('success', 'Sub departemen berhasil ditambahkan');
    }

    public function updateSubDepartemen(Request $request, Departemen $departemen)
    {
        $request->validate([
            'name_dep' => [
                'required',
                Rule::unique('departemens')
                    ->where(function ($query) use ($departemen) {
                        return $query->where('parent_id', $departemen->parent_id);
                    })
                    ->ignore($departemen->id),
            ]
        ], [
            'name_dep.required' => 'Nama sub departemen wajib diisi.',
            'name_dep.unique' => 'Nama sub departemen sudah digunakan.',
        ]);

        $departemen->update([
            'name_dep' => $request->name_dep
        ]);

        return back()->with('success', 'Sub departemen berhasil diperbarui');
    }

    public function deleteSubDepartemen(Departemen $departemen)
    {
        $departemen->delete();

        return back()->with('success', 'Sub departemen berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | SUB DEPARTEMEN END
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | PROGRAM START
    |--------------------------------------------------------------------------
    */

    public function storeProgram(Request $request)
    {
        $request->validate([
            'name_prog' => [
                'required',
                Rule::unique('programs')
                    ->where(function ($query) use ($request) {
                        return $query->where('departemen_id', $request->departemen_id);
                    }),
            ],
            'departemen_id' => 'required|exists:departemens,id'
        ], [
            'name_prog.required' => 'Nama program wajib diisi.',
            'name_prog.unique' => 'Nama program sudah digunakan di departemen ini.',
            'departemen_id.required' => 'Departemen wajib dipilih.'
        ]);

        Program::create([
            'name_prog' => $request->name_prog,
            'departemen_id' => $request->departemen_id
        ]);

        return back()->with('success', 'Program berhasil ditambahkan');
    }

    public function updateProgram(Request $request, Program $program)
    {
        $request->validate([
            'name_prog' => [
                'required',
                Rule::unique('programs')
                    ->where(function ($query) use ($request) {
                        return $query->where('departemen_id', $request->departemen_id);
                    })
                    ->ignore($program->id),
            ],
            'departemen_id' => 'required|exists:departemens,id'
        ], [
            'name_prog.required' => 'Nama program wajib diisi.',
            'name_prog.unique' => 'Nama program sudah digunakan di departemen ini.',
        ]);

        $program->update([
            'name_prog' => $request->name_prog,
            'departemen_id' => $request->departemen_id
        ]);

        return back()->with('success', 'Program berhasil diperbarui');
    }

    public function deleteProgram(Program $program)
    {
        $program->delete();

        return back()->with('success', 'Program berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | PROGRAM END
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | KATEGORI START
    |--------------------------------------------------------------------------
    */

    public function storeKategori(Request $request)
    {
        $request->validate([
            'name_ktgr' => [
                'required',
                Rule::unique('kategoris')->where(function ($query) use ($request) {
                    return $query->where('program_id', $request->program_id);
                }),
            ],
            'type_ktgr' => 'required|in:pemasukan,pengeluaran',
            'program_id' => 'required|exists:programs,id',
            'color_ktgr' => 'nullable|string|max:7',
        ], [
            'name_ktgr.required' => 'Nama kategori wajib diisi.',
            'name_ktgr.unique' => 'Nama kategori sudah digunakan di program ini.',
            'type_ktgr.required' => 'Tipe kategori wajib dipilih.',
            'program_id.required' => 'Program wajib dipilih.',
        ]);

        Kategori::create([
            'name_ktgr' => $request->name_ktgr,
            'type_ktgr' => $request->type_ktgr,
            'color_ktgr' => $request->color_ktgr,
            'program_id' => $request->program_id,
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan');
    }

    public function updateKategori(Request $request, Kategori $kategori)
    {
        $request->validate([
            'name_ktgr' => [
                'required',
                Rule::unique('kategoris')->where(function ($query) use ($request) {
                    return $query->where('program_id', $request->program_id);
                })->ignore($kategori->id),
            ],
            'type_ktgr' => 'required|in:pemasukan,pengeluaran',
            'program_id' => 'required|exists:programs,id',
            'color_ktgr' => 'nullable|string|max:7',
        ], [
            'name_ktgr.required' => 'Nama kategori wajib diisi.',
            'name_ktgr.unique' => 'Nama kategori sudah digunakan di program ini.',
            'type_ktgr.required' => 'Tipe kategori wajib dipilih.',
            'program_id.required' => 'Program wajib dipilih.',
        ]);

        $kategori->update([
            'name_ktgr' => $request->name_ktgr,
            'type_ktgr' => $request->type_ktgr,
            'color_ktgr' => $request->color_ktgr,
            'program_id' => $request->program_id,
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui');
    }

    public function deleteKategori(Kategori $kategori)
    {
        $kategori->delete();
        return back()->with('success', 'Kategori berhasil dihapus');
    }

    /*
    |--------------------------------------------------------------------------
    | KATEGORI END
    |--------------------------------------------------------------------------
    */
}
