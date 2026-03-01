<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAppSecret
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = env('APP_SECRET');

        // Check if APP_SECRET is set and provided correctly
        if (empty($secret) || $request->header('X-App-Secret') !== $secret) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized App Client'
            ], 401);
        }

        return $next($request);
    }
}
