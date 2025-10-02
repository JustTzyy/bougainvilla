<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

trait EnhancedLoggingTrait
{
    /**
     * Log user action with context
     */
    protected function logUserAction($action, $context = [], $level = 'info')
    {
        $logData = [
            'action' => $action,
            'user_id' => Auth::id(),
            'user_name' => Auth::user() ? Auth::user()->name : 'Guest',
            'timestamp' => now(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'context' => $context
        ];

        \Log::$level('User Action: ' . $action, $logData);
    }

    /**
     * Log database operation
     */
    protected function logDatabaseOperation($operation, $model, $data = [], $level = 'info')
    {
        $logData = [
            'operation' => $operation,
            'model' => $model,
            'user_id' => Auth::id(),
            'data' => $data,
            'timestamp' => now()
        ];

        \Log::$level('Database Operation: ' . $operation . ' on ' . $model, $logData);
    }

    /**
     * Log security event
     */
    protected function logSecurityEvent($event, $context = [], $level = 'warning')
    {
        $logData = [
            'security_event' => $event,
            'user_id' => Auth::id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now(),
            'context' => $context
        ];

        \Log::$level('Security Event: ' . $event, $logData);
    }

    /**
     * Log API request/response
     */
    protected function logApiCall($endpoint, $method, $requestData = [], $responseData = [], $statusCode = 200)
    {
        $logData = [
            'endpoint' => $endpoint,
            'method' => $method,
            'user_id' => Auth::id(),
            'request_data' => $this->sanitizeLogData($requestData),
            'response_data' => $this->sanitizeLogData($responseData),
            'status_code' => $statusCode,
            'ip' => request()->ip(),
            'timestamp' => now()
        ];

        $level = $statusCode >= 400 ? 'error' : 'info';
        \Log::$level('API Call: ' . $method . ' ' . $endpoint, $logData);
    }

    /**
     * Log business operation
     */
    protected function logBusinessOperation($operation, $details = [], $level = 'info')
    {
        $logData = [
            'business_operation' => $operation,
            'user_id' => Auth::id(),
            'details' => $details,
            'timestamp' => now()
        ];

        \Log::$level('Business Operation: ' . $operation, $logData);
    }

    /**
     * Sanitize sensitive data from logs
     */
    private function sanitizeLogData($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $sensitiveKeys = ['password', 'password_confirmation', 'current_password', 'token', 'api_key'];
        
        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = '[REDACTED]';
            }
        }

        return $data;
    }

    /**
     * Log performance metrics
     */
    protected function logPerformance($operation, $duration, $additionalMetrics = [])
    {
        $logData = [
            'operation' => $operation,
            'duration_ms' => $duration,
            'user_id' => Auth::id(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'timestamp' => now(),
            'additional_metrics' => $additionalMetrics
        ];

        $level = $duration > 5000 ? 'warning' : 'info'; // Warn if operation takes more than 5 seconds
        \Log::$level('Performance: ' . $operation, $logData);
    }
}
