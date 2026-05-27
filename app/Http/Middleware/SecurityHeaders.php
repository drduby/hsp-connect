<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        header_remove('X-Powered-By');

        $response->headers->remove('X-Powered-By');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        $viteSources = $this->viteDevelopmentSources();
        $viteConnectSources = $this->viteDevelopmentConnectSources($viteSources);

        return implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            $this->directive('connect-src', ["'self'", ...$viteConnectSources]),
            "font-src 'self' https://fonts.gstatic.com data:",
            "form-action 'self'",
            "frame-ancestors 'self'",
            $this->directive('img-src', ["'self'", 'data:', ...$viteSources]),
            "object-src 'none'",
            $this->directive('script-src', ["'self'", "'unsafe-inline'", "'unsafe-eval'", ...$viteSources]),
            $this->directive('style-src', ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com', ...$viteSources]),
        ]);
    }

    /**
     * @param  array<int, string>  $sources
     */
    private function directive(string $name, array $sources): string
    {
        return $name.' '.implode(' ', array_values(array_unique($sources)));
    }

    /**
     * @return array<int, string>
     */
    private function viteDevelopmentSources(): array
    {
        if (app()->isProduction() || ! is_file(public_path('hot'))) {
            return [];
        }

        $hotUrl = trim((string) file_get_contents(public_path('hot')));

        if ($hotUrl === '') {
            return [];
        }

        $origin = parse_url($hotUrl, PHP_URL_SCHEME).'://'.parse_url($hotUrl, PHP_URL_HOST);
        $port = parse_url($hotUrl, PHP_URL_PORT);

        return [$port ? "{$origin}:{$port}" : $origin];
    }

    /**
     * @param  array<int, string>  $sources
     * @return array<int, string>
     */
    private function viteDevelopmentConnectSources(array $sources): array
    {
        return collect($sources)
            ->flatMap(fn (string $source): array => [
                $source,
                str_replace('http://', 'ws://', str_replace('https://', 'wss://', $source)),
            ])
            ->values()
            ->all();
    }
}
