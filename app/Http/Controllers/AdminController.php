<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // Total users
        $totalUsers = User::count();

        // Active users
        $activeToday = User::whereDate('last_login_at', now()->toDateString())->get();
        $activeThisMonth = User::whereBetween('last_login_at', [now()->startOfMonth(), now()->endOfMonth()])->get();

        // Total transactions across several tables
        $tables = ['pemasukan', 'pengeluaran', 'transfer', 'utang', 'piutang'];
        $totalTransactions = 0;
        foreach ($tables as $t) {
            if (DB::getSchemaBuilder()->hasTable($t)) {
                $totalTransactions += DB::table($t)->count();
            }
        }

        // Growth: new users per month for last 12 months
        $usersPerMonth = DB::table('users')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
            ->whereNotNull('created_at')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        // ensure months exist in series
        $labels = [];
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i)->format('Y-m');
            $labels[] = $m;
            $data[] = $usersPerMonth->get($m, 0);
        }

        return view('admin.dashboard', compact('totalUsers', 'activeToday', 'activeThisMonth', 'totalTransactions', 'labels', 'data'));
    }

    public function usersIndex()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(25);
        return view('admin.users.index', compact('users'));
    }

    public function usersShow(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function ban(Request $request, User $user)
    {
        $user->is_banned = !$user->is_banned;
        $user->save();
        return back()->with('success', $user->is_banned ? 'User diblokir.' : 'User dibuka blokir.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $temp = 'Reset1234';
        $user->password = \Illuminate\Support\Facades\Hash::make($temp);
        $user->save();
        // Optionally: mail user the temporary password or log activity
        return back()->with('success', 'Password direset ke: ' . $temp);
    }
}
