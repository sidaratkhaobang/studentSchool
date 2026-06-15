<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isStudent()) {
            return response()->json(['message' => 'ไม่มีสิทธิ์เข้าถึง (Student only)'], 403);
        }

        return $next($request);
    }
}
