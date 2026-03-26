<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;

class LogApiActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Capture activity after request is handled
        try {
            ActivityLog::create([
                'user_id' => optional($request->user())->id,
                'route' => $request->path(),
                'method' => $request->method(),
                'action' => $this->getActionDescription($request),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => $this->getCleanPayload($request),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log API activity: ' . $e->getMessage());
        }

        return $response;
    }

    /**
     * Get a human-readable description of the action.
     */
    protected function getActionDescription(Request $request)
    {
        $path = strtolower($request->path());
        
        if (str_contains($path, 'login')) return 'System Login';
        if (str_contains($path, 'register')) return 'Account Registration';
        if (str_contains($path, 'profile/update') || str_contains($path, 'user/update')) return 'Profile Updated';
        if (str_contains($path, 'logout')) return 'System Logout';
        if (str_contains($path, 'password')) return 'Password Changed';
        if (str_contains($path, 'toggle-status')) return 'Status Toggled';
        if (str_contains($path, 'approve') || str_contains($path, 'verify')) return 'Account Verified';
        
        // Fallback: use the last segment of the path
        $segments = explode('/', $path);
        return strtoupper(str_replace(['-', '_'], ' ', end($segments)));
    }

    /**
     * Get a cleaned version of the input payload (stripping passwords).
     */
    protected function getCleanPayload(Request $request)
    {
        $payload = $request->except(['password', 'password_confirmation']);
        return count($payload) > 0 ? $payload : null;
    }
}
