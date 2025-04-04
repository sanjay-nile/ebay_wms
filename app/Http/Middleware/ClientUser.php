<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
class ClientUser
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
        if (Auth::check() && Auth::user()->user_type_id == 4) {
            return $next($request);
        }

        return redirect('/')->with('error','You have not Client user access');
    }
}
