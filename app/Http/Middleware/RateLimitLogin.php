<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RateLimitLogin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply rate limiting to login POST requests
        if ($request->isMethod('POST') && $request->routeIs('login.post')) {
            $ip = $request->ip();
            $key = 'login_attempts:' . $ip;
            $maxAttempts = 5; // Maximum attempts per IP
            $decayMinutes = 15; // Reset attempts after 15 minutes
            
            // Get current attempt count
            $attempts = Cache::get($key, 0);
            
            if ($attempts >= $maxAttempts) {
                Log::warning('Login rate limit exceeded', [
                    'ip' => $ip,
                    'attempts' => $attempts,
                    'user_agent' => $request->userAgent()
                ]);
                
                return redirect()->route('login')
                    ->withErrors(['email' => 'Too many login attempts. Please try again in ' . $decayMinutes . ' minutes.'])
                    ->withInput($request->only('email'));
            }
            
            // Increment attempt counter
            Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        }
        
        $response = $next($request);
        
        // If login was successful (redirect to dashboard), reset the rate limit
        if ($request->isMethod('POST') && $request->routeIs('login.post')) {
            if ($response->isRedirect() && 
                (str_contains($response->getTargetUrl(), 'adminPages/dashboard') || 
                 str_contains($response->getTargetUrl(), 'frontdesk/dashboard'))) {
                
                $ip = $request->ip();
                $key = 'login_attempts:' . $ip;
                Cache::forget($key); // Reset attempts on successful login
            }
        }
        
        return $response;
    }
}