@extends('layouts.app')

@section('title', 'Utang')
@section('header_title', 'Manajemen Utang')

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card p-4 shadow-sm border-0">
            <h4 class="mb-3"><i class="bi bi-plus-circle"></i> Catat Utang Baru</h4>
            <form method="POST" action="{{ route('utang.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Deskripsi (Ke Siapa & Untuk Apa)</label>
                    <input type="text" name="deskripsi" class="form-control" required placeholder="Contoh: Pinjam ke Budi (Makan)">
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah (Rp)</label>
                    <input type="number" name="jumlah" class="form-control" required placeholder="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Jatuh Tempo</label>
                    <input type="date" name="jatuh_tempo" class="form-control">
                </div>
                <button type="submit" class="btn btn-danger w-100">Simpan Catatan Utang</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card p-4 shadow-sm border-0">
            <h4 class="mb-3">Daftar Utang</h4>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Deskripsi</th>
                            <th>Sisa / Total</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($utang as $u)
                        <tr class="{{ $u->status == 'Lunas' ? 'table-light text-muted' : '' }}">
                            <td>
                                <div class="fw-bold">{{ $u->deskripsi }}</div>
                                @if($u->status == 'Lunas')
                                    <small class="text-success"><i class="bi bi-check-all"></i> Lunas</small>
                                @endif
                            </td>
                            <td>
                                @if($u->status == 'Lunas')
                                    <span class="text-decoration-line-through">Rp {{ number_format($u->jumlah, 0, ',', '.') }}</span>
                                @else
                                    <div class="text-danger fw-bold">Sisa: Rp {{ number_format($u->sisa_jumlah, 0, ',', '.') }}</div>
                                    <small class="text-muted">Total: Rp {{ number_format($u->jumlah, 0, ',', '.') }}</small>
                                @endif
                            </td>
                            <td>
                                @if($u->jatuh_tempo)
                                    {{ \Carbon\Carbon::parse($u->jatuh_tempo)->format('d M Y') }}
                                    @if(\Carbon\Carbon::parse($u->jatuh_tempo)->isPast() && $u->status != 'Lunas')
                                        <span class="badge bg-danger">Lewat</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($u->status == 'Lunas')
                                    <span class="badge bg-success">Lunas</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Lunas</span>
                                @endif
                            </td>
                            <td>
                                @if($u->status != 'Lunas')
                                    <button class="btn btn-sm btn-success me-1" data-bs-toggle="modal" data-bs-target="#bayarModal-{{ $u->id }}">
                                        Bayar
                                    </button>
                                    <button class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#editModal" onclick="isiModalEdit({{ $u->id }}, '{{ $u->deskripsi }}', {{ $u->jumlah }}, '{{ $u->jatuh_tempo }}')">
                                        Edit
                                    </button>
                                @endif
                                
                                <form action="{{ route('utang.destroy', $u->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini? History pembayaran tidak akan hilang, tapi data utang ini akan dihapus.')">Hapus</button>
                                </form>
                            </td>
                        </tr>

                        @if($u->status != 'Lunas')
                        <div class="modal fade" id="bayarModal-{{ $u->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('utang.bayar') }}">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Bayar / Cicil Utang</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id_utang" value="{{ $u->id }}">
                                            
                                            <div class="alert alert-warning">
                                                <div class="d-flex justify-content-between">
                                                    <span>Total Hutang Awal:</span>
                                                    <strong>Rp {{ number_format($u->jumlah, 0, ',', '.') }}</strong>
                                                </div>
                                                <hr class="my-1">
                                                <div class="d-flex justify-content-between text-danger">
                                                    <span>Sisa Yang Harus Dibayar:</span>
                                                    <strong>Rp {{ number_format($u->sisa_jumlah, 0, ',', '.') }}</strong>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Jumlah Bayar Sekarang</label>
                                                <input type="number" 
                                                       name="jumlah_bayar" 
                                                       class="form-control input-bayar" 
                                                       value="{{ $u->sisa_jumlah }}" 
                                                       max="{{ $u->sisa_jumlah }}" 
                                                       min="1" 
                                                       required>
                                                <small class="text-muted">Anda bisa mengubah nominal ini untuk mencicil.</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Ambil dari Rekening</label>
                                                <select name="rekening_id" class="form-select" required>
                                                    @foreach($rekening as $rek)
                                                        <option value="{{ $rek->id }}">{{ $rek->nama_rekening }} (Saldo: Rp {{ number_format($rek->saldo, 0, ',', '.') }})</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Catatan (Masuk ke Riwayat Pengeluaran)</label>
                                                <input type="text" name="deskripsi_utang" class="form-control" value="Bayar Utang: {{ $u->deskripsi }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Konfirmasi Pembayaran</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data utang. Bagus!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Utang -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Utang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Keterangan / Kepada Siapa</label>
                        <input type="text" name="deskripsi" id="editDeskripsi" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label>Jumlah (Rp)</label>
                        <input type="number" name="jumlah" id="editJumlah" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Jatuh Tempo (Opsional)</label>
                        <input type="date" name="jatuh_tempo" id="editJatuhTempo" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function isiModalEdit(id, deskripsi, jumlah, jatuhTempo) {
        document.getElementById('editForm').action = '/utang/' + id;
        document.getElementById('editDeskripsi').value = deskripsi;
        document.getElementById('editJumlah').value = jumlah;
        document.getElementById('editJatuhTempo').value = jatuhTempo;
    }
</script>
@endsection