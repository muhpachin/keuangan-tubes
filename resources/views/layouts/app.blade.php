<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Keuangan')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f8f9fa; }
        .body-no-scroll { overflow: hidden; }
        .sidebar { width: 250px; height: 100vh; position: fixed; left: 0; top: 0; background: linear-gradient(to bottom, #007bff, #0056b3); color: white; padding-top: 20px; transition: transform 0.3s ease-in-out; z-index: 1051; display: flex; flex-direction: column; }
        .sidebar-menu { flex-grow: 1; overflow-y: auto; }
        .sidebar a { display: block; padding: 12px 20px; color: white; text-decoration: none; transition: background-color 0.2s; }
        .sidebar a.active, .sidebar a:hover { background: rgba(255, 255, 255, 0.2); }
        .main-content { transition: margin-left 0.3s ease-in-out; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .sidebar-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1050; display: none; }
        .sidebar-overlay.active { display: block; }
        
        /* Animasi Loader */
        .loader-wrapper { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(255, 255, 255, 0.8); backdrop-filter: blur(5px); display: none; align-items: center; justify-content: center; z-index: 2000; opacity: 0; transition: opacity 0.3s ease-in-out; }
        .loader-wrapper.active { display: flex; opacity: 1; }
        .loader { border: 8px solid #f3f3f3; border-radius: 50%; border-top: 8px solid #007bff; width: 60px; height: 60px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        @media (min-width: 992px) { .main-content { margin-left: 250px; } }
        @media (max-width: 991.98px) { .sidebar { transform: translateX(-100%); } .sidebar.active { transform: translateX(0); } .main-content { margin-left: 0; } }
    </style>
</head>
<body>
    <div class="loader-wrapper" id="loader">
        <div class="loader"></div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-menu">
            <div class="text-center mb-4">
                <img src="{{ asset('logo1.svg') }}" alt="Logo" style="width: 70px; height: auto; margin-bottom: 2px;">
                <h3>KEUANGAN</h3>
            </div>
            
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('rekening.index') }}" class="{{ request()->routeIs('rekening.*') ? 'active' : '' }}">Rekening</a>
            <a href="{{ route('pemasukan.index') }}" class="{{ request()->routeIs('pemasukan.*') ? 'active' : '' }}">Pemasukan</a>
            <a href="{{ route('pengeluaran.index') }}" class="{{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}">Pengeluaran</a>
            <a href="{{ route('transfer.index') }}" class="{{ request()->routeIs('transfer.*') ? 'active' : '' }}">Transfer</a>
            <a href="{{ route('utang.index') }}" class="{{ request()->routeIs('utang.*') ? 'active' : '' }}">Utang</a>
            <a href="{{ route('laporan.index') }}" class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}">Laporan</a>
            @if(Auth::check() && Auth::user()->tipe_akun === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">Admin</a>
            @endif
            
            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                @csrf
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            </form>
        </div>
    </div>

    <div class="main-content p-3 p-md-4">
        <nav class="navbar navbar-light bg-light mb-4 d-lg-none">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" id="sidebarToggle">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <span class="navbar-brand mb-0 h1">@yield('header_title', 'Dashboard')</span>
            </div>
        </nav>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
            document.body.classList.toggle('body-no-scroll');
        }

        if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);

        // Simple Loader logic for links
        document.querySelectorAll('.sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                if(this.getAttribute('href') !== '#') {
                    document.getElementById('loader').classList.add('active');
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>