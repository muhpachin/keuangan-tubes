@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Admin Dashboard</h2>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">Manajemen User</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card p-3">
                <h5>Total User</h5>
                <h2>{{ $totalUsers }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h5>User Aktif (Hari ini)</h5>
                <h2>{{ $activeToday->count() }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h5>User Aktif (Bulan ini)</h5>
                <h2>{{ $activeThisMonth->count() }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h5>Total Transaksi</h5>
                <h2>{{ $totalTransactions }}</h2>
            </div>
        </div>
    </div>

    <div class="card mt-4 p-4">
        <h5>Pertumbuhan User (12 bulan terakhir)</h5>
        <canvas id="userChart" height="80"></canvas>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('userChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($labels) !!},
        datasets: [{
            label: 'Pendaftar baru',
            data: {!! json_encode($data) !!},
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13,110,253,0.1)'
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
@endsection