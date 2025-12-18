<?php

namespace App\Http\Controllers;

use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekeningController extends Controller
{
    public function index()
    {
        $rekening = Rekening::where('user_id', Auth::id())->get();
        return view('rekening.index', compact('rekening'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_rekening' => 'required',
            'tipe' => 'required',
            'saldo' => 'required|numeric'
        ]);

        Rekening::create([
            'user_id' => Auth::id(),
            'nama_rekening' => $request->nama_rekening,
            'tipe' => $request->tipe,
            'saldo' => $request->saldo,
            'minimum_saldo' => $request->minimum_saldo ?? 0
        ]);

        return back()->with('success', 'Rekening berhasil dibuat.');
    }

    public function destroy($id)
    {
        Rekening::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Rekening dihapus.');
    }
}