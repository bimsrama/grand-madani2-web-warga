<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResidentAuthenticated
{
    /**
     * Protects resident routes by checking the server-side session.
     * Redirects to the resident login page if no valid session exists.
     * Stores the intended URL so we can redirect back after login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('resident_id') || !session()->has('resident_rt')) {
            // Remember where they were going
            session(['url.intended' => $request->url()]);

            return redirect()
                ->route('resident.login')
                ->with('info', 'Silahkan login terlebih dahulu untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
