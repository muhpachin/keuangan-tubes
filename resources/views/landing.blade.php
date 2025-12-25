<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan App - Kelola Uangmu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%);
            color: white;
            padding: 100px 0;
            border-bottom-right-radius: 50px;
            border-bottom-left-radius: 50px;
        }
        .hero-title { font-weight: 800; font-size: 3.5rem; }
        .feature-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            height: 100%;
        }
        .feature-card:hover { transform: translateY(-10px); }
        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 20px;
        }
        .cta-section { background-color: #f8f9fa; padding: 80px 0; }
        .navbar-brand { font-weight: 800; color: #0d6efd !important; }
        .btn-light-primary { 
            background-color: white; 
            color: #0d6efd; 
            font-weight: 600; 
            border: none;
            padding: 12px 30px;
        }
        .btn-light-primary:hover { background-color: #f0f0f0; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('logo1.svg') }}" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-2">
                KEUANGAN
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if (Route::has('login'))
                        @auth
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary rounded-pill">Dashboard</a>
                            </li>
                        @else
                            <li class="nav-item me-2">
                                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-4">Masuk</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4">Daftar</a>
                                </li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="hero-title mb-4">Kelola Keuangan Anda dengan Lebih Cerdas</h1>
                    <p class="lead mb-5 opacity-75">Catat pemasukan, atur pengeluaran, dan pantau arus kas Anda dalam satu aplikasi yang mudah digunakan.</p>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-light-primary btn-lg rounded-pill shadow">Buka Dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-light-primary btn-lg rounded-pill shadow">Mulai Sekarang Gratis</a>
                    @endauth
                </div>
            </div>
            <!-- Mockup Image Placeholder -->
            <div class="mt-5">
                <img src="https://placehold.co/800x400/2962ff/ffffff?text=Dashboard+Preview" alt="App Preview" class="img-fluid rounded-4 shadow-lg" style="border: 5px solid rgba(255,255,255,0.2);">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 mt-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Fitur Unggulan</h2>
                <p class="text-muted">Semua yang Anda butuhkan untuk mengatur dompet Anda.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card p-4 text-center">
                        <div class="feature-icon">💸</div>
                        <h5>Pencatatan Mudah</h5>
                        <p class="text-muted">Catat pemasukan dan pengeluaran harian Anda hanya dalam beberapa klik. Simpel dan cepat.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card p-4 text-center">
                        <div class="feature-icon">🏦</div>
                        <h5>Multi Rekening</h5>
                        <p class="text-muted">Kelola saldo dari berbagai sumber: Tunai, Bank, atau E-Wallet dalam satu tempat.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card p-4 text-center">
                        <div class="feature-icon">📊</div>
                        <h5>Laporan Ringkas</h5>
                        <p class="text-muted">Pantau kesehatan finansial Anda melalui dashboard yang informatif dan mudah dipahami.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container text-center">
            <p class="text-muted mb-0">&copy; {{ date('Y') }} Keuangan App. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
