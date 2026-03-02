<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rekening;

class DashboardController extends Controller
{
    public function index()
    {
        $rekenings = Rekening::all();

        $totalSaldo = $rekenings->sum('saldo_akhir');

        return view('dashboard', compact('rekenings', 'totalSaldo'));
    }
}
