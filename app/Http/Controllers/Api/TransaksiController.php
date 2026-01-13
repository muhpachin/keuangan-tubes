<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransaksiController extends Controller
{
    /**
     * Menampilkan daftar semua transaksi.
     */
    public function index()
    {
        $transaksi = Transaksi::orderBy('tanggal', 'desc')->get();
        return response()->json($transaksi);
    }

    /**
     * Menyimpan transaksi baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'deskripsi' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'tanggal' => 'required|date',
        ]);

        $transaksi = Transaksi::create($validated);

        return response()->json($transaksi, Response::HTTP_CREATED);
    }

    /**
     * Menampilkan satu transaksi spesifik.
     */
    public function show(Transaksi $transaksi)
    {
        return response()->json($transaksi);
    }

    /**
     * Memperbarui transaksi yang ada.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        $validated = $request->validate([
            'deskripsi' => 'sometimes|required|string|max:255',
            'jumlah' => 'sometimes|required|numeric|min:0',
            'jenis' => 'sometimes|required|in:pemasukan,pengeluaran',
            'tanggal' => 'sometimes|required|date',
        ]);

        $transaksi->update($validated);

        return response()->json($transaksi);
    }

    /**
     * Menghapus transaksi.
     */
    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
