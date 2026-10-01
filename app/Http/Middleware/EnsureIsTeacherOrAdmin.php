<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsTeacherOrAdmin
{
    /**
     * Handle an incoming request.
     * Only allow users with the 'teacher' or 'admin' role.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || (!$request->user()->isTeacher() && !$request->user()->isAdmin())) {
            abort(403, 'Unauthorized. Teacher or Admin access required.');
        }

        return $next($request);
    }
}
