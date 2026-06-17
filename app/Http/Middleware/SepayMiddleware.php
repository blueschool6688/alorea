<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SepayMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('Authorization');
        if (!$apiKey || !str_starts_with($apiKey, 'Apikey ')) {
            return response()->json([
                'message' => 'Invalid API key format'
            ], 401);
        }
        $key = substr($apiKey, 7);
        if ($key !== env('API_KEY_SEPAY')) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}
