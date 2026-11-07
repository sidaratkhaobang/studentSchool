<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isTeacher() || ! $request->user()->teacher) {
            return response()->json(['message' => 'ไม่มีสิทธิ์เข้าถึง (Teacher only)'], 403);
        }

        return $next($request);
    }
}
