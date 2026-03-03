<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rekening;
use App\Models\Transaksi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        $bulanIniStart  = $now->copy()->startOfMonth();
        $bulanIniEnd    = $now->copy()->endOfMonth();

        $bulanLaluStart = $now->copy()->subMonth()->startOfMonth();
        $bulanLaluEnd   = $now->copy()->subMonth()->endOfMonth();

        // ==============================
        // REKENING
        // ==============================
        $rekenings = Rekening::where('is_active', true)
            ->get()
            ->sortByDesc('saldo_akhir')
            ->take(5)
            ->values();
        $totalSaldo = $rekenings->sum('saldo_akhir');

        // ==============================
        // PEMASUKAN
        // ==============================
        $totalPemasukan = Transaksi::where('type_transaksi', Transaksi::TYPE_PEMASUKAN)
            ->whereBetween('tgl_transaksi', [$bulanIniStart, $bulanIniEnd])
            ->sum('nominal_transaksi');

        $pemasukanBulanLalu = Transaksi::where('type_transaksi', Transaksi::TYPE_PEMASUKAN)
            ->whereBetween('tgl_transaksi', [$bulanLaluStart, $bulanLaluEnd])
            ->sum('nominal_transaksi');

        // ==============================
        // PENGELUARAN
        // ==============================
        $totalPengeluaran = Transaksi::where('type_transaksi', Transaksi::TYPE_PENGELUARAN)
            ->whereBetween('tgl_transaksi', [$bulanIniStart, $bulanIniEnd])
            ->sum('nominal_transaksi');

        $pengeluaranBulanLalu = Transaksi::where('type_transaksi', Transaksi::TYPE_PENGELUARAN)
            ->whereBetween('tgl_transaksi', [$bulanLaluStart, $bulanLaluEnd])
            ->sum('nominal_transaksi');

        // ==============================
        // PERSENTASE
        // ==============================
        $persenPemasukan = $pemasukanBulanLalu > 0
            ? (($totalPemasukan - $pemasukanBulanLalu) / $pemasukanBulanLalu) * 100
            : 0;

        $persenPengeluaran = $pengeluaranBulanLalu > 0
            ? (($totalPengeluaran - $pengeluaranBulanLalu) / $pengeluaranBulanLalu) * 100
            : 0;

        return view('dashboard', compact(
            'rekenings',
            'totalSaldo',
            'totalPemasukan',
            'totalPengeluaran',
            'persenPemasukan',
            'persenPengeluaran'
        ));
    }
}
