<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content Security Policy
        $csp = implode(' ', [
            "default-src 'self';",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'
                https://cdn.jsdelivr.net
                https://cdnjs.cloudflare.com
                https://challenges.cloudflare.com;",
            "style-src 'self' 'unsafe-inline'
                https://fonts.googleapis.com
                https://cdnjs.cloudflare.com;",
            "font-src 'self' data:
                https://fonts.gstatic.com
                https://cdnjs.cloudflare.com;",
            "img-src 'self' data: blob: https:;",
            "connect-src 'self'
                https://challenges.cloudflare.com;",
            "frame-src
                https://challenges.cloudflare.com;",
            "form-action 'self';",
            "frame-ancestors 'self';",
            "object-src 'none';",
            "base-uri 'self';",
        ]);

        $response->headers->set('Content-Security-Policy', preg_replace('/\s+/', ' ', $csp));

        return $response;
    }
}