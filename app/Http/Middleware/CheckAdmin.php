<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'manajemen'])) {
            return $next($request);
        }
        return redirect('login')->withErrors(['message' => 'Unauthorized']);
    }
}
