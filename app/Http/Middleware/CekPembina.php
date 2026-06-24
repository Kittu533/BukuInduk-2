<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekPembina
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('role')) {
            return redirect('/');
        }

        if (session('role') != 'guru' || !session('is_pembina')) {
            abort(403, 'Akses ditolak');
        }

        return $next($request);
    }
}