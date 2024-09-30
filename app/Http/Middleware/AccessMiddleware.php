<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $userRoleName = auth()->user()->role->name;
        foreach ($roles as  $role) {
            if ($role === $userRoleName) {
                return $next($request);
            }
        }
        abort(403, 'Unauthorized action.');    }
}
