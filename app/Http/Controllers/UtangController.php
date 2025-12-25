<?php

namespace App\Http\Controllers;

use App\Models\Utang;
use App\Models\Rekening;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UtangController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $utang = Utang::where('user_id', $userId)
            ->orderBy('status', 'asc') // Belum lunas diatas
            ->get();
        $rekening = Rekening::where('user_id', $userId)->get();

        return view('utang.index', compact('utang', 'rekening'));
    }

    public function store(Request $request)
    {
        Utang::create([
            'user_id' => Auth::id(),
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'sisa_jumlah' => $request->jumlah, // Set sisa_jumlah sama dengan jumlah awal
            'tanggal' => now(), // Set tanggal sekarang
            'jatuh_tempo' => $request->jatuh_tempo,
            'status' => 'Belum Lunas'
        ]);

        return back()->with('success', 'Utang dicatat.');
    }

    public function destroy($id)
    {
        Utang::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Data utang dihapus.');
    }

    public function bayar(Request $request)
    {
        try {
            DB::transaction(function() use ($request) {
                $userId = Auth::id();
                
                // 1. Update Utang jadi Lunas
                Utang::where('user_id', $userId)
                    ->where('id', $request->id_utang)
                    ->update([
                        'status' => 'Lunas',
                        'sisa_jumlah' => 0 // Set sisa_jumlah ke 0 saat lunas
                    ]);

                // 2. Potong Saldo Rekening
                Rekening::where('id', $request->rekening_id)
                    ->decrement('saldo', $request->jumlah_bayar);

                // 3. Catat di Pengeluaran (History)
                Pengeluaran::create([
                    'user_id' => $userId,
                    'kategori' => 'Pembayaran Utang',
                    'deskripsi' => $request->deskripsi_utang,
                    'jumlah' => $request->jumlah_bayar,
                    'rekening_id' => $request->rekening_id,
                    'tanggal' => now()
                ]);
            });

            return back()->with('success', 'Utang berhasil dibayar!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}