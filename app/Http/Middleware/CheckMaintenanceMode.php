<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    public function handle($request, Closure $next)
    {
        $enabled = Setting::get('maintenance_mode', '0');
        if ($enabled == '1') {
            // allow admins
            if (Auth::check() && method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin()) {
                return $next($request);
            }

            // Allow access to admin URLs only for admins and the maintenance page itself
            if ($request->is('admin*')) {
                return $next($request);
            }

            $message = Setting::get('maintenance_message', 'Sistem sedang dalam perbaikan.');
            return response()->view('maintenance', ['message' => $message]);
        }

        return $next($request);
    }
}
