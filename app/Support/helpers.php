<?php

/**
 * Standardized API response helpers. Used by controllers so success/error shape is consistent.
 */

if (! function_exists('api_success')) {
    /**
     * Success response. If $message is empty, body is $data as-is; otherwise { data, message }. Status = $code.
     */
    function api_success(mixed $data, string $message = '', int $code = 200): \Illuminate\Http\JsonResponse
    {
        $body = $message ? ['data' => $data, 'message' => $message] : $data;
        return response()->json($body, $code);
    }
}

if (! function_exists('api_error')) {
    /**
     * Error response: JSON { message: $message } with status $code (e.g. 400, 403).
     */
    function api_error(string $message, int $code = 400): \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => $message], $code);
    }
}
