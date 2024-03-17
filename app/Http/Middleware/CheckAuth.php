<?php

namespace App\Http\Middleware;

use Closure;
use App\Facades\Utils;
use App\Enums\StatusCode;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->header('Authorization')) {

            return Utils::setResponse(
                StatusCode::UNAUTHORIZED,
                null,
                'Unauthorized Request'
            );
        }
        
        try {
            $user = JWTAuth::parseToken()->authenticate();

        } catch (TokenExpiredException $e) {
            return Utils::setResponse(
                StatusCode::UNAUTHORIZED,
                null,
                'Token Expired'
            );
        } catch (TokenInvalidException $e) {
            return Utils::setResponse(
                StatusCode::UNAUTHORIZED,
                null,
                'Token Invalid'
            );
        } catch (\Exception $e) {
            // Other exceptions
            return Utils::setResponse(
                StatusCode::UNAUTHORIZED,
                null,
                'Invalid or Expired Token'
            );
        }



        return $next($request);
    }
}
