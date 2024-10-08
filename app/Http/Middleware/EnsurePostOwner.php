<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePostOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $post = $request->route('post');
        if ($post->user_id !== auth()->user()->id) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Must Be Post Owner!'
            ], 403);
        }
        return $next($request);
    }
}
