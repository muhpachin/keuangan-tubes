@extends('layouts.app')

@section('title', 'Pemasukan')
@section('header_title', 'Pemasukan')

@section('content')
<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card p-4">
            <h4>Tambah Pemasukan</h4>
            <form method="POST" action="{{ route('pemasukan.store') }}">
                @csrf
                <div class="mb-3">
                    <label>Kategori</label>
                    <select name="kategori" class="form-select">
                        @foreach($kategori as $kat)
                            <option value="{{ $kat->nama_kategori }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <input type="text" name="deskripsi" class="form-control">
                </div>
                <div class="mb-3">
                    <label>Jumlah (Rp)</label>
                    <input type="number" name="jumlah" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Masuk ke Rekening</label>
                    <select name="rekening_id" class="form-select">
                        @foreach($rekening as $rek)
                            <option value="{{ $rek->id }}">{{ $rek->nama_rekening }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card p-4">
            <div class="d-flex justify-content-between mb-3">
                <h4>Riwayat Pemasukan</h4>
                <div>
                    <a href="{{ route('pemasukan.index', ['filter' => 'harian']) }}" class="btn btn-sm btn-outline-primary">Hari Ini</a>
                    <a href="{{ route('pemasukan.index', ['filter' => 'mingguan']) }}" class="btn btn-sm btn-outline-primary">Minggu Ini</a>
                    <a href="{{ route('pemasukan.index', ['filter' => 'semua']) }}" class="btn btn-sm btn-outline-secondary">Semua</a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Ket</th>
                            <th>Jumlah</th>
                            <th>Rekening</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pemasukan as $item)
                        <tr>
                            <td>{{ $item->kategori }}</td>
                            <td>{{ $item->deskripsi }}</td>
                            <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td>{{ $item->rekening->nama_rekening ?? '-' }}</td>
                            <td>
                                <form action="{{ route('pemasukan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data ini? Saldo akan dikembalikan.')">
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