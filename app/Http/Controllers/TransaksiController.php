<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Transaksi;
use App\Models\Rekening;
use App\Models\Departemen;
use App\Models\Program;
use App\Models\Kategori;
use App\Models\User;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->tanggal ?? now()->toDateString();
        $today = now()->toDateString();

        if ($tanggal > $today) {
            return redirect()
                ->route('transaksi.index')
                ->with('error', 'Tanggal tidak terdeteksi');
        }

        $query = Transaksi::with([
            'departemen',
            'program',
            'kategoris',
            'rekening',
            'rekeningTujuan'
        ]);

        // Role filter
        if (Auth::user()->role === User::ROLE_USER) {
            $query->where('departemen_id', Auth::user()->departemen_id);
            $departemensUser = [Auth::user()->departemen];
        } else {
            $departemensUser = Departemen::all();
        }

        // Filter tanggal
        $query->whereDate('created_at', $tanggal);

        $transaksis = $query->latest()->get();

        return view('transaksi.index', [
            'transaksis'      => $transaksis,
            'rekenings'       => Rekening::all(),
            'departemensUser' => $departemensUser,
            'programs'        => Program::all(),
            'kategoris'       => Kategori::all(),
            'tanggal'         => $tanggal,
            'isFilter'        => $request->has('tanggal')
        ]);
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

    private function storePemasukan(Request $request)
    {
        $request->validate([
            'rekening_id'       => 'required|exists:rekenings,id',
            'nominal_transaksi' => 'required|numeric|min:1',
            'departemen_id'     => 'required|exists:departemens,id',
            'kategori_id'       => 'nullable|array',
            'kategori_id.*'     => 'exists:kategoris,id',
            'bukti_nota'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $this->checkUserDepartemen($request->departemen_id);

        DB::transaction(function () use ($request) {

            $filePath = $this->handleUpload($request);

            $transaksi = Transaksi::create([
                'type_transaksi'    => Transaksi::TYPE_PEMASUKAN,
                'nominal_transaksi' => $request->nominal_transaksi,
                'keterangan'        => $request->keterangan,
                'tgl_transaksi'     => now(),
                'user_id'           => Auth::id(),
                'rekening_id'       => $request->rekening_id,
                'departemen_id'     => $request->departemen_id,
                'program_id'        => $request->program_id,
                'bukti_nota'        => $filePath
            ]);

            if ($request->kategori_id) {
                $transaksi->kategoris()->sync($request->kategori_id);
            }
        });

        return back()->with('success', 'Pemasukan berhasil disimpan');
    }

    /*
    |--------------------------------------------------------------------------
    | PENGELUARAN
    |--------------------------------------------------------------------------
    */

    private function storePengeluaran(Request $request)
    {
        $request->validate([
            'rekening_id'       => 'required|exists:rekenings,id',
            'nominal_transaksi' => 'required|numeric|min:1',
            'departemen_id'     => 'required|exists:departemens,id',
            'kategori_id'       => 'nullable|array',
            'kategori_id.*'     => 'exists:kategoris,id',
            'bukti_nota'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $this->checkUserDepartemen($request->departemen_id);

        $rekening = Rekening::findOrFail($request->rekening_id);

        if ($rekening->saldo_akhir < $request->nominal_transaksi) {
            return back()->withErrors(['Saldo tidak cukup']);
        }

        DB::transaction(function () use ($request) {

            $filePath = $this->handleUpload($request);

            $transaksi = Transaksi::create([
                'type_transaksi'    => Transaksi::TYPE_PENGELUARAN,
                'nominal_transaksi' => $request->nominal_transaksi,
                'keterangan'        => $request->keterangan,
                'tgl_transaksi'     => now(),
                'user_id'           => Auth::id(),
                'rekening_id'       => $request->rekening_id,
                'departemen_id'     => $request->departemen_id,
                'program_id'        => $request->program_id,
                'bukti_nota'        => $filePath
            ]);

            if ($request->kategori_id) {
                $transaksi->kategoris()->sync($request->kategori_id);
            }
        });

        return back()->with('success', 'Pengeluaran berhasil disimpan');
    }

    /*
    |--------------------------------------------------------------------------
    | TRANSFER
    |--------------------------------------------------------------------------
    */

    private function storeTransfer(Request $request)
    {
        $request->validate([
            'rekening_id'        => 'required|exists:rekenings,id',
            'rekening_tujuan_id' => 'required|exists:rekenings,id|different:rekening_id',
            'nominal_transaksi'  => 'required|numeric|min:1',
            'keterangan'         => 'nullable|string',
            'bukti_nota'         => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $from = Rekening::findOrFail($request->rekening_id);

        // VALIDASI SALDO (masih boleh)
        if ($from->saldo_akhir < $request->nominal_transaksi) {
            return back()->withErrors(['Saldo rekening asal tidak cukup']);
        }

        DB::transaction(function () use ($request) {

            $filePath = $this->handleUpload($request);

            // CUKUP 1 TRANSAKSI
            Transaksi::create([
                'type_transaksi'     => Transaksi::TYPE_TRANSFER,
                'nominal_transaksi'  => $request->nominal_transaksi,
                'keterangan'         => $request->keterangan,
                'tgl_transaksi'      => now(),
                'user_id'            => Auth::id(),
                'rekening_id'        => $request->rekening_id,
                'rekening_tujuan_id' => $request->rekening_tujuan_id,
                'bukti_nota'         => $filePath
            ]);
        });

        return back()->with('success', 'Transfer berhasil dilakukan');
    }

    /*
    |--------------------------------------------------------------------------
    | UTANG
    |--------------------------------------------------------------------------
    */

    private function storeUtang(Request $request)
    {
        $request->validate([
            'nominal_transaksi' => 'required|numeric|min:1',
            'keterangan'        => 'nullable|string',
            'bukti_nota'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $filePath = $this->handleUpload($request);

        Transaksi::create([
            'type_transaksi'    => Transaksi::TYPE_UTANG,
            'nominal_transaksi' => $request->nominal_transaksi,
            'keterangan'        => $request->keterangan,
            'tgl_transaksi'     => now(),
            'user_id'           => Auth::id(),
            'bukti_nota'        => $filePath
        ]);

        return back()->with('success', 'Utang dicatat');
    }

    /*
    |--------------------------------------------------------------------------
    | PIUTANG
    |--------------------------------------------------------------------------
    */

    private function storePiutang(Request $request)
    {
        $request->validate([
            'nominal_transaksi' => 'required|numeric|min:1',
            'keterangan'        => 'nullable|string',
            'bukti_nota'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $filePath = $this->handleUpload($request);

        Transaksi::create([
            'type_transaksi'    => Transaksi::TYPE_PIUTANG,
            'nominal_transaksi' => $request->nominal_transaksi,
            'keterangan'        => $request->keterangan,
            'tgl_transaksi'     => now(),
            'user_id'           => Auth::id(),
            'bukti_nota'        => $filePath
        ]);

        return back()->with('success', 'Piutang dicatat');
    }

    /*
    |--------------------------------------------------------------------------
    | HANDLE UPLOAD FILE
    |--------------------------------------------------------------------------
    */

    private function handleUpload(Request $request)
    {
        if ($request->hasFile('bukti_nota')) {
            return $request->file('bukti_nota')
                ->store('bukti_transaksi', 'public');
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI DEPARTEMEN USER
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
        $transaksi->load(['departemen', 'program', 'kategoris']);

        if (
            Auth::user()->role === User::ROLE_USER &&
            Auth::user()->departemen_id != $transaksi->departemen_id
        ) {
            abort(403, 'Akses ditolak');
        }

        return view('transaksi.partials.detail', compact('transaksi'));
    }
}
