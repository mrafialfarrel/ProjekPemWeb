<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGuestAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->cookie('sakku-role') !== 'authenticated') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Akses Terbatas. Anda menggunakan Guest Mode.'], 403);
            }
            return redirect()->back()->with('error', 'Akses Terbatas. Anda menggunakan Guest Mode.');
        }

        return $next($request);
    }
}
