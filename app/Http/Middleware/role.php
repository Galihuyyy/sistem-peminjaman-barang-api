<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, String $role): Response
    {
        $user = auth()->user();
        if ($user->role !== $role) {
            return response()->json([
                'message' => 'Forbidden, you are not ' . $role
            ], 403);
        }

        return $next($request);
    }
}
