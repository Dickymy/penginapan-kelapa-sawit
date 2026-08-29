<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof \Illuminate\Http\Response || $response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            // Apply 1-year cache to static image routes if served via Laravel
            if ($request->is('storage/*') || $request->is('images/*')) {
                $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            }
        }

        return $response;
    }
}
