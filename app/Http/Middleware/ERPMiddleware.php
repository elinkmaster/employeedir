<?php

namespace App\Http\Middleware;

use Closure;

class ERPMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->usertype == 2) {
            // route to not an admin page
            return redirect()->route('admin-invalid');
        }
        return $next($request);
    }
}
