@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3>Manajemen User</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Tanggal Bergabung</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $u)
                <tr>
                    <td><a href="{{ route('admin.users.show', $u->id) }}">{{ $u->username }}</a></td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->created_at ? $u->created_at->format('Y-m-d') : '-' }}</td>
                    <td>{{ $u->is_banned ? 'Diblokir' : 'Aktif' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.users.ban', $u->id) }}" style="display:inline-block">
                            @csrf
                            <button class="btn btn-sm btn-{{ $u->is_banned ? 'success' : 'danger' }}">{{ $u->is_banned ? 'Buka' : 'Blokir' }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.users.reset_password', $u->id) }}" style="display:inline-block">
                            @csrf
                            <button class="btn btn-sm btn-warning">Reset Password</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links() }}
</div>
@endsection