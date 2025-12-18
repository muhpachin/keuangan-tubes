<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-wrapper { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; max-width: 900px; width: 100%; }
        .login-image { background: linear-gradient(45deg, #28a745, #20c997); color: white; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .login-form { padding: 4rem; }
        @media (max-width: 768px) { .login-form { padding: 2rem; } }
    </style>
</head>
<body>
    <div class="container p-3">
        <div class="login-wrapper row g-0">
            <div class="col-lg-6 d-none d-lg-block login-image">
                <div class="text-center">
                    <h1>Mulai Sekarang</h1>
                    <p>Kelola keuangan Anda dengan lebih baik.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login-form">
                    <h3 class="mb-4">Buat Akun</h3>
                    
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label>Pertanyaan Keamanan</label>
                            <select name="security_question" class="form-select">
                                <option>Siapa nama hewan peliharaan pertama Anda?</option>
                                <option>Apa nama jalan tempat Anda tinggal saat kecil?</option>
                                <option>Siapa nama guru favorit Anda?</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Jawaban</label>
                            <input type="text" name="security_answer" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mb-3">Daftar</button>
                    </form>

                    <div class="text-center">
                        <p class="mt-3">Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>