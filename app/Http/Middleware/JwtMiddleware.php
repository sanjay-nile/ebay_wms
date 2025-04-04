<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
// use Tymon\JWTAuth\Middleware\BaseMiddleware;

class JwtMiddleware
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
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['msg'=>'user_not_found','status'=>false,'data'=>[]], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['msg'=>'token_expired','status'=>false,'data'=>[]], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['msg'=>'token_invalid','status'=>false,'data'=>[]], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['msg'=>'token_absent','status'=>false,'data'=>[]], $e->getStatusCode());

        } 

          // the token is valid and we have exposed the contents
          
          return $next($request);
    }
}
