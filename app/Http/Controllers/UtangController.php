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
            ->orderBy('status', 'asc') // Tampilkan yang belum lunas dulu
            ->orderBy('jatuh_tempo', 'asc')
            ->get();
        $rekening = Rekening::where('user_id', $userId)->get();

        return view('utang.index', compact('utang', 'rekening'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required',
            'jumlah' => 'required|numeric|min:1',
        ]);

        Utang::create([
            'user_id' => Auth::id(),
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            
            // --- PERBAIKAN DI SINI ---
            // Sisa jumlah awal = Jumlah utang
            'sisa_jumlah' => $request->jumlah, 
            // -------------------------

            'jatuh_tempo' => $request->jatuh_tempo,
            'status' => 'Belum Lunas'
        ]);

        return back()->with('success', 'Utang berhasil dicatat.');
    }

    public function bayar(Request $request)
    {
        $request->validate([
            'id_utang' => 'required',
            'rekening_id' => 'required',
            'jumlah_bayar' => 'required|numeric|min:1'
        ]);

        try {
            DB::transaction(function() use ($request) {
                $userId = Auth::id();
                
                // 1. Ambil Data Utang
                $utang = Utang::where('user_id', $userId)->findOrFail($request->id_utang);

                // Cek apakah pembayaran melebihi sisa utang
                if ($request->jumlah_bayar > $utang->sisa_jumlah) {
                    throw new \Exception("Jumlah bayar melebihi sisa utang (Sisa: " . number_format($utang->sisa_jumlah) . ")");
                }

                // 2. Kurangi Sisa Jumlah
                $sisaBaru = $utang->sisa_jumlah - $request->jumlah_bayar;
                
                $updateData = ['sisa_jumlah' => $sisaBaru];
                
                // Jika lunas (sisa 0), update status
                if ($sisaBaru <= 0) {
                    $updateData['status'] = 'Lunas';
                }

                $utang->update($updateData);

                // 3. Potong Saldo Rekening
                $rekening = Rekening::where('id', $request->rekening_id)->first();
                if (!$rekening) throw new \Exception("Rekening tidak ditemukan.");
                
                // Cek saldo rekening (Logika biasa + 50rb)
                if (($rekening->saldo - $rekening->minimum_saldo) < $request->jumlah_bayar) {
                    throw new \Exception("Saldo rekening tidak cukup!");
                }

                $rekening->decrement('saldo', $request->jumlah_bayar);

                // 4. Catat Otomatis di Pengeluaran
                Pengeluaran::create([
                    'user_id' => $userId,
                    'kategori' => 'Pembayaran Utang',
                    'deskripsi' => $request->deskripsi_utang . " (" . $utang->deskripsi . ")",
                    'jumlah' => $request->jumlah_bayar,
                    'rekening_id' => $request->rekening_id,
                    'tanggal' => now()
                ]);
            });

            return back()->with('success', 'Pembayaran berhasil dicatat!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal bayar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        Utang::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Data utang dihapus.');
    }

    public function edit($id)
    {
        $utang = Utang::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($utang);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'deskripsi' => 'required',
            'jumlah' => 'required|numeric|min:1',
            'jatuh_tempo' => 'nullable|date'
        ]);

        $utang = Utang::where('user_id', Auth::id())->findOrFail($id);

        // Jika jumlah berubah, update sisa_jumlah juga (jika belum lunas)
        $sisaJumlah = $request->jumlah;
        if ($utang->status == 'Belum Lunas') {
            // Jika jumlah baru lebih besar, tambah sisa; jika lebih kecil, kurangi sisa tapi tidak kurang dari 0
            $selisih = $request->jumlah - $utang->jumlah;
            $sisaJumlah = max(0, $utang->sisa_jumlah + $selisih);
        }

        $utang->update([
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'sisa_jumlah' => $sisaJumlah,
            'jatuh_tempo' => $request->jatuh_tempo
        ]);

        return back()->with('success', 'Utang berhasil diperbarui.');
    }
}