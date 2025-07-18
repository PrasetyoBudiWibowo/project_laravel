<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfNotLoggedIn
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
        // Cek apakah session 'user_logged_in' ada dan true
        if (!session()->has('user_logged_in') || !session('user_logged_in')) {
            // Jika belum login, redirect ke route login
            return redirect()->route('login');
        }

        // Jika sudah login, teruskan request
        return $next($request);
    }
}