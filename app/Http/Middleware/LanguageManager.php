<?php

namespace App\Http\Middleware;

use Closure;
use Stichoza\GoogleTranslate\GoogleTranslate;

class LanguageManager
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
        if(session()->has('locale')){
            \App::setLocale(session()->get('locale'));
        }
        return $next($request);
    }
}
