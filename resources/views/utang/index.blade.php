@extends('layouts.app')

@section('title', 'Utang')
@section('header_title', 'Manajemen Utang')

@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card p-4">
            <h5 class="mb-3">Catat Utang Baru</h5>
            <form method="POST" action="{{ route('utang.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Keterangan / Kepada Siapa</label>
                    <input type="text" name="deskripsi" class="form-control" required placeholder="Contoh: Pinjam ke Budi">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Jumlah (Rp)</label>
                    <input type="number" name="jumlah" class="form-control" required placeholder="0">
                </div>

                <div class="mb-3">
                    <label class="form-label">Jatuh Tempo (Opsional)</label>
                    <input type="date" name="jatuh_tempo" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary w-100">Simpan</button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card p-4">
            <h5 class="mb-3">Daftar Utang</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($utang as $item)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $item->deskripsi }}</div>
                                <small class="text-muted">Dicatat: {{ \Carbon\Carbon::parse($item->created_at ?? now())->format('d M Y') }}</small>
                            </td>
                            <td class="fw-bold">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td>
                                @if($item->status == 'Lunas')
                                    <span class="badge bg-success">Lunas</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Lunas</span>
                                @endif
                            </td>
                            <td>
                                {{ $item->jatuh_tempo ? \Carbon\Carbon::parse($item->jatuh_tempo)->format('d M Y') : '-' }}
                            </td>
                            <td class="text-center">
                                @if($item->status != 'Lunas')
                                    <button class="btn btn-sm btn-success mb-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#bayarModal" 
                                        onclick="isiModalBayar({{ $item->id }}, '{{ $item->deskripsi }}', {{ $item->jumlah }})">
                                        Bayar
                                    </button>
                                @endif
                                
                                <form action="{{ route('utang.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger mb-1">&times;</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada data utang.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bayar Utang -->
<div class="modal fade" id="bayarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('utang.bayar') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bayar Utang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_utang" id="modalIdUtang">
                    <input type="hidden" name="deskripsi_utang" id="modalDeskripsiUtang">
                    
                    <div class="mb-3">
                        <label>Bayar Utang: <strong id="labelDeskripsi"></strong></label>
                    </div>
                    
                    <div class="mb-3">
                        <label>Jumlah Bayar</label>
                        <input type="number" name="jumlah_bayar" id="modalJumlah" class="form-control" readonly required>
                    </div>

                    <div class="mb-3">
                        <label>Sumber Dana</label>
                        <select name="rekening_id" class="form-select" required>
                            @foreach($rekening as $rek)
                                <option value="{{ $rek->id }}">{{ $rek->nama_rekening }} (Rp {{ number_format($rek->saldo, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Bayar Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function isiModalBayar(id, deskripsi, jumlah) {
        document.getElementById('modalIdUtang').value = id;
        document.getElementById('modalDeskripsiUtang').value = 'Bayar Utang: ' + deskripsi;
        document.getElementById('modalJumlah').value = jumlah;
        document.getElementById('labelDeskripsi').innerText = deskripsi;
    }
</script>
@endsection
