@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3>Manajemen User</h3>
    <p class="text-muted small">Gunakan kotak <strong>Cari</strong> untuk menemukan user (username atau email). Pilih <strong>Status</strong> atau <strong>Per halaman</strong> untuk menyesuaikan tampilan. Gunakan ikon di kolom <strong>Aksi</strong> untuk <em>Lihat</em>, <em>Blokir/Buka blokir</em>, atau <em>Reset password</em>.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3 align-items-center">
        <div class="col-md-4 col-sm-6">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari username atau email...">
            <small class="text-muted">Ketik sebagian username atau email lalu tekan Cari.</small>
        </div>
        <div class="col-md-2 col-sm-3">
            <select name="status" class="form-select">
                <option value="">Semua</option>
                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Aktif</option>
                <option value="banned" {{ request('status')=='banned' ? 'selected' : '' }}>Diblokir</option>
            </select>
        </div>
        <div class="col-auto">
            <select name="per" class="form-select">
                <option value="10" {{ request('per')==10 ? 'selected' : '' }}>10 / halaman</option>
                <option value="25" {{ request('per')==25 ? 'selected' : '' }}>25 / halaman</option>
                <option value="50" {{ request('per')==50 ? 'selected' : '' }}>50 / halaman</option>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Cari</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Tanggal Bergabung</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td><a href="{{ route('admin.users.show', $u->id) }}">{{ $u->username }}</a></td>
                        <td>{{ $u->email }}</td>
                        <td title="{{ $u->created_at ? \Carbon\Carbon::parse($u->created_at)->toDayDateTimeString() : '' }}">{{ $u->created_at ? \Carbon\Carbon::parse($u->created_at)->format('Y-m-d') : '-' }}</td>
                        <td>{{ $u->tipe_akun ?? '-' }}</td>
                        <td>
                            @if($u->is_banned)
                                <span class="badge bg-danger" title="Pengguna diblokir">Diblokir</span>
                            @else
                                <span class="badge bg-success" title="Pengguna aktif">Aktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.users.show', $u->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Lihat User" aria-label="Lihat user {{ $u->username }}"><i class="bi bi-eye" aria-hidden="true"></i> <span class="visually-hidden">Lihat</span></a>

                            <form id="ban-form-{{ $u->id }}" method="POST" action="{{ route('admin.users.ban', $u->id) }}" style="display:inline-block">
                                @csrf
                                <button type="button" class="btn btn-sm btn-{{ $u->is_banned ? 'success' : 'danger' }} btn-confirm-action" data-title="{{ $u->is_banned ? 'Buka Blokir User' : 'Blokir User' }}" data-message="Anda yakin ingin {{ $u->is_banned ? 'membuka blokir' : 'memblokir' }} user {{ $u->username }} ?" data-form="#ban-form-{{ $u->id }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $u->is_banned ? 'Buka blokir' : 'Blokir' }}" aria-label="{{ $u->is_banned ? 'Buka blokir user '.$u->username : 'Blokir user '.$u->username }}">
                                    <i class="bi {{ $u->is_banned ? 'bi-unlock' : 'bi-lock' }}" aria-hidden="true"></i> <span class="visually-hidden">{{ $u->is_banned ? 'Buka blokir' : 'Blokir' }}</span>
                                </button>
                            </form>

                            <form id="reset-form-{{ $u->id }}" method="POST" action="{{ route('admin.users.reset_password', $u->id) }}" style="display:inline-block">
                                @csrf
                                <button type="button" class="btn btn-sm btn-warning btn-confirm-action" data-title="Reset Password" data-message="Anda akan mereset password user {{ $u->username }} menjadi password sementara. Lanjutkan?" data-form="#reset-form-{{ $u->id }}" title="Reset Password" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Reset Password" aria-label="Reset password user {{ $u->username }}">
                                    <i class="bi bi-arrow-clockwise" aria-hidden="true"></i> <span class="visually-hidden">Reset</span>
                                </button>
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $users->links() }}

    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmModalTitle">Konfirmasi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="confirmModalBody">Apakah Anda yakin?</div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-primary" id="confirmModalConfirm">Ya, lanjutkan</button>
          </div>
        </div>
      </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            let targetFormSelector = null;

            // Initialize bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl)
            })

            document.querySelectorAll('.btn-confirm-action').forEach(btn => {
                btn.addEventListener('click', function(e){
                    targetFormSelector = this.getAttribute('data-form');
                    document.getElementById('confirmModalTitle').textContent = this.getAttribute('data-title') || 'Konfirmasi';
                    document.getElementById('confirmModalBody').textContent = this.getAttribute('data-message') || 'Anda yakin?';
                    var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
                    confirmModal.show();
                });
            });

            document.getElementById('confirmModalConfirm').addEventListener('click', function(){
                if (!targetFormSelector) return;
                var form = document.querySelector(targetFormSelector);
                if (form) form.submit();
            });
        });
    </script>
    @endpush
</div>
@endsection