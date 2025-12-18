<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Rekening;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Transfer;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $bulanIni = date('Y-m');

        $totalPemasukan = Pemasukan::where('user_id', $userId)
            ->where('tanggal', 'like', "$bulanIni%")
            ->sum('jumlah');

        $totalPengeluaran = Pengeluaran::where('user_id', $userId)
            ->where('tanggal', 'like', "$bulanIni%")
            ->sum('jumlah');

        $rekeningList = Rekening::where('user_id', $userId)->get();
        
        $totalSaldoBisaDipakai = $rekeningList->sum(function($rek) {
            return $rek->saldo - $rek->minimum_saldo;
        });

        $saldoTunai = $rekeningList->where('tipe', 'TUNAI')->sum('saldo');

        return view('dashboard.index', compact(
            'totalPemasukan', 'totalPengeluaran', 'rekeningList', 
            'totalSaldoBisaDipakai', 'saldoTunai'
        ));
    }

    public function tarikTunai(Request $request)
    {
        $request->validate(['jumlah_tarik' => 'required|numeric', 'rekening_sumber_id' => 'required']);
        $userId = Auth::id();

        DB::transaction(function() use ($request, $userId) {
            $sumber = Rekening::where('user_id', $userId)->find($request->rekening_sumber_id);
            $tujuan = Rekening::where('user_id', $userId)->where('tipe', 'TUNAI')->first();

            if(!$tujuan) throw new \Exception("Rekening Tunai tidak ditemukan");
            if(($sumber->saldo - $sumber->minimum_saldo) < $request->jumlah_tarik) throw new \Exception("Saldo tidak cukup");

            $sumber->decrement('saldo', $request->jumlah_tarik);
            $tujuan->increment('saldo', $request->jumlah_tarik);

            Transfer::create([
                'user_id' => $userId,
                'jumlah' => $request->jumlah_tarik,
                'rekening_sumber_id' => $sumber->id,
                'rekening_tujuan_id' => $tujuan->id,
                'deskripsi' => 'Tarik tunai ke dompet',
                'tanggal' => now()
            ]);
        });

        return back()->with('success', 'Tarik tunai berhasil');
    }
}
