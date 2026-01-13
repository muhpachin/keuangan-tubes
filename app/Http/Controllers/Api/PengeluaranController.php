<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PengeluaranController extends Controller
{
    public function index()
    {
        $data = Pengeluaran::where('user_id', Auth::id())
            ->with('rekening')
            ->orderBy('tanggal', 'desc')
            ->paginate(10);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|numeric|min:1',
            'rekening_id' => 'required|exists:rekenings,id',
            'kategori' => 'required|string',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string'
        ]);

        if ($validator->fails()) return response()->json($validator->errors(), 422);

        try {
            DB::transaction(function() use ($request) {
                // Cek Saldo Cukup atau Tidak
                $rekening = Rekening::find($request->rekening_id);
                if($rekening->saldo < $request->jumlah) {
                    throw new \Exception("Saldo tidak mencukupi!");
                }

                Pengeluaran::create([
                    'user_id' => Auth::id(),
                    'kategori' => $request->kategori,
                    'deskripsi' => $request->deskripsi ?? $request->kategori,
                    'jumlah' => $request->jumlah,
                    'rekening_id' => $request->rekening_id,
                    'tanggal' => $request->tanggal
                ]);

                // Update Saldo (Berkurang)
                $rekening->decrement('saldo', $request->jumlah);
            });

            return response()->json(['message' => 'Pengeluaran berhasil dicatat'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function show($id)
    {
        $data = Pengeluaran::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($data);
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function() use ($id) {
                $pengeluaran = Pengeluaran::where('user_id', Auth::id())->findOrFail($id);

                // Kembalikan saldo (Tambah) karena transaksi dibatalkan
                Rekening::where('id', $pengeluaran->rekening_id)
                    ->increment('saldo', $pengeluaran->jumlah);

                $pengeluaran->delete();
            });
            return response()->json(['message' => 'Data dihapus dan saldo dikembalikan']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghapus'], 500);
        }
    }
}
