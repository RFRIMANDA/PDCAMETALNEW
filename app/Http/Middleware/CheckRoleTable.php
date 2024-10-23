<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRoleTable
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Ambil user yang sedang login
        $user = auth()->user();

        // Ambil ID divisi yang diakses dari request, misalnya dari parameter URL atau input
        $divisiId = $request->route('divisi'); // Ambil dari route parameter

        // Cek apakah user memiliki akses ke divisi tersebut berdasarkan ID
        if (!$user->divisi->contains($divisiId)) {
            // Jika user tidak memiliki akses, kembalikan response unauthorized atau redirect
            return response()->json(['message' => 'Anda tidak memiliki akses untuk divisi ini.'], 403);
        }

        return $next($request);
    }

}
