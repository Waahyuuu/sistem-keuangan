<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\Rekening;
use App\Models\Departemen;
use App\Models\Program;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    public function index()
    {
        $query = Transaksi::with(['departemen', 'program', 'kategori', 'rekening']);

        if (Auth::user()->role === User::ROLE_USER) {
            $query->where('departemen_id', Auth::user()->departemen_id);
            $departemensUser = [Auth::user()->departemen];
        } else {
            $departemensUser = Departemen::all();
        }

        $transaksis  = $query->latest()->get();
        $rekenings   = Rekening::all();
        $programs    = Program::all();
        $kategoris   = Kategori::all();

        return view('transaksi.index', compact(
            'transaksis',
            'rekenings',
            'departemensUser',
            'programs',
            'kategoris'
        ));
    }

    public function store(Request $request)
    {
        switch ($request->type_transaksi) {
            case Transaksi::TYPE_PEMASUKAN:
                return $this->storePemasukan($request);

            case Transaksi::TYPE_PENGELUARAN:
                return $this->storePengeluaran($request);

            case Transaksi::TYPE_TRANSFER:
                return $this->storeTransfer($request);

            case Transaksi::TYPE_UTANG:
                return $this->storeUtang($request);

            case Transaksi::TYPE_PIUTANG:
                return $this->storePiutang($request);

            default:
                abort(400, 'Tipe transaksi tidak valid');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PEMASUKAN
    |--------------------------------------------------------------------------
    */

    private function storePemasukan($request)
    {
        $request->validate([
            'rekening_id'       => 'required|exists:rekenings,id',
            'nominal_transaksi' => 'required|numeric|min:1',
            'departemen_id'     => 'required|exists:departemens,id'
        ]);

        $this->checkUserDepartemen($request->departemen_id);

        DB::transaction(function () use ($request) {

            Transaksi::create([
                'type_transaksi'    => Transaksi::TYPE_PEMASUKAN,
                'nominal_transaksi' => $request->nominal_transaksi,
                'keterangan'        => $request->keterangan,
                'tgl_transaksi'     => now(),
                'user_id'           => Auth::id(),
                'rekening_id'       => $request->rekening_id,
                'departemen_id'     => $request->departemen_id,
                'program_id'        => $request->program_id ?? null,
                'kategori_id'       => $request->kategori_id ?? null
            ]);
        });

        return back()->with('success', 'Pemasukan berhasil disimpan');
    }

    /*
    |--------------------------------------------------------------------------
    | PENGELUARAN
    |--------------------------------------------------------------------------
    */

    private function storePengeluaran($request)
    {
        $request->validate([
            'rekening_id'       => 'required|exists:rekenings,id',
            'nominal_transaksi' => 'required|numeric|min:1',
            'departemen_id'     => 'required|exists:departemens,id'
        ]);

        $this->checkUserDepartemen($request->departemen_id);

        $rekening = Rekening::findOrFail($request->rekening_id);

        // ✅ CEK SALDO DARI ACCESSOR
        if ($rekening->saldo_akhir < $request->nominal_transaksi) {
            return back()->withErrors(['Saldo tidak cukup']);
        }

        DB::transaction(function () use ($request) {

            Transaksi::create([
                'type_transaksi'    => Transaksi::TYPE_PENGELUARAN,
                'nominal_transaksi' => $request->nominal_transaksi,
                'keterangan'        => $request->keterangan,
                'tgl_transaksi'     => now(),
                'user_id'           => Auth::id(),
                'rekening_id'       => $request->rekening_id,
                'departemen_id'     => $request->departemen_id,
                'program_id'        => $request->program_id ?? null,
                'kategori_id'       => $request->kategori_id ?? null
            ]);
        });

        return back()->with('success', 'Pengeluaran berhasil disimpan');
    }

    /*
    |--------------------------------------------------------------------------
    | TRANSFER
    |--------------------------------------------------------------------------
    */

    private function storeTransfer($request)
    {
        $request->validate([
            'rekening_id'        => 'required|exists:rekenings,id',
            'rekening_tujuan_id' => 'required|exists:rekenings,id|different:rekening_id',
            'nominal_transaksi'  => 'required|numeric|min:1'
        ]);

        $from = Rekening::findOrFail($request->rekening_id);
        $to   = Rekening::findOrFail($request->rekening_tujuan_id);

        if ($from->saldo_akhir < $request->nominal_transaksi) {
            return back()->withErrors(['Saldo rekening asal tidak cukup']);
        }

        DB::transaction(function () use ($request, $from, $to) {

            // 1️⃣ Catat pengeluaran dari rekening asal
            Transaksi::create([
                'type_transaksi'    => Transaksi::TYPE_PENGELUARAN,
                'nominal_transaksi' => $request->nominal_transaksi,
                'keterangan'        => 'Transfer ke ' . $to->name_rek . ' - ' . $request->keterangan,
                'tgl_transaksi'     => now(),
                'user_id'           => Auth::id(),
                'rekening_id'       => $from->id,
            ]);

            // 2️⃣ Catat pemasukan ke rekening tujuan
            Transaksi::create([
                'type_transaksi'    => Transaksi::TYPE_PEMASUKAN,
                'nominal_transaksi' => $request->nominal_transaksi,
                'keterangan'        => 'Transfer dari ' . $from->name_rek . ' - ' . $request->keterangan,
                'tgl_transaksi'     => now(),
                'user_id'           => Auth::id(),
                'rekening_id'       => $to->id,
            ]);
        });

        return back()->with('success', 'Transfer berhasil dilakukan');
    }

    /*
    |--------------------------------------------------------------------------
    | UTANG
    |--------------------------------------------------------------------------
    */

    private function storeUtang($request)
    {
        $request->validate([
            'nominal_transaksi' => 'required|numeric|min:1',
            'keterangan'        => 'nullable|string'
        ]);

        Transaksi::create([
            'type_transaksi'    => Transaksi::TYPE_UTANG,
            'nominal_transaksi' => $request->nominal_transaksi,
            'keterangan'        => $request->keterangan,
            'tgl_transaksi'     => now(),
            'user_id'           => Auth::id()
        ]);

        return back()->with('success', 'Utang dicatat');
    }

    /*
    |--------------------------------------------------------------------------
    | PIUTANG
    |--------------------------------------------------------------------------
    */

    private function storePiutang($request)
    {
        $request->validate([
            'nominal_transaksi' => 'required|numeric|min:1',
            'keterangan'        => 'nullable|string'
        ]);

        Transaksi::create([
            'type_transaksi'    => Transaksi::TYPE_PIUTANG,
            'nominal_transaksi' => $request->nominal_transaksi,
            'keterangan'        => $request->keterangan,
            'tgl_transaksi'     => now(),
            'user_id'           => Auth::id()
        ]);

        return back()->with('success', 'Piutang dicatat');
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI DEPARTEMEN
    |--------------------------------------------------------------------------
    */

    private function checkUserDepartemen($departemen_id)
    {
        if (
            Auth::user()->role === User::ROLE_USER &&
            Auth::user()->departemen_id != $departemen_id
        ) {
            abort(403, 'Akses ditolak');
        }
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['departemen', 'program', 'kategori']);

        if (
            Auth::user()->role === User::ROLE_USER &&
            Auth::user()->departemen_id != $transaksi->departemen_id
        ) {
            abort(403, 'Akses ditolak');
        }

        return view('transaksi.partials.detail', compact('transaksi'));
    }
}
