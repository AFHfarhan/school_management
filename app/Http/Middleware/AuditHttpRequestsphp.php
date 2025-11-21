<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditHttpRequestsphp
{
    protected $logger;

    public function __construct(LogService $logger)
    {
        $this->logger = $logger;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = round((microtime(true) - $start) * 1000, 2);

        $context = [
            'method' => $request->method(),
            'path' => $request->path(),
            'query' => $request->query(),
            'input' => array_slice($request->except(['password', 'password_confirmation', 'file']), 0, 50),
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'ip' => $request->ip(),
            'user_id' => optional($request->user())->id,
        ];

        $this->logger->info('http.request', $context);

        return $response;
    }
}
