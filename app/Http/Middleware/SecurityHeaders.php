<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request and attach cyber security defense headers.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Anti Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Anti MIME-type confusion / sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // XSS Filter defense for legacy browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer Privacy Protection
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Disable unneeded browser device hardware features
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Content Security frame policy (deny embedding in unauthorized external domains)
        if (! $response->headers->has('Content-Security-Policy')) {
            $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'; base-uri 'self'; object-src 'none';");
        }

        // HTTP Strict Transport Security (HSTS) when on HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
