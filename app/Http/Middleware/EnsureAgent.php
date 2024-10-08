<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAgent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user()->is_agent && auth()->user()->status == "accepted") {
            return response()->json([
                'status' => 'fail',
                'message' => 'Must Be Agent!'
            ], 403);
        }
        return $next($request);
    }
}
