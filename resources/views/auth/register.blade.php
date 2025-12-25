<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .register-wrapper { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; max-width: 900px; width: 100%; }
        .register-image { background: linear-gradient(45deg, #28a745, #20c997); color: white; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .register-form { padding: 3rem; }
        @media (max-width: 768px) { .register-form { padding: 2rem; } }
    </style>
</head>
<body>
    <div class="container p-3">
        <div class="register-wrapper row g-0">
            <div class="col-lg-6 d-none d-lg-block register-image">
                <div class="text-center">
                    <h1>Mulai Sekarang</h1>
                    <p>Buat akun dan kelola keuanganmu dengan lebih baik.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="register-form">
                    <div class="d-flex align-items-center mb-4">
                        <img src="{{ asset('logo1.svg') }}" alt="Logo" style="width:48px; height:auto; margin-right:12px;">
                        <h3 class="mb-0">Buat Akun Baru</h3>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required value="{{ old('username') }}">
                        </div>
                        <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                        
                        <div class="mb-3">
                            <label>Pertanyaan Keamanan</label>
                            <select name="security_question" class="form-select" required>
                                <option value="" disabled selected>Pilih pertanyaan...</option>
                                <option value="Siapa nama hewan peliharaan pertama Anda?">Siapa nama hewan peliharaan pertama Anda?</option>
                                <option value="Apa nama jalan tempat Anda tinggal saat kecil?">Apa nama jalan tempat Anda tinggal saat kecil?</option>
                                <option value="Siapa nama guru favorit Anda?">Siapa nama guru favorit Anda?</option>
                                <option value="Di kota mana Anda lahir?">Di kota mana Anda lahir?</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Jawaban</label>
                            <input type="text" name="security_answer" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mb-3">Daftar</button>
                    </form>

                    <div class="text-center">
                        <p class="mt-3">Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>