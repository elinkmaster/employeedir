<?php
namespace App\Http\Middleware;
use Closure;
use Auth;
class AdminMiddleware
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
        if (!Auth::check() || Auth::user()->is_admin == 0) {
        	// route to not an admin page
            return redirect()->route('403');
        }
        return $next($request);
    }
}