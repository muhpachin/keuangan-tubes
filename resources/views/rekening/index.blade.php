@extends('layouts.app')

@section('title', 'Rekening')
@section('header_title', 'Rekening')

@section('content')
<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card p-4">
            <h4>Tambah/Edit Rekening</h4>
            <form method="POST" action="{{ route('rekening.store') }}">
                @csrf
                <div class="mb-3">
                    <label>Nama Rekening</label>
                    <input type="text" name="nama_rekening" class="form-control" required placeholder="Contoh: BCA, Dompet, Gopay">
                </div>
                <div class="mb-3">
                    <label>Tipe</label>
                    <select name="tipe" class="form-select" required>
                        <option value="BANK">Bank / E-Wallet</option>
                        <option value="TUNAI">Tunai / Dompet Fisik</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Saldo Awal (Rp)</label>
                    <input type="number" name="saldo" class="form-control" value="0" required>
                </div>
                <div class="mb-3">
                    <label>Saldo Minimum (Tidak bisa dipakai)</label>
                    <input type="number" name="minimum_saldo" class="form-control" value="0">
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card p-4">
            <h4>Daftar Rekening</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>Saldo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekening as $rek)
                        <tr>
                            <td>{{ $rek->nama_rekening }}</td>
                            <td>
                                <span class="badge bg-{{ $rek->tipe == 'TUNAI' ? 'info' : 'secondary' }}">
                                    {{ $rek->tipe }}
                                </span>
                            </td>
                            <td>Rp {{ number_format($rek->saldo, 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('rekening.destroy', $rek->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus? Data tidak bisa dikembalikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection