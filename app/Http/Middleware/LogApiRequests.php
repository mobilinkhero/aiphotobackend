<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiLog;
use Illuminate\Support\Facades\Log;

class LogApiRequests
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $startTime;

        try {
            // We don't log the actual binary image data to keep DB small, 
            // but we log all other fields/headers.
            $requestData = $request->except(['image', 'img']);

            ApiLog::create([
                'method' => $request->method(),
                'path' => $request->path(),
                'ip' => $request->ip(),
                'request_body' => json_encode($requestData),
                'response_body' => $response->getContent(),
                'status_code' => $response->getStatusCode(),
                'duration' => $duration,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to log API request: " . $e->getMessage());
        }

        return $response;
    }
}
