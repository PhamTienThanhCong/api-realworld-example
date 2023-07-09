<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AccessibleMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');

        // remove Bearer
        $token = str_replace('Bearer ', '', $token);
        // remove Token 
        $token = str_replace('Token ', '', $token);

        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
    
            if ($accessToken) {
                $user = $accessToken->tokenable;
    
                if ($user) {
                    $request->merge(['user' => $user]);
                }
            }
        }
        return $next($request);
    }
}
