<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Rekening;
use App\Models\KategoriPengeluaran;
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
        elseif ($request->filter == 'bulanan') $query->whereMonth('tanggal', date('m'));

        $pengeluaran = $query->orderBy('tanggal', 'desc')->get();
        
        // Pastikan Model KategoriPengeluaran sudah dibuat, jika belum bisa pakai array manual atau model Kategori biasa
        // Cek apakah Anda punya model App\Models\KategoriPengeluaran, jika tidak ubah ke App\Models\Kategori
        $kategori = \App\Models\KategoriPengeluaran::where('user_id', $userId)->get(); 
        
        $rekening = Rekening::where('user_id', $userId)->get();

        return view('pengeluaran.index', compact('pengeluaran', 'kategori', 'rekening'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|numeric',
            'rekening_id' => 'required',
            'kategori' => 'required',
            'tanggal' => 'required'
        ]);

        DB::transaction(function() use ($request) {
            $userId = Auth::id();
            $rekening = Rekening::where('user_id', $userId)->find($request->rekening_id);

            // LOGIKA PERBAIKAN: 
            // Cek apakah deskripsi diisi? Jika kosong, pakai nama kategori.
            $deskripsiFinal = $request->deskripsi ? $request->deskripsi : $request->kategori;

            Pengeluaran::create([
                'user_id' => $userId,
                'kategori' => $request->kategori,
                'deskripsi' => $deskripsiFinal, // <--- Tidak akan NULL lagi
                'jumlah' => $request->jumlah,
                'rekening_id' => $request->rekening_id,
                'tanggal' => $request->tanggal
            ]);

            // Kurangi Saldo Rekening
            $rekening->decrement('saldo', $request->jumlah);
        });

        return back()->with('success', 'Pengeluaran berhasil dicatat.');
    }

    // Method Baru: Tambah Kategori Pengeluaran
    public function storeKategori(Request $request)
    {
        $request->validate(['nama_kategori' => 'required']);
        KategoriPengeluaran::create([
            'user_id' => Auth::id(),
            'nama_kategori' => $request->nama_kategori
        ]);
        return back()->with('success', 'Kategori pengeluaran baru ditambahkan.');
    }

    public function destroy($id)
    {
        DB::transaction(function() use ($id) {
            $data = Pengeluaran::where('user_id', Auth::id())->findOrFail($id);
            // Kembalikan Saldo (Refund) ke rekening
            Rekening::where('id', $data->rekening_id)->increment('saldo', $data->jumlah);
            $data->delete();
        });

        return back()->with('success', 'Data dihapus dan saldo dikembalikan.');
    }
}