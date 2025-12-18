<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Rekening;
use App\Models\KategoriPengeluaran; // Pastikan Model Kategori Pengeluaran ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Pengeluaran::where('user_id', $userId)->with('rekening');

        if ($request->filter == 'harian') $query->whereDate('tanggal', today());
        elseif ($request->filter == 'mingguan') $query->whereBetween('tanggal', [now()->startOfWeek(), now()->endOfWeek()]);

        $pengeluaran = $query->orderBy('tanggal', 'desc')->get();
        
        // Sesuaikan Model Kategori Anda
        $kategori = \App\Models\KategoriPengeluaran::where('user_id', $userId)->get();
        $rekening = Rekening::where('user_id', $userId)->get();

        return view('pengeluaran.index', compact('pengeluaran', 'kategori', 'rekening'));
    }

    public function store(Request $request)
    {
        DB::transaction(function() use ($request) {
            $userId = Auth::id();
            $rekening = Rekening::where('user_id', $userId)->find($request->rekening_id);

            // Cek saldo cukup atau tidak (opsional, bisa dihapus jika ingin saldo minus)
            if (($rekening->saldo - $rekening->minimum_saldo) < $request->jumlah) {
                // throw new \Exception("Saldo tidak mencukupi!"); // Uncomment jika ingin strict
            }

            Pengeluaran::create([
                'user_id' => $userId,
                'kategori' => $request->kategori,
                'deskripsi' => $request->deskripsi,
                'jumlah' => $request->jumlah,
                'rekening_id' => $request->rekening_id,
                'tanggal' => $request->tanggal
            ]);

            // Kurangi Saldo
            $rekening->decrement('saldo', $request->jumlah);
        });

        return back()->with('success', 'Pengeluaran dicatat.');
    }

    public function destroy($id)
    {
        DB::transaction(function() use ($id) {
            $item = Pengeluaran::where('user_id', Auth::id())->findOrFail($id);
            // Kembalikan Saldo (Tambah lagi)
            Rekening::where('id', $item->rekening_id)->increment('saldo', $item->jumlah);
            $item->delete();
        });

        return back()->with('success', 'Pengeluaran dihapus.');
    }
}