<!DOCTYPE html>
<html lang="id">
<head>
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-17526921276"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);} 
  gtag('js', new Date());
  gtag('config', 'AW-17526921276');
</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Aplikasi Keuangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
        }
        .main-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
            align-items: center;
            justify-content: center;
        }
        .register-wrapper {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        .register-image {
            background: linear-gradient(45deg, #28a745, #20c997);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .register-image h1 {
            font-size: 3.5rem;
            font-weight: 900;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
            margin-top: 250px;
        }
        .register-form {
            padding: 3.5rem;
        }
        .form-control { border-radius: 10px; padding: 12px 20px; border: 1px solid #e0e0e0; }
        .btn { border-radius: 10px; padding: 12px; font-weight: 600; }
        .btn-success { background-color: #28a745; border-color: #28a745; }
        .btn-danger { background-color: #DB4437; border-color: #DB4437; }
        .loader-wrapper {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255,255,255,0.8); backdrop-filter: blur(5px); display: none; align-items: center; justify-content: center; z-index: 2000; opacity: 0; transition: opacity 0.3s ease-in-out;
        }
        .loader-wrapper.active { display:flex; opacity:1; }
        .loader { border:8px solid #f3f3f3; border-radius:50%; border-top:8px solid #007bff; width:60px; height:60px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @media (max-width: 991.98px) {
            .register-form { padding: 2.5rem; }
            .register-wrapper { box-shadow: none; border-radius: 0; }
            .main-container { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="loader-wrapper" id="loader"><div class="loader"></div></div>
    <div class="main-container p-3 p-lg-4">
        <div class="register-wrapper row g-0">
            <div class="col-lg-6 d-none d-lg-block register-image">
                <h1 id="animated-text"></h1>
            </div>
            <div class="col-lg-6">
                <div class="register-form">
                    <div class="text-center mb-4">
                        <img src="{{ asset('logo1.svg') }}" alt="Logo Keuangan" style="width:100px; height:auto; margin-bottom:1rem;">
                        <h3>Buat Akun Baru</h3>
                        <p class="text-muted">Buat akun dan kelola keuanganmu dengan lebih baik.</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">{!! session('success') !!}</div>
                    @else

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required value="{{ old('username') }}">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>

                        <div class="mb-3">
                            <label for="security_question" class="form-label">Pertanyaan Keamanan</label>
                            <select name="security_question" id="security_question" class="form-select" required>
                                <option value="" disabled selected>Pilih pertanyaan...</option>
                                <option value="Siapa nama hewan peliharaan pertama Anda?">Siapa nama hewan peliharaan pertama Anda?</option>
                                <option value="Apa nama jalan tempat Anda tinggal saat kecil?">Apa nama jalan tempat Anda tinggal saat kecil?</option>
                                <option value="Siapa nama guru favorit Anda?">Siapa nama guru favorit Anda?</option>
                                <option value="Di kota mana Anda lahir?">Di kota mana Anda lahir?</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="security_answer" class="form-label">Jawaban</label>
                            <input type="text" class="form-control" id="security_answer" name="security_answer" required>
                        </div>

n                        <div class="d-grid">
                            <button type="submit" name="register" class="btn btn-primary">Register</button>
                        </div>
                    </form>


n                    @endif

                    <div class="text-center mt-4">
                        <small class="text-muted">Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('loader');
        const form = document.querySelector('form');

        if (form) {
            form.addEventListener('submit', function() {
                loader.classList.add('active');
            });
        }

n        const textElement = document.getElementById('animated-text');
        const phrases = [
            "Buat Akun Baru",
            "Kelola Keuanganmu",
            "Aman & Terpercaya",
            "Mulai Sekarang"
        ];
        let currentIndex = 0;

n        function changeText() {
            textElement.style.opacity = 0;
            setTimeout(() => {
                currentIndex = (currentIndex + 1) % phrases.length;
                textElement.textContent = phrases[currentIndex];
                textElement.style.opacity = 1;
            }, 800);
        }

n        textElement.textContent = phrases[currentIndex];
        textElement.style.opacity = 1;

        setInterval(changeText, 4000);
    });
    </script>
</body>
</html>