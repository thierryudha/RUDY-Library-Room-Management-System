<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponse;

class EnsureUserHasRole
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return $this->errorResponse('Unauthenticated.', 401);
        }

        // Assuming User model has a 'role' relationship that returns the Role model
        // which has a 'role' attribute (as defined in migrations).
        $userRole = $request->user()->role->name ?? null;

        if (! in_array($userRole, $roles)) {
            return $this->errorResponse('Forbidden: You do not have the required role to access this resource.', 403);
        }

        return $next($request);
    }
}
