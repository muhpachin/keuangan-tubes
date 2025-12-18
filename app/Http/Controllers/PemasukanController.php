<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Rekening;
use App\Models\Kategori; // Perhatikan nama model sesuai tabel 'kategori'
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PemasukanController extends Controller
{
    public function index() {
        $userId = Auth::id();
        $pemasukan = Pemasukan::where('user_id', $userId)->with('rekening')->orderBy('tanggal', 'desc')->get();
        $kategori = Kategori::where('user_id', $userId)->get();
        $rekening = Rekening::where('user_id', $userId)->where('tipe', '!=', 'TUNAI')->get();
        
        return view('pemasukan.index', compact('pemasukan', 'kategori', 'rekening'));
    }

    public function store(Request $request) {
        DB::transaction(function() use ($request) {
            Pemasukan::create([
                'user_id' => Auth::id(),
                'kategori' => $request->kategori,
                'deskripsi' => $request->deskripsi,
                'jumlah' => $request->jumlah,
                'rekening_id' => $request->rekening_id,
                'tanggal' => $request->tanggal
            ]);
            Rekening::where('id', $request->rekening_id)->increment('saldo', $request->jumlah);
        });
        return back()->with('success', 'Pemasukan dicatat');
    }
    
    // Tambahkan method update & destroy dengan logika revert saldo (kurangi saldo lama, tambah baru)
}
