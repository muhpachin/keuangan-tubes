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

    public function usersIndex(Request $request)
    {
        $query = User::query();

        // search by username or email
        if ($q = $request->query('q')) {
            $query->where(function($q2) use ($q) {
                $q2->where('username', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // filter by status
        if ($status = $request->query('status')) {
            if ($status === 'banned') {
                $query->where('is_banned', 1);
            } elseif ($status === 'active') {
                $query->where(function($q3) {
                    $q3->where('is_banned', 0)->orWhereNull('is_banned');
                });
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

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

        // Log activity
        if (class_exists(\App\Helpers\ActivityLogger::class)) {
            \App\Helpers\ActivityLogger::log(auth()->id() ?? null, $user->is_banned ? 'user.ban' : 'user.unban', $user, 'Admin toggled ban status for user');
        }

        return back()->with('success', $user->is_banned ? 'User diblokir.' : 'User dibuka blokir.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $temp = 'Reset1234';
        $user->password = \Illuminate\Support\Facades\Hash::make($temp);
        $user->save();

        // Log activity
        if (class_exists(\App\Helpers\ActivityLogger::class)) {
            \App\Helpers\ActivityLogger::log(auth()->id() ?? null, 'user.reset_password', $user, 'Admin reset password for user');
        }

        // Optionally: mail user the temporary password or log activity
        return back()->with('success', 'Password direset ke: ' . $temp);
    }
}
