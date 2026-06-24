<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class cekOrangTua
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // cek apakah sudah login
        if (!session()->has('role')) {
            return redirect('/login');
        }

        // cek role orang tua
        if (session('role') != 'orangtua') {
            return redirect('/login');
        }

        return $next($request);
    }
}
