<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rekening;
use App\Models\Transaksi;
use App\Models\Departemen;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
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
        $hitungPersen = function ($sekarang, $lalu) {
            if ($lalu > 0) {
                return round((($sekarang - $lalu) / $lalu) * 100, 1);
            }

            if ($lalu == 0 && $sekarang > 0) {
                return 100;
            }

            return 0;
        };

        $persenPemasukan   = $hitungPersen($totalPemasukan, $pemasukanBulanLalu);
        $persenPengeluaran = $hitungPersen($totalPengeluaran, $pengeluaranBulanLalu);

        // =====================================================
        // ================= ANALITIK ARUS KAS =================
        // =====================================================

        $currentYear  = $now->year;
        $selectedYear = $request->tahun ?? $currentYear;
        $selectedDepartemen = $request->departemen ?? 'all';

        $years = collect(range($currentYear - 4, $currentYear))->reverse();

        $query = Transaksi::whereYear('tgl_transaksi', $selectedYear)
            ->whereIn('type_transaksi', [
                Transaksi::TYPE_PEMASUKAN,
                Transaksi::TYPE_PENGELUARAN
            ]);

        if ($selectedDepartemen !== 'all') {
            $query->where('departemen_id', $selectedDepartemen);
        }

        $analitik = $query
            ->select(
                DB::raw('MONTH(tgl_transaksi) as bulan'),
                'type_transaksi',
                DB::raw('SUM(nominal_transaksi) as total')
            )
            ->groupBy('bulan', 'type_transaksi')
            ->get();

        $dataPemasukan   = array_fill(1, 12, 0);
        $dataPengeluaran = array_fill(1, 12, 0);

        foreach ($analitik as $row) {
            if ($row->type_transaksi == Transaksi::TYPE_PEMASUKAN) {
                $dataPemasukan[$row->bulan] = $row->total;
            } else {
                $dataPengeluaran[$row->bulan] = $row->total;
            }
        }

        $departemens = Departemen::orderBy('name_dep')->get();

        $dataPemasukan   = array_values($dataPemasukan);
        $dataPengeluaran = array_values($dataPengeluaran);

        // ==============================
        // TRANSAKSI TERBARU
        // ==============================
        $transaksiTerbaru = Transaksi::latest()
            ->take(4)
            ->get();

        return view('dashboard', compact(
            'rekenings',
            'totalSaldo',
            'totalPemasukan',
            'totalPengeluaran',
            'persenPemasukan',
            'persenPengeluaran',
            'years',
            'selectedYear',
            'selectedDepartemen',
            'departemens',
            'dataPemasukan',
            'dataPengeluaran',
            'transaksiTerbaru'
        ));
    }
}
