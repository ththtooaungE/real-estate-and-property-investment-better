<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminOrSelfUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->route('user');
        if (auth()->user()->id !== $user->id || auth()->user()->is_admin) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Must Be Admin or Self user!'
            ], 403);
        }
        return $next($request);
    }
}
