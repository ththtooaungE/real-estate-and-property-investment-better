<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrAgent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->user()->is_admin || !(auth()->user()->is_agent && auth()->user()->status === "accepted")) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Must Be Admin or Agent!'
            ], 403);
        }
        return $next($request);
    }
}
