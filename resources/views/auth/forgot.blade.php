@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Lupa Password</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label for="identifier" class="form-label">Username atau Email</label>
            <input id="identifier" name="identifier" class="form-control" value="{{ old('identifier') }}" required>
            @error('identifier')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-primary">Kirim Link / Token Reset</button>
    </form>

    <hr>
    <h5>Sudah punya token?</h5>
    <p class="text-muted">Jika Anda sudah menerima token (mis. dari email atau tampilkan token), masukkan di bawah untuk langsung membuka halaman reset.</p>
    <form id="use-token-form" onsubmit="event.preventDefault(); useToken();">
        <div class="mb-3">
            <label for="token_input" class="form-label">Token Reset</label>
            <input id="token_input" name="token" class="form-control" placeholder="Masukkan token di sini" required>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-secondary" type="submit">Gunakan Token</button>
            <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('token_input').value=''">Bersihkan</button>
        </div>
    </form>

    <script>
        function useToken() {
            const t = document.getElementById('token_input').value.trim();
            if (!t) return;
            const url = '{{ url('/password/reset') }}/' + encodeURIComponent(t);
            window.location.href = url;
        }
    </script>
</div>
@endsection
