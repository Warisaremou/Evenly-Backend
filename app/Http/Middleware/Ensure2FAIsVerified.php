<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Ensure2FAIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && !$request->user()->two_factor_enabled) {
            return response()->json([
                'message' => 'Two-factor authentication is not enabled for this user.'
            ], Response::HTTP_FORBIDDEN);
        }
    }
}
