<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next)
    {
        if(Auth::user()->role_id != 1){
            $response = [
                'message' => 'Not Found',
                'status_code' => Response::HTTP_NOT_FOUND
            ];
            // return false;
            return response()->json($response, Response::HTTP_NOT_FOUND);
            
        }
        return $next($request);
    }
}